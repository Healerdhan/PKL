<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->user();

            $role = $user->roles->first()->name ?? null;
            $permissions = $user->getAllPermissions()->pluck('name');


            $response = [
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil mendapatkan data',
                'error' => null,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'user_verified_at' => $user->created_at,
                    'image' => $user->image,
                    'role' => $role,
                    'role_id' => $user->roles->first()->id ?? null,
                    'permissions' => $permissions,
                ],
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
