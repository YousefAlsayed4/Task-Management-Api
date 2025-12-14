<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_user_id',
        'due_date',
        'status',

    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    // -------------------
    // Relationships
    // -------------------
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id');
    }

    public function dependentTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id');
    }


    // -------------------
    // SCOPES (Filtering)
    // -------------------
    public function scopeStatus($query, $status)
    {
        if ($status) $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        if ($userId) $query->where('assigned_user_id', $userId);
    }

    public function scopeDueBetween($query, $from, $to)
    {
        if ($from && $to) $query->whereBetween('due_date', [$from, $to]);
    }


    // -------------------
    // BUSINESS LOGIC
    // -------------------
    public function canBeCompleted()
    {
        return !$this->dependencies()->where('status', '!=', 'completed')->exists();
    }

   public function changeStatus(TaskStatus $status): void
    {
        if ($this->status === $status->value) {
            throw new \DomainException(
                "Task is already {$status->value}"
            );
        }

        match ($status) {
            TaskStatus::COMPLETED => $this->complete(),
            TaskStatus::CANCELED  => $this->cancel(),
            TaskStatus::PENDING   => $this->pending(),
            default => throw new \DomainException('Invalid status transition'),
        };
    }

    protected function complete(): void
    {
        if (! $this->canBeCompleted()) {
            throw new \DomainException('All dependencies must be completed first');
        }
        $this->update(['status' => TaskStatus::COMPLETED->value]);
    }
    protected function cancel(): void
    {
        $this->update(['status' => TaskStatus::CANCELED->value]);
    }
    protected function pending(): void
    {
        $this->update(['status' => TaskStatus::PENDING->value]);
    }
    public function addDependency($id)
    {
        if ($this->id == $id) {
            throw new \Exception("Task cannot depend on itself.");
        }

        if ($this->wouldCauseCycle($id)) {
            throw new \Exception("Circular dependency detected.");
        }

        return $this->dependencies()->syncWithoutDetaching($id);
    }

    public function wouldCauseCycle($dependsOnId)
    {
        return $this->hasAncestor($dependsOnId);
    }

    public function hasAncestor($taskId)
    {
        foreach ($this->dependencies as $dep) {
            if ($dep->id == $taskId || $dep->hasAncestor($taskId)) {
                return true;
            }
        }
        return false;
    }
}
