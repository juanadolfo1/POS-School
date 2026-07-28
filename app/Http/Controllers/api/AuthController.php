<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\ApiResponse;
use App\Services\AuditService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;
    protected AuditService $auditService;

    public function __construct(AuthRepositoryInterface $authRepository, AuditService $auditService)
    {
        $this->authRepository = $authRepository;
        $this->auditService = $auditService;
    }

    public function login(Request $request)
    {
        try {
            $user = $this->authRepository->login($request);

            if(!$user){
                $this->auditService->log(null, 'LOGIN_FAILED', 'auth', null, [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                ]);
                return response()
                        ->json(ApiResponse::notFound('User not found', []))
                        ->setStatusCode(404);
            }

            $isAlreadyLoggedIn = $this->authRepository->is_already_logged($user->id);
            if($isAlreadyLoggedIn){
                return response()
                        ->json(ApiResponse::forbidden('User already logged in', []))
                        ->setStatusCode(403);
            }

            $jwt = $this->authRepository->generate_jwt_token($user);
            $user->jwt = $jwt;

            $modules = $this->authRepository->get_modules($user->role_id);
            $user->modules = $modules;

            $this->auditService->log($user->id, 'LOGIN_SUCCESS', 'auth', null, [
                'ip' => $request->ip(),
            ]);

            return response()
                    ->json(ApiResponse::success('User logged in successfully', $user))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error logging in: ' . $e->getMessage());
            return response()
                    ->json(ApiResponse::internalError('Failed to login', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function register(Request $request)
    {
        try {
            $user = $this->authRepository->register_user($request);
            return response()
                    ->json(ApiResponse::success('User registered successfully', $user))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());
            return response()
                    ->json(ApiResponse::internalError('Failed to register user', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function refresh_token(Request $request)
    {
        $jwt = $request->bearerToken();

        try {
            $this->authRepository->invalidate_jwt_token($jwt);
            $refreshed = $this->authRepository->refresh_jwt_token($jwt);

            return response()
                    ->json(ApiResponse::success('Token refreshed successfully', ['jwt' => $refreshed]))
                    ->setStatusCode(200);
        } catch(Exception $e) {
            if($e->getCode()){
                return response()
                        ->json(ApiResponse::forbidden($e->getMessage(), []))
                        ->setStatusCode(403);
            }

            return response()
                    ->json(ApiResponse::internalError('Failed to refresh token', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function logout(Request $request)
    {
        $jwt = $request->bearerToken();
        try {
            $this->authRepository->invalidate_jwt_token($jwt);
            return response()
                    ->json(ApiResponse::success('Logged out successfully', []))
                    ->setStatusCode(200);
        } catch(Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to logout', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function get_modules(Request $request)
    {
        $jwt = $request->bearerToken();
        try {
            $modules = $this->authRepository->get_modules_from_jwt($jwt);
            return response()
                    ->json(ApiResponse::success('Modules fetched successfully', $modules))
                    ->setStatusCode(200);
        } catch(Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to fetch modules', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }
}
