<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository){
        $this->authRepository = $authRepository;
    }

    public function login(Request $request)
    {
        Log::info('Login request received ' . $request->email);
        try {
            $user = $this->authRepository->login($request);

            if(!$user){
                Log::error('User not found ' . $request->email);
                return response()
                        ->json(ApiResponse::notFound('User not found', []))
                        ->setStatusCode(404);
            }

            $isAlreadyLoggedIn = $this->authRepository->is_already_logged($user->id);
            if($isAlreadyLoggedIn){
                Log::error('User already logged in ' . $request->email);
                return response()
                        ->json(ApiResponse::forbidden('User already logged in', []))
                        ->setStatusCode(403);
            }

            $jwt = $this->authRepository->generate_jwt_token($user);
            $user->jwt = $jwt;

            $modules = $this->authRepository->get_modules($user->role_id);
            $user->modules = $modules;

            Log::info('User logged in successfully ' . $user->email);
            return response()
                    ->json(ApiResponse::success('User logged in successfully', $user))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error logging in: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to login', [$e->getMessage()]))
                    ->setStatusCode(500);
        }

    }

    public function register(Request $request)
    {

        Log::info('Register request received ' . $request->email);
        try {
            $user = $this->authRepository->register_user($request);
            Log::info('User registered successfully ' . $user->email);
            return response()
                    ->json(ApiResponse::success('User registered successfully', $user))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error registering user: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to register user', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function refresh_token(Request $request)
    {
        Log::info('Refresh token request received');
        $jwt = $request->bearerToken();

        try {
            $this->authRepository->invalidate_jwt_token($jwt);

            $refreshed = $this->authRepository->refresh_jwt_token($jwt);

            return response()
                    ->json(ApiResponse::success('Token refreshed successfully', ['jwt' => $refreshed]))
                    ->setStatusCode(200);
        } catch(Exception $e) {
            if($e->getCode()){
                Log::error($e->getMessage());
                return response()
                        ->json(ApiResponse::forbidden($e->getMessage(), []))
                        ->setStatusCode(403);
            }

            Log::error('Error refreshing token in: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to refresh token', [$e->getMessage()]))
                    ->setStatusCode(500);
        }

    }

    public function get_modules(Request $request){
        Log::info('Get modules request received');
        $jwt = $request->bearerToken();
        Log::info('Bearer token: ' . $jwt);
        try {
            $modules = $this->authRepository->get_modules_from_jwt($jwt);
            return response()
                    ->json(ApiResponse::success('Modules fetched successfully', $modules))
                    ->setStatusCode(200);
        } catch(Exception $e) {
            Log::error('Error fetching modules: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to fetch modules', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }
}
