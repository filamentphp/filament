<?php

use Filament\Tables\Actions\AssociateAction;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('associateActionExists', fn (AssociateAction $action) => true)
        ->assertTableActionDoesNotExist('associateActionDoesNotExist');
});

it('can have a multiple select', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('associateActionIsMultiple', fn (AssociateAction $action) => $action->isMultiple())
        ->assertTableActionExists('associateActionIsNotMultiple', fn (AssociateAction $action) => ! $action->isMultiple());
});
