<?php

namespace Filament\Actions\Testing;

use Closure;
use Filament\Actions\Imports\ImportDispatcher;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Testing\Assert;

class ImportFake extends ImportDispatcher
{
    /**
     * @var array<class-string<Importer>, array<array{import: Import, columnMap: array<string, string>, options: array<string, mixed>}>>
     */
    protected array $imports = [];

    public function dispatch(Import $import, iterable $importChunks, array $columnMap, array $options, string $job, ?string $authGuard): ?string
    {
        $this->imports[$import->importer][] = [
            'import' => $import,
            'columnMap' => $columnMap,
            'options' => $options,
        ];

        return null;
    }

    /**
     * @param  class-string<Importer>  $importer
     * @param  (Closure(Import, array<string, string>, array<string, mixed>): bool) | null  $callback
     */
    public function assertDispatched(string $importer, ?Closure $callback = null): static
    {
        foreach ($this->imports[$importer] ?? [] as $import) {
            if ($callback && (! $callback($import['import'], $import['columnMap'], $import['options']))) {
                continue;
            }

            Assert::assertTrue(true);

            return $this;
        }

        Assert::fail("The expected [{$importer}] import was not dispatched" . ($callback ? ' with matching data.' : '.'));
    }

    /**
     * @param  class-string<Importer>  $importer
     */
    public function assertDispatchedTimes(string $importer, int $count = 1): static
    {
        Assert::assertCount($count, $this->imports[$importer] ?? [], "Expected [{$importer}] to be dispatched {$count} time(s).");

        return $this;
    }

    public function assertNothingDispatched(): static
    {
        Assert::assertCount(0, $this->imports, 'Imports were dispatched.');

        return $this;
    }
}
