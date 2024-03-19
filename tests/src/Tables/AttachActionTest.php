<?php

use Filament\Tables\Actions\AttachAction;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('attachActionExists', fn (AttachAction $action) => true)
        ->assertTableActionDoesNotExist('attachActionDoesNotExist');
});

it('can have a multiple select', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('attachActionIsMultiple', fn (AttachAction $action) => $action->isMultiple())
        ->assertTableActionExists('attachActionIsNotMultiple', fn (AttachAction $action) => ! $action->isMultiple());
});
