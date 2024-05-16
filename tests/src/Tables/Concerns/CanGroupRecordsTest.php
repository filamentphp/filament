<?php

use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Filament\Tests\livewire;

uses(TestCase::class);

it('can hide group direction when `groupingDirectionHidden()` method called', function (): void {
    livewire(PostsTable::class)->assertHasNoErrors()
        ->assertDontSee('<x-filament::input.select x-model="direction">')
        ->assertDontSee(__('filament-tables::table.grouping.fields.direction.options.asc'))
        ->assertDontSee(__('filament-tables::table.grouping.fields.direction.options.desc'));
});
