<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Filters\QueryBuilder\Concerns\HasConstraints;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasConstraints instance()
 *
 * @mixin Testable
 */
class TestsQueryBuilders
{
    public function queryBuilderTable(): Closure
    {
        return function (string $column, string $operator, $data = null): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableConstraintExists($column);

            $constraint = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($column);

            if ($constraint instanceof TextConstraint) {
                $queryBuilder = [
                    'type' => $column,
                    'data' => [
                        'operator' => $operator,
                        'settings' => [],
                    ]
                ];

                if ($data) {
                    $queryBuilder['data']['settings'] = ['text' => $data];
                }
            }

            $this->set("tableFilters.queryBuilder.rules.{$constraint->getName()}", $queryBuilder);

            return $this;
        };
    }

    public function assertTableConstraintExists(): Closure
    {
        return function (string $column): static {
            $filter = $this->instance()->getTable()->getFilter('queryBuilder')->getConstraint($column);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Constraint::class,
                $filter,
                message: "Failed asserting that a table filter with name [{$column}] exists on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }
}
