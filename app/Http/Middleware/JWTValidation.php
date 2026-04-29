<?php

namespace App\Http\Middleware;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\ApiResponse;
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

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        Log::info('Requested with: Bearer ' . $token);

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

            $isValid = $this->authRepository->validate_expiration($token);
            Log::info('Token is valid: ' . $isValid);
            if(!$isValid){
                throw new Exception('Token is not valid', 401);
            }

            return $next($request);
        } catch (Exception $e) {
            Log::error('Error validating token: ' . $e->getMessage());
            switch($e->getCode()){
                case 401:
                    Log::warning('Unauthorized');

                    return response()
                            ->json(ApiResponse::unauthorized($e->getMessage(), []))
                            ->setStatusCode(401);
                case 426:
                    Log::warning('Must refresh token');
                    $response = $next($request);
                    $response->headers->set('must-refresh-token', true);
                    return $response;
            }
        }
        return $next($request);
    }
}
