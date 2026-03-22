<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use JsonResponseTrait;

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse($result, 'Utilisateur créé avec succès', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->errorResponse('Les identifiants fournis sont incorrects.', 401);
        }

        return $this->successResponse($result, 'Connexion réussie');
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(null, 'Déconnexion réussie');
    }
}