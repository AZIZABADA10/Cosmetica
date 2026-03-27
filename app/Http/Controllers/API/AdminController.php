<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminService;
use App\Http\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    use JsonResponseTrait;

    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function getAllUsers(): JsonResponse
    {
        $users = User::with('role')->get();
        return $this->successResponse($users, 'Tous les utilisateurs');
    }

    public function getUserById($id): JsonResponse
    {
        $user = User::with('role')->findOrFail($id);
        return $this->successResponse($user);
    }

    public function renderUserEmployee($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $role = Role::where('name', 'employe')->firstOrFail();
        $user->role_id = $role->id;
        $user->save();

        return $this->successResponse($user->load('role'), 'Le rôle a été changé avec succès');
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->adminService->getSalesStats();
        return $this->successResponse($stats, 'Statistiques de vente');
    }
}
