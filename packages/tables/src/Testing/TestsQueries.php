<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Assert;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component&HasTable instance()
 *
 * @mixin Testable
 */
class TestsQueries
{
    public function assertTableRendersWithinQueries(): Closure
    {
        $countRenderQueries = $this->getRenderQueryCounter();

        return function (int $maxQueryCount) use ($countRenderQueries): static {
            $queryCount = $countRenderQueries($this);

            $livewireClass = $this->instance()::class;

            Assert::assertLessThanOrEqual(
                $maxQueryCount,
                $queryCount,
                "Failed asserting that the [{$livewireClass}] table renders within [{$maxQueryCount}] queries, [{$queryCount}] queries were executed.",
            );

            return $this;
        };
    }

    public function assertTableQueryCountDoesNotGrowWithRecords(): Closure
    {
        $countRenderQueries = $this->getRenderQueryCounter();

        return function (Closure $createRecords, int $smallRecordCount = 3, int $largeRecordCount = 20) use ($countRenderQueries): static {
            $createRecords($smallRecordCount);

            $smallQueryCount = $countRenderQueries($this);

            $createRecords($largeRecordCount - $smallRecordCount);

            $largeQueryCount = $countRenderQueries($this);

            $livewireClass = $this->instance()::class;

            Assert::assertSame(
                $smallQueryCount,
                $largeQueryCount,
                "Failed asserting that the number of queries executed while rendering the [{$livewireClass}] table does not grow with the number of records. [{$smallQueryCount}] queries were executed for [{$smallRecordCount}] records, and [{$largeQueryCount}] queries were executed for [{$largeRecordCount}] records.",
            );

            return $this;
        };
    }

    public function assertTableHasNoLazyLoading(): Closure
    {
        return function (): static {
            $violations = [];

            $wasPreventingLazyLoading = Model::preventsLazyLoading();
            /** @phpstan-ignore-next-line */
            $previousViolationCallback = Closure::bind(static fn (): ?callable => Model::$lazyLoadingViolationCallback, null, Model::class)();

            Model::preventLazyLoading();
            Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) use (&$violations): void {
                $violations[] = $model::class . '::' . $relation;
            });

            try {
                $this->call('$refresh');
            } finally {
                Model::preventLazyLoading($wasPreventingLazyLoading);
                Model::handleLazyLoadingViolationUsing($previousViolationCallback);
            }

            $violations = array_values(array_unique($violations));

            $livewireClass = $this->instance()::class;

            Assert::assertEmpty(
                $violations,
                "Failed asserting that the [{$livewireClass}] table renders without lazy loading relationships. The following relationships were lazy loaded: [" . implode('], [', $violations) . '].',
            );

            return $this;
        };
    }

    /**
     * @return Closure(Testable): int
     */
    private function getRenderQueryCounter(): Closure
    {
        return function (Testable $testable): int {
            /** @var Connection $connection */
            $connection = $testable->instance()->getTable()->getQuery()?->getConnection() ?? DB::connection();

            $wasLogging = $connection->logging();
            $connection->enableQueryLog();

            $initialQueryCount = count($connection->getQueryLog());

            try {
                $testable->call('$refresh');

                return count($connection->getQueryLog()) - $initialQueryCount;
            } finally {
                if (! $wasLogging) {
                    $connection->disableQueryLog();
                }
            }
        };
    }
}
