<?php

namespace App\Interfaces;

use App\Models\User;
use App\Models\Module;
use Illuminate\Http\Request;

interface AuthRepositoryInterface{
    function register_user($userData): User;
    function assign_role(User $user, int $idRole): User;
    function update_user($userData): User;

    function login(Request $request): ?User;
    function logout(string $email): void;

    function generate_jwt_token(string $user): string;
    function verify_jwt_token(string $token): bool;
    function validate_expiration(string $token): bool;
    function is_already_logged(int $userId): bool;
    function invalidate_jwt_token(string $token): void;
    function refresh_jwt_token(string $token): string;
    /** @return Module[] */
    function get_modules(int $roleId): array;
    /** @return Module[] */
    function get_modules_from_jwt(string $jwt): array;
}