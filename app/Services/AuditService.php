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
        AuditLog::create([
            'event' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'actor_id' => Auth::id(), // Nullable if system action
            'properties' => $properties,
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
