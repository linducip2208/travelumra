<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditTrail
{
    public static function log(string $action, string $entityType, $entityId = null, ?array $old = null, ?array $new = null): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
