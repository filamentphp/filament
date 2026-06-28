<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRelationshipRecords
{
    protected ?Model $evaluatingRelationshipRecord = null;

    /**
     * @return array<string, mixed>
     */
    public function getNamedInjectionsForStateItem(mixed $state, ?Model $relationshipRecord = null): array
    {
        $injections = [
            'state' => $state,
        ];

        $relationshipRecord ??= $this->evaluatingRelationshipRecord;

        if ($relationshipRecord !== null) {
            $injections['relationshipRecord'] = $relationshipRecord;
        }

        return $injections;
    }

    protected function evaluateForStateItem(mixed $value, mixed $state, ?Model $relationshipRecord = null): mixed
    {
        return $this->evaluate($value, $this->getNamedInjectionsForStateItem($state, $relationshipRecord));
    }

    /**
     * @param  array<mixed>  $state
     * @return array{state: array<mixed>, relationshipRecords: array<Model>}
     */
    protected function sliceStateWithRelationshipRecords(array $state, ?int $limit = null, bool $shouldSlice = true): array
    {
        $relationshipRecords = $this->getRelationshipRecords();

        if ($limit !== null && $shouldSlice && (count($state) > $limit)) {
            $state = array_slice($state, 0, $limit);
            $relationshipRecords = array_slice($relationshipRecords, 0, $limit);
        }

        return [
            'state' => $state,
            'relationshipRecords' => $relationshipRecords,
        ];
    }

    protected function withEvaluatingRelationshipRecord(?Model $relationshipRecord, Closure $callback): mixed
    {
        $previous = $this->evaluatingRelationshipRecord;

        $this->evaluatingRelationshipRecord = $relationshipRecord;

        try {
            return $callback();
        } finally {
            $this->evaluatingRelationshipRecord = $previous;
        }
    }
}
