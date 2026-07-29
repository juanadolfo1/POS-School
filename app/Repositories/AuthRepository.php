<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\Module;
use App\Models\SessionToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthRepository implements AuthRepositoryInterface{

    protected int $validTime;

    public function __construct(){
        $this->validTime = env('JWT_VALID_TIME', 300);
    }

    private function base64URLEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64URLDecode(string $text): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $text));
    }

    public function register_user($userData): User{
        $user = new User();
        $user->name = $userData->name;
        $user->email = $userData->email;
        $user->password = Hash::make($userData->password);
        $user->role_id = $userData->role_id;
        $user->save();

        return $user;
    }

    public function assign_role(User $user, int $idRole): User{
        $user->role_id = $idRole;
        $user->save();

        return $user;
    }

    public function update_user($userData): User{
        $user = User::find($userData->id);
        if($user){
            $user->email = $userData->email;
            $user->password = $userData->password;
            $user->save();
        }

        return $user;
    }

    public function login(Request $request): ?User{
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        if(Auth::attempt($credentials)){
            $user = User::where('email', '=', $request->email)
                    ->select(
                        'id',
                        'name',
                        'email',
                        'role_id'
                    )
                    ->first();
            return $user;
        }

        return null;
    }

    public function logout(string $email): void{
        $user = User::where('email', '=', $email)->first();
        if($user){
            SessionToken::where('user_id', '=', $user->id)->delete();
        }
    }

    public function is_already_logged(int $userId): bool
    {
        $now = time();
        $activeToken = SessionToken::where('user_id', '=', $userId)
                        ->where('expiration', '>', $now)
                        ->first();

        return !!$activeToken;
    }

    public function generate_jwt_token($user): string
    {
        $now = time();
        $expiration_time = $now + $this->validTime;

        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);

        // Generar hash de permisos para validación cruzada en frontend
        $permissionsHash = $this->generatePermissionsHash($user->role_id);

        $payload = json_encode([
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $expiration_time,
            'permissions_hash' => $permissionsHash
        ]);

        $header = $this->base64URLEncode($header);
        $payload = $this->base64URLEncode($payload);

        $signature = hash_hmac('sha256', $header . '.' . $payload, env('APP_KEY'), true);
        $signature = $this->base64URLEncode($signature);

        $jwt = $header . '.' . $payload . '.' . $signature;

        $sessionToken = new SessionToken();
        $sessionToken->token = $jwt;
        $sessionToken->expiration = $expiration_time;
        $sessionToken->user_id = $user->id;
        $sessionToken->save();

        return $jwt;
    }

    private function generatePermissionsHash(int $roleId): string
    {
        $permissions = DB::table('role_operations as ro')
            ->join('operations as op', 'op.id', '=', 'ro.operation_id')
            ->join('modules as m', 'm.id', '=', 'op.module_id')
            ->where('ro.role_id', $roleId)
            ->orderBy('m.id')
            ->orderBy('op.id')
            ->select('m.module_name', 'op.operation_name')
            ->get()
            ->toJson();

        return hash('sha256', $permissions);
    }

    public function validate_expiration(string $token): bool
    {
        $now = time();
        $tokenParts = explode('.', $token);
        $decodedPayload = $this->base64URLDecode($tokenParts[1]);

        $decodedPayload = json_decode($decodedPayload);
        if($decodedPayload->exp < $now) {
            throw new Exception('Token is expired', 401);
        } else if(($decodedPayload->exp - 60) < $now){
            throw new Exception('Token is early to expire', 426);
        }
        return true;
    }

    public function verify_jwt_token($token): bool
    {
        $tokenParts = explode('.', $token);

        $header = $tokenParts[0];
        $payload = $tokenParts[1];
        $signatureProvided = $tokenParts[2];

        $signature = hash_hmac('sha256', $header . '.' . $payload, env('APP_KEY'), true);
        $signature = $this->base64URLEncode($signature);

        return $signature === $signatureProvided;
    }

    public function invalidate_jwt_token($token): void
    {
        $currentToken = SessionToken::where('token', '=', $token)
                        ->first();
        if($currentToken){
            $currentToken->delete();
        } else {
            throw new Exception('Token not found', 404);
        }
    }

    public function refresh_jwt_token($token): string
    {
        $now = time();
        $jwtParts = explode('.', $token);

        $decodedPayload = $this->base64URLDecode($jwtParts[1]);
        $decodedPayload = json_decode($decodedPayload);

        $isValidToRefresh = abs($decodedPayload->exp - $now) < $this->validTime;

        if(!$isValidToRefresh){
            throw new Exception('Token is not valid to refresh', 403);
        }

        $user = User::find($decodedPayload->sub);
        if(!$user){
            throw new Exception('User not found', 404);
        }

        $refreshed = $this->generate_jwt_token($user);


        return $refreshed;
    }

    public function get_modules_from_jwt(string $jwt): array{
        $now = time();
        $jwtParts = explode('.', $jwt);

        $decodedPayload = $this->base64URLDecode($jwtParts[1]);
        Log::info($decodedPayload);
        $decodedPayload = json_decode($decodedPayload);

        $user = User::find($decodedPayload->sub);
        if(!$user){
            throw new Exception('User not found', 404);
        }
        return $this->get_modules($user->role_id);
    }

    public function get_modules(int $roleId): array
    {
        $modules = Module::join('operations as op', 'op.module_id', '=', 'modules.id')
                            ->join('role_operations as rp', 'op.id', '=', 'rp.operation_id')
                            ->join('roles as r', 'r.id', '=', 'rp.role_id')
                            ->groupBy('modules.id')
                            ->where([
                                ['r.id', '=', $roleId],
                                ['modules.status', '=', '1']
                            ])
                            ->select(
                                'modules.id',
                                'modules.module_name',
                                'modules.path',
                                'modules.icon',
                                DB::raw(
                                    'concat(\'[\', string_agg(concat(\'{\', \'"id": \', op.id, \', "operation_name": "\', op.operation_name ,\'"}\'), \',\'), \']\') as operations'
                                    )
                                )
                            ->get()->toArray();

        $modules = array_map(function($module){
            $parsedOperations = json_decode($module["operations"]);
            $module['operations'] = $parsedOperations;
            return $module;
        }, $modules);

        return $modules;

    }
}
