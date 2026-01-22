<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class AuditService
{
    /**
     * Log an event to the audit table.
     *
     * @param string $event
     * @param string $subjectType
     * @param int|string $subjectId
     * @param array $properties
     * @return void
     */
    public function log(string $event, string $subjectType, int|string $subjectId, array $properties = []): void
    {
        $user = Auth::user();

        AuditLog::create([
            'event_type' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'actor_id' => $user?->id,
            'actor_role' => $user?->role?->value,
            'actor_type' => $properties['actor_type'] ?? 'INTERNAL',
            'properties' => $properties,
            'old_values' => $properties['old'] ?? null,
            'new_values' => $properties['new'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log an event with a model instance subject.
     *
     * @param string $event
     * @param Model $subject
     * @param array $properties
     * @return void
     */
    public function logModel(string $event, Model $subject, array $properties = []): void
    {
        $this->log($event, get_class($subject), $subject->getKey(), $properties);
    }
}
