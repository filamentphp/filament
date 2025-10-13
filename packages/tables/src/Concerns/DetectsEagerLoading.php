<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait DetectsEagerLoading
{
    /**
     * Cache for detected relationships to avoid repeated analysis.
     */
    protected ?array $detectedRelationships = null;

    /**
     * Automatically detect and apply eager loading for relationships used in columns.
     */
    public function applyEagerLoadingToTableQuery(Builder $query): Builder
    {
        if (! $this->shouldAutoEagerLoad()) {
            return $query;
        }

        $relationships = $this->getRequiredEagerLoadRelationships();

        if (empty($relationships)) {
            return $query;
        }

        // Apply eager loading with counts and exists separately
        $withRelationships = $relationships['with'] ?? [];
        $withCountRelationships = $relationships['withCount'] ?? [];
        $withExistsRelationships = $relationships['withExists'] ?? [];

        if (! empty($withRelationships)) {
            $query->with($withRelationships);
        }

        if (! empty($withCountRelationships)) {
            $query->withCount($withCountRelationships);
        }

        if (! empty($withExistsRelationships)) {
            $query->withExists($withExistsRelationships);
        }

        return $query;
    }

    /**
     * Get all relationships that need to be eager loaded based on table columns.
     */
    public function getRequiredEagerLoadRelationships(): array
    {
        if ($this->detectedRelationships !== null) {
            return $this->detectedRelationships;
        }

        $relationships = [
            'with' => [],
            'withCount' => [],
            'withExists' => [],
        ];

        $table = $this->getTable();
        $columns = $table->getColumns();

        foreach ($columns as $column) {
            // Detect dot notation relationships (e.g., 'author.name')
            $columnName = $column->getName();

            if (Str::contains($columnName, '.')) {
                $relationshipPath = $this->extractRelationshipPath($columnName);

                if ($relationshipPath) {
                    $relationships['with'][] = $relationshipPath;
                }
            }

            // Detect count relationships
            if (method_exists($column, 'getCounts') && $column->getCounts()) {
                $relationships['withCount'][] = $column->getCounts();
            }

            // Detect exists relationships
            if (method_exists($column, 'getExists') && $column->getExists()) {
                $relationships['withExists'][] = $column->getExists();
            }

            // Detect relationship from state path
            if (method_exists($column, 'getRelationship') && $relationship = $column->getRelationship()) {
                $relationships['with'][] = $relationship;
            }
        }

        // Remove duplicates and filter empty values
        $relationships['with'] = array_unique(array_filter($relationships['with']));
        $relationships['withCount'] = array_unique(array_filter($relationships['withCount']));
        $relationships['withExists'] = array_unique(array_filter($relationships['withExists']));

        // Build nested relationship arrays for proper eager loading
        $relationships['with'] = $this->buildNestedRelationships($relationships['with']);

        $this->detectedRelationships = $relationships;

        return $this->detectedRelationships;
    }

    /**
     * Extract relationship path from column name (e.g., 'author.name' -> 'author').
     */
    protected function extractRelationshipPath(string $columnName): ?string
    {
        // Handle nested relationships (e.g., 'author.department.name' -> 'author.department')
        $parts = explode('.', $columnName);

        // Remove the last part (the attribute name)
        array_pop($parts);

        return ! empty($parts) ? implode('.', $parts) : null;
    }

    /**
     * Build nested relationships array for optimal eager loading.
     *
     * Converts ['author', 'author.department', 'posts']
     * to ['author' => ['department'], 'posts']
     */
    protected function buildNestedRelationships(array $relationships): array
    {
        $nested = [];

        foreach ($relationships as $relationship) {
            if (! Str::contains($relationship, '.')) {
                // Simple relationship
                if (! isset($nested[$relationship])) {
                    $nested[$relationship] = [];
                }
            } else {
                // Nested relationship
                $parts = explode('.', $relationship);
                $current = &$nested;

                foreach ($parts as $index => $part) {
                    if ($index === count($parts) - 1) {
                        // Last part
                        if (! isset($current[$part])) {
                            $current[$part] = [];
                        }
                    } else {
                        // Intermediate part
                        if (! isset($current[$part])) {
                            $current[$part] = [];
                        }
                        $current = &$current[$part];
                    }
                }
            }
        }

        // Convert nested array format to Laravel's eager loading format
        return $this->convertToLaravelEagerLoadFormat($nested);
    }

    /**
     * Convert nested array to Laravel eager loading format.
     */
    protected function convertToLaravelEagerLoadFormat(array $nested): array
    {
        $result = [];

        foreach ($nested as $key => $value) {
            if (empty($value)) {
                // Simple relationship
                $result[] = $key;
            } else {
                // Nested relationship
                $result[$key] = $this->convertToLaravelEagerLoadFormat($value);
            }
        }

        return $result;
    }

    /**
     * Check if auto eager loading should be applied.
     */
    protected function shouldAutoEagerLoad(): bool
    {
        // Disable in testing environment to prevent memory issues
        if (app()->environment('testing')) {
            return false;
        }

        // Check config setting
        if (config('filament.performance.auto_eager_load') === false) {
            return false;
        }

        // Allow per-table override
        if (property_exists($this, 'disableAutoEagerLoad') && $this->disableAutoEagerLoad === true) {
            return false;
        }

        return true;
    }

    /**
     * Get a summary of detected relationships for debugging.
     */
    public function getEagerLoadingSummary(): array
    {
        $relationships = $this->getRequiredEagerLoadRelationships();

        return [
            'total_relationships' => count($relationships['with']) + count($relationships['withCount']) + count($relationships['withExists']),
            'with' => $relationships['with'],
            'withCount' => $relationships['withCount'],
            'withExists' => $relationships['withExists'],
            'auto_eager_load_enabled' => $this->shouldAutoEagerLoad(),
        ];
    }

    /**
     * Log detected relationships for debugging (development only).
     */
    protected function logEagerLoadingDetection(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        if (! config('filament.performance.log_eager_loading', false)) {
            return;
        }

        $summary = $this->getEagerLoadingSummary();

        if ($summary['total_relationships'] > 0) {
            logger()->info('Filament: Auto-eager loading applied', [
                'table' => static::class,
                'relationships' => $summary,
            ]);
        }
    }

    /**
     * Manually override detected relationships (for advanced use cases).
     */
    public function setEagerLoadRelationships(array $with = [], array $withCount = [], array $withExists = []): static
    {
        $this->detectedRelationships = [
            'with' => $with,
            'withCount' => $withCount,
            'withExists' => $withExists,
        ];

        return $this;
    }

    /**
     * Clear cached relationship detection.
     */
    public function clearEagerLoadingCache(): static
    {
        $this->detectedRelationships = null;

        return $this;
    }
}
