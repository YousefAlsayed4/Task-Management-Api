<?php
namespace App\Repositories;

interface AuthRepositoryInterface
{
    /**
     * @param array $credentials ['email' => '', 'password' => '']
     * @return array ['user' => User|null, 'token' => string|null]
     */
    public function login(array $credentials): array;
}
