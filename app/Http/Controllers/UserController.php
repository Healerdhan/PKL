<?php

namespace App\Http\Controllers;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\PaginationResponse;
use App\Traits\RequestFilter;
use App\Traits\ResponseFormatter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    use ResponseFormatter, PaginationResponse, RequestFilter;

    public function index()
    {
        try {
            $users = User::with('roles')->get();
            if (!$users) {
                throw new Error(404, "Not Found");
            }
            return $this->success(Code::SUCCESS, $users, Message::successGet);
        } catch (Error | \Exception $e) {
            return $this->error($e);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'image' => 'nullable|string|max:255',
                'role' => 'required|in:super-admin,admin,user',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $request->image,
            ]);

            $user->assignRole($request->role);

            DB::commit();
            return $this->success(Code::POST_SUCCESS, $user, Message::successCreate);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            if (!$user) {
                throw new Error(404, "Not Found");
            }
            return $this->success(Code::SUCCESS, $user, Message::successGet);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'image' => 'nullable|string|max:255',
                'role' => 'sometimes|required|in:super-admin,admin,user',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = User::findOrFail($id);

            $user->update($request->only('name', 'email', 'password', 'image'));

            if ($request->has('role')) {
                $user->syncRoles($request->role);
            }

            DB::commit();
            return $this->success(Code::SUCCESS, $user, Message::successUpdate);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
            return $this->success(Code::SUCCESS, null, Message::successDelete);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
