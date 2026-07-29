<?php

namespace App\Http\Middleware;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\ApiResponse;
use App\Models\SessionToken;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JWTValidation
{
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->query('token');

        if(!$token){
            return response()
                    ->json(ApiResponse::unauthorized('Token not provided', []))
                    ->setStatusCode(401);
        }

        try {
            $verified = $this->authRepository->verify_jwt_token($token);

            if(!$verified){
                throw new Exception('Token is not valid', 401);
            }

            // Blacklist check: token debe existir en session_tokens
            $exists = SessionToken::where('token', $token)->exists();
            if (!$exists) {
                throw new Exception('Token has been revoked', 401);
            }

            $isValid = $this->authRepository->validate_expiration($token);
            if(!$isValid){
                throw new Exception('Token is not valid', 401);
            }

            return $next($request);
        } catch (Exception $e) {
            Log::error('Auth error: ' . $e->getMessage());
            switch($e->getCode()){
                case 401:
                    return response()
                            ->json(ApiResponse::unauthorized($e->getMessage(), []))
                            ->setStatusCode(401);
                case 426:
                    $response = $next($request);
                    $response->headers->set('must-refresh-token', true);
                    return $response;
            }
        }
        return $next($request);
    }
}
