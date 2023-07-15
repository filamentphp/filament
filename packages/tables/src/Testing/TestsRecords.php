<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method HasTable instance()
 *
 * @mixin Testable
 */
class TestsRecords
{
    public function assertCanSeeTableRecords(): Closure
    {
        return function (array | Collection $records, bool $inOrder = false): static {
            $html = array_map(
                function ($record) {
                    if ($record instanceof Model) {
                        $record = $this->instance()->getTableRecordKey($record);
                    }

                    return "{$this->instance()->getId()}.table.records.{$record}";
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            if ($inOrder) {
                $this->assertSeeHtmlInOrder($html);
            } else {
                $this->assertSeeHtml($html);
            }

            return $this;
        };
    }

    public function assertCanNotSeeTableRecords(): Closure
    {
        return function (array | Collection $records): static {
            $html = array_map(
                function ($record) {
                    if ($record instanceof Model) {
                        $record = $this->instance()->getTableRecordKey($record);
                    }

                    return "wire:key=\"{$this->instance()->getId()}.table.records.{$record}\"";
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            $this->assertDontSeeHtml($html);

            return $this;
        };
    }

    public function assertCountTableRecords(): Closure
    {
        return function (int $count): static {
            $livewireClass = $this->instance()::class;

            Assert::assertSame(
                $count,
                $this->instance()->getAllTableRecordsCount(),
                message: "Failed asserting that the [{$livewireClass}] table has [{$count}] records in total.",
            );

            return $this;
        };
    }

    public function loadTable(): Closure
    {
        return function (): static {
            $this->call('loadTable');

            return $this;
        };
    }
}
