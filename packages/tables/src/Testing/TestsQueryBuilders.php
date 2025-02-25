<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasTable instance()
 *
 * @mixin Testable
 */
class TestsQueryBuilders
{
    public function queryBuilderTable(): Closure
    {
        return function (string $filter, string $operator, $data = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableConstraintExists($filter);

            /** @phpstan-ignore-next-line */
            $constraint = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($filter);

            $queryBuilder = [
                'type' => $filter,
                'data' => [
                    'operator' => $operator,
                    'settings' => [],
                ],
            ];
            if ($constraint instanceof TextConstraint) {
                if ($data) {
                    $queryBuilder['data']['settings'] = ['text' => $data];
                }
            } elseif ($constraint instanceof NumberConstraint) {
                if ($data) {
                    $queryBuilder['data']['settings'] = ['number' => $data];
                }
            } elseif ($constraint instanceof DateConstraint) {
                if ($data) {
                    $queryBuilder['data']['settings'] = ['date' => $data];
                }
            } elseif ($constraint instanceof SelectConstraint) {
                if ($data) {
                    $queryBuilder['data']['settings'] = ['values' => $data];
                }
            }

            $this->set("tableFilters.queryBuilder.rules.{$constraint->getName()}", $queryBuilder);

            return $this;
        };
    }

    public function assertTableConstraintExists(): Closure
    {
        return function (string $name): static {
            /** @phpstan-ignore-next-line */
            $filter = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($name);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Constraint::class,
                $filter,
                message: "Failed asserting that a query builder with name [{$name}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }
}
