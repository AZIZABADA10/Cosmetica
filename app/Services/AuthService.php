<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthService extends BaseService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $roleId = $this->userRepository->count() === 0 ? 1 : 2; // 1: Admin, 2: Client

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $roleId,
        ]);

        $token = auth('api')->login($user);

        return [
            'user' => $user->load('role'),
            'access_token' => $token,
            'token_type' => 'bearer'
        ];
    }

    public function login(array $credentials): ?array
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        return [
            'user' => auth('api')->user()->load('role'),
            'access_token' => $token,
            'token_type' => 'bearer'
        ];
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
