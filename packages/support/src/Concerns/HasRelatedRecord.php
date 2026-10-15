<?php

namespace Filament\Support\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRelatedRecord
{
    /**
     * @return array<Model>
     */
    abstract protected function getRelatedRecords(): array;

    protected function getRelatedRecord(): ?Model
    {
        $relatedRecords = $this->getRelatedRecords();

        return count($relatedRecords) === 1 ? reset($relatedRecords) : null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getStateEvaluationParameters(mixed $state, ?Model $relatedRecord, bool $hasState): array
    {
        if (! $hasState) {
            return [];
        }

        return [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ];
    }

    /**
     * @param  array<mixed>  $state
     * @return array<int | string, Model | null>
     */
    protected function getRelatedRecordsForState(array $state): array
    {
        if ($state === []) {
            return [];
        }

        $relatedRecords = $this->getRelatedRecords();

        if ((count($relatedRecords) === 1) && (count($state) > 1)) {
            return array_fill_keys(array_keys($state), reset($relatedRecords));
        }

        $relatedRecords = array_pad(
            array_slice(array_values($relatedRecords), 0, count($state)),
            count($state),
            null,
        );

        return array_combine(array_keys($state), $relatedRecords);
    }
}
