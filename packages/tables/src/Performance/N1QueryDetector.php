<?php

namespace Filament\Tables\Performance;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class N1QueryDetector
{
    protected int $queryCount = 0;

    protected int $queryThreshold;

    protected array $queries = [];

    protected bool $enabled = false;

    protected ?string $contextName = null;

    /**
     * Maximum number of queries to store in memory to prevent memory exhaustion.
     */
    protected int $maxStoredQueries = 1000;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->queryThreshold = config('filament.performance.n1_query_threshold', 15);
    }

    /**
     * Enable N+1 query detection (development only).
     */
    public function enable(?string $contextName = null): void
    {
        if (! $this->shouldEnable()) {
            return;
        }

        $this->enabled = true;
        $this->contextName = $contextName ?? request()->url();
        $this->reset();

        Event::listen(QueryExecuted::class, function (QueryExecuted $event) {
            $this->trackQuery($event);
        });
    }

    /**
     * Disable detection.
     */
    public function disable(): void
    {
        $this->enabled = false;
        Event::forget(QueryExecuted::class);
    }

    /**
     * Track a query execution.
     */
    protected function trackQuery(QueryExecuted $event): void
    {
        if (! $this->enabled) {
            return;
        }

        $this->queryCount++;

        // Only store queries if we haven't exceeded the memory limit
        if (count($this->queries) < $this->maxStoredQueries) {
            $this->queries[] = [
                'sql' => $event->sql,
                'bindings' => $event->bindings,
                'time' => $event->time,
                'connection' => $event->connectionName,
            ];
        }

        // Check if threshold exceeded
        if ($this->queryCount === $this->queryThreshold) {
            $this->warnAboutPotentialN1Query();
        }
    }

    /**
     * Warn about potential N+1 query issue.
     */
    protected function warnAboutPotentialN1Query(): void
    {
        $duplicates = $this->detectDuplicateQueries();

        logger()->warning('Filament: Potential N+1 Query Detected', [
            'query_count' => $this->queryCount,
            'threshold' => $this->queryThreshold,
            'context' => $this->contextName,
            'duplicate_patterns' => count($duplicates),
            'suggestion' => 'Consider using eager loading with ->with() in getEloquentQuery()',
            'duplicates' => array_slice($duplicates, 0, 3), // Show first 3
        ]);

        // Flash message for development
        if (config('app.debug') && request()->hasSession()) {
            $message = sprintf(
                "⚠️ N+1 Query Warning: %d queries executed (threshold: %d)\n" .
                "Detected %d duplicate query patterns.\n" .
                "Suggestion: Add eager loading to your Resource's getEloquentQuery() method.",
                $this->queryCount,
                $this->queryThreshold,
                count($duplicates)
            );

            session()->flash('filament.performance.warning', $message);
        }
    }

    /**
     * Detect duplicate query patterns (indicator of N+1).
     */
    protected function detectDuplicateQueries(): array
    {
        $patterns = [];
        $duplicates = [];

        foreach ($this->queries as $query) {
            // Normalize query by replacing values with placeholders
            $pattern = $this->normalizeQuery($query['sql']);

            if (! isset($patterns[$pattern])) {
                $patterns[$pattern] = 0;
            }

            $patterns[$pattern]++;

            // Mark as duplicate if executed more than once
            if ($patterns[$pattern] > 1) {
                $duplicates[$pattern] = [
                    'count' => $patterns[$pattern],
                    'example' => $query['sql'],
                ];
            }
        }

        // Sort by frequency
        uasort($duplicates, fn ($a, $b) => $b['count'] <=> $a['count']);

        return $duplicates;
    }

    /**
     * Normalize SQL query for pattern matching.
     */
    protected function normalizeQuery(string $sql): string
    {
        // Remove specific IDs and values
        $normalized = preg_replace('/\b\d+\b/', '?', $sql);
        $normalized = preg_replace('/"[^"]*"/', '?', $normalized);
        $normalized = preg_replace("/'[^']*'/", '?', $normalized);

        return $normalized;
    }

    /**
     * Get current query count.
     */
    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    /**
     * Get all tracked queries.
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Get analysis report.
     */
    public function getReport(): array
    {
        $duplicates = $this->detectDuplicateQueries();
        $totalTime = array_sum(array_column($this->queries, 'time'));

        return [
            'query_count' => $this->queryCount,
            'total_time_ms' => round($totalTime, 2),
            'average_time_ms' => $this->queryCount > 0 ? round($totalTime / $this->queryCount, 2) : 0,
            'duplicate_patterns' => count($duplicates),
            'duplicates' => $duplicates,
            'threshold_exceeded' => $this->queryCount > $this->queryThreshold,
            'has_n1_issue' => count($duplicates) > 0 && $this->queryCount > $this->queryThreshold,
        ];
    }

    /**
     * Reset counters.
     */
    public function reset(): void
    {
        $this->queryCount = 0;
        $this->queries = [];
    }

    /**
     * Set query threshold for warnings.
     */
    public function setThreshold(int $threshold): static
    {
        $this->queryThreshold = $threshold;

        return $this;
    }

    /**
     * Set maximum number of queries to store in memory.
     */
    public function setMaxStoredQueries(int $maxQueries): static
    {
        $this->maxStoredQueries = $maxQueries;

        return $this;
    }

    /**
     * Check if detection should be enabled.
     */
    protected function shouldEnable(): bool
    {
        // Only enable in local environment (disable in testing to prevent memory issues)
        if (! app()->environment('local')) {
            return false;
        }

        // Check config
        if (config('filament.performance.enable_n1_detection') === false) {
            return false;
        }

        return true;
    }

    /**
     * Generate recommendations based on detected issues.
     */
    public function getRecommendations(): array
    {
        $report = $this->getReport();
        $recommendations = [];

        if ($report['has_n1_issue']) {
            $recommendations[] = [
                'type' => 'N+1_QUERY',
                'severity' => 'high',
                'message' => 'N+1 query pattern detected. Add eager loading to prevent multiple queries.',
                'example' => "public static function getEloquentQuery(): Builder\n{\n    return parent::getEloquentQuery()->with(['relationship']);\n}",
            ];
        }

        if ($report['query_count'] > $this->queryThreshold) {
            $recommendations[] = [
                'type' => 'HIGH_QUERY_COUNT',
                'severity' => 'medium',
                'message' => "Query count ({$report['query_count']}) exceeds recommended threshold ({$this->queryThreshold}).",
                'suggestion' => 'Review relationships and consider eager loading or caching.',
            ];
        }

        if ($report['average_time_ms'] > 50) {
            $recommendations[] = [
                'type' => 'SLOW_QUERIES',
                'severity' => 'medium',
                'message' => "Average query time ({$report['average_time_ms']}ms) is high.",
                'suggestion' => 'Consider adding database indexes or optimizing queries.',
            ];
        }

        return $recommendations;
    }

    /**
     * Create a summary message for developers.
     */
    public function getSummaryMessage(): string
    {
        $report = $this->getReport();
        $recommendations = $this->getRecommendations();

        $message = "Query Performance Summary\n";
        $message .= str_repeat('=', 50) . "\n";
        $message .= "Total Queries: {$report['query_count']}\n";
        $message .= "Total Time: {$report['total_time_ms']}ms\n";
        $message .= "Average Time: {$report['average_time_ms']}ms\n";
        $message .= "Duplicate Patterns: {$report['duplicate_patterns']}\n";

        if (! empty($recommendations)) {
            $message .= "\nRecommendations:\n";
            foreach ($recommendations as $index => $rec) {
                $message .= ($index + 1) . ". [{$rec['severity']}] {$rec['message']}\n";
            }
        }

        return $message;
    }
}
