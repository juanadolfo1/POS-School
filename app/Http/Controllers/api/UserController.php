<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->query('limit', 10);
            $page = (int) $request->query('page', 1);

            $users = User::join('roles', 'roles.id', '=', 'users.role_id')
                ->select('users.id', 'users.name', 'users.email', 'users.role_id', 'roles.role_name')
                ->paginate($limit, ['*'], 'page', $page);

            return response()
                ->json(ApiResponse::success('Users retrieved successfully', $users))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching users: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve users', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:128',
            'email' => 'required|email|unique:users,email|max:128',
            'password' => 'required|string|min:8|max:64',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id;
            $user->save();

            return response()
                ->json(ApiResponse::success('User created successfully', $user))
                ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating user: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to create user', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'nullable|string|max:128',
            'email' => 'nullable|email|max:128',
            'password' => 'nullable|string|min:8|max:64',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        try {
            $user = User::find($request->id);
            if (!$user) {
                return response()
                    ->json(ApiResponse::notFound('User not found', []))
                    ->setStatusCode(404);
            }

            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->role_id = $request->role_id ?? $user->role_id;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            return response()
                ->json(ApiResponse::success('User updated successfully', $user))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating user: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to update user', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function delete(int $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()
                    ->json(ApiResponse::notFound('User not found', []))
                    ->setStatusCode(404);
            }

            $user->delete();

            return response()
                ->json(ApiResponse::success('User deleted successfully', []))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error deleting user: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to delete user', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function get_roles()
    {
        try {
            $roles = Role::select('id', 'role_name')->get();

            return response()
                ->json(ApiResponse::success('Roles retrieved successfully', $roles))
                ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching roles: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve roles', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}
