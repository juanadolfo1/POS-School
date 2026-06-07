<?php

namespace App\Http\Middleware;

use App\Models\ApiResponse;
use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PermissionValidation
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function handle(Request $request, Closure $next, string $module, string $operation): Response
    {
        $userId = $this->getUserIdFromToken($request->bearerToken());

        if (!$userId) {
            return response()
                ->json(ApiResponse::unauthorized('User not identified', []))
                ->setStatusCode(401);
        }

        $hasPermission = DB::table('users')
            ->join('role_operations as ro', 'ro.role_id', '=', 'users.role_id')
            ->join('operations as op', 'op.id', '=', 'ro.operation_id')
            ->join('modules as m', 'm.id', '=', 'op.module_id')
            ->where([
                ['users.id', '=', $userId],
                ['m.module_name', '=', $module],
                ['op.operation_name', '=', $operation],
            ])
            ->exists();

        if (!$hasPermission) {
            $this->auditService->log($userId, 'ACCESS_DENIED', $module, null, [
                'operation' => $operation,
                'endpoint' => $request->method() . ' ' . $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()
                ->json(ApiResponse::forbidden('No tienes permiso para realizar esta acción', []))
                ->setStatusCode(403);
        }

        $request->merge(['auth_user_id' => $userId]);

        return $next($request);
    }

    private function getUserIdFromToken(?string $token): ?int
    {
        if (!$token) return null;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])));

        return $payload->sub ?? null;
    }
}
