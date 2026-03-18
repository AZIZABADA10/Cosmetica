<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json(['message' =>'tout les utilisateurs','users'=> $users],200);
    }

    public function getUserById($id)
    {
        $user = User::findOrFail($id);
        return response()->json([$user],200);
    }

    public function renderUserEmployee($id)
    {
        $user = User::findOrFail($id);
        $role = Role::where('name','employe')->firstOrFail();
        $user->role_id = $role->id;
        $user->save();
        return response()->json([
            'message'=> 'le role a été changer avec succes',
            'role'=>$user->load('role')
            ]);
    }
}
