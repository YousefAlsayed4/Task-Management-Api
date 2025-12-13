<?php
namespace App\Helpers;

use App\Enums\Http;
use Illuminate\Contracts\Support\Responsable;

class APIResponse implements Responsable
{
    public function __construct(
        public readonly string $status = "success",
        public readonly Http $code = Http::OK,
        public readonly string $message = 'Request completed successfully',
        public readonly array|object $body =  [],
        public readonly ?array $errors = null,
    ) {}

    public function toResponse($request)
    {
        $payload = [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->body,
        ];

        if (!is_null($this->errors)) {
            $payload['errors'] = $this->errors;
        }

        return response()->json($payload, $this->code->value);
    }
}
