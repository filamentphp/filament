<?php

use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertTableAssociateActionExists('associateActionExists')
        ->assertTableAssociateActionDoesNotExist('associateActionDoesNotExist');
});

it('can have a multiple select', function () {
    livewire(PostsTable::class)
        ->assertTableAssociateActionIsMultipleSelect('associateActionIsMultiple', true)
        ->assertTableAssociateActionIsNotMultipleSelect('associateActionIsNotMultiple', false);
});
