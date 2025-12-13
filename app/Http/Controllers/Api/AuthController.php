<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\APIResponse;
use App\Enums\Http;
use App\Repositories\AuthRepositoryInterface;
use Exception;

class AuthController extends Controller
{
    public function __construct(private AuthRepositoryInterface $authRepo) {}

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);

            $result = $this->authRepo->login($credentials);

            if (! $result['user'] || ! $result['token']) {
                return new APIResponse(
                    status: 'fail',
                    code: Http::UNAUTHORIZED,
                    message: __('auth.failed') ?: 'Invalid credentials.',
                    errors: ['credentials' => [__('auth.failed') ?: 'Invalid credentials.']]
                );
            }

            // prepare body
            $body = [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role?->name,
                ],
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ];

            return new APIResponse(
                status: 'success',
                code: Http::OK,
                message: 'Authenticated successfully',
                body: $body
            );

        } catch (Exception $e) {
            return new APIResponse(
                status: 'fail',
                code: Http::SERVER_ERROR,
                message: 'Server error while creating token',
                errors: ['server' => [$e->getMessage()]]
            );
        }
    }
}
