<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:super-admin,admin,user',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            DB::commit();
            return response()->json($user, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = app('auth')->user();

            $role = $user->roles->first()->name ?? null;
            $permissions = $user->getAllPermissions()->pluck('name');

            // Buat refresh token
            $refreshToken = $this->createRefreshToken($user);

            // Commit transaksi setelah semua proses berhasil
            DB::commit();

            // Format respons JSON
            $response = [
                'success' => true,
                'code' => 200,
                'message' => 'Berhasil Login',
                'error' => null,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->image, // Pastikan Anda memiliki kolom `image` di tabel pengguna
                    'role' => $role,
                    'permissions' => $permissions,
                    'token' => $token,
                    'type' => 'Bearer',
                    'expired_at' => now()->addMinutes(config('jwt.ttl'))->toDateTimeString(),
                    'refresh_token' => $refreshToken,
                    'refresh_token_expired_at' => now()->addMinutes(config('jwt.refresh_ttl'))->toDateTimeString(),
                ],
            ];

            return response()->json($response);
        } catch (JWTException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Could not create token: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    private function createRefreshToken($user)
    {
        return 'dummy-refresh-token';
    }


    public function logout(Request $request)
    {
        try {

            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {

            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }


    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
