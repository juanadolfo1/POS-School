<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    /**
     * Registra una acción en el log de auditoría.
     */
    public function log(?int $userId, string $action, string $entity, ?int $entityId = null, ?array $payload = null): void
    {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'payload' => $payload,
        ]);
    }
}
