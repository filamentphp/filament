<?php

namespace Filament\Actions\Testing;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Closure;
use Filament\Actions\Exports\Enums\Contracts\ExportFormat;
use Filament\Actions\Exports\ExportDispatcher;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert;

class ExportFake extends ExportDispatcher
{
    /**
     * @var array<array{export: Export, serializedQuery: string, columnMap: array<string, string>, options: array<string, mixed>, formats: array<ExportFormat>, records: array<mixed> | null}>
     */
    protected array $dispatched = [];

    public function dispatch(
        Export $export,
        string $serializedQuery,
        array $columnMap,
        array $options,
        array $formats,
        ?array $records,
        string $job,
        int $chunkSize,
        ?string $jobQueue,
        ?string $jobConnection,
        ?string $jobBatchName,
        string $authGuard,
    ): void {
        $this->dispatched[] = compact('export', 'serializedQuery', 'columnMap', 'options', 'formats', 'records');
    }

    /**
     * @param  class-string<Exporter>  $exporter
     * @param  (Closure(Export, Builder<Model>, array<string, string>, array<string, mixed>, array<ExportFormat>, array<mixed> | null): bool) | null  $callback
     */
    public function assertDispatched(string $exporter, ?Closure $callback = null): static
    {
        foreach ($this->dispatched as $dispatch) {
            if ($dispatch['export']->exporter !== $exporter) {
                continue;
            }

            if ($callback && (! $callback(
                $dispatch['export'],
                EloquentSerializeFacade::unserialize($dispatch['serializedQuery']),
                $dispatch['columnMap'],
                $dispatch['options'],
                $dispatch['formats'],
                $dispatch['records'],
            ))) {
                continue;
            }

            Assert::assertTrue(true);

            return $this;
        }

        Assert::fail("The expected [{$exporter}] export was not dispatched.");
    }

    /**
     * @param  class-string<Exporter>  $exporter
     */
    public function assertDispatchedTimes(string $exporter, int $count = 1): static
    {
        $actualCount = count(array_filter(
            $this->dispatched,
            static fn (array $dispatch): bool => $dispatch['export']->exporter === $exporter,
        ));

        Assert::assertSame($count, $actualCount, "The expected [{$exporter}] export was dispatched {$actualCount} times instead of {$count} times.");

        return $this;
    }

    public function assertNothingDispatched(): static
    {
        Assert::assertEmpty($this->dispatched, 'Unexpected exports were dispatched.');

        return $this;
    }
}
