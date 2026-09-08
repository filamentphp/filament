<?php

use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Tests\Fixtures\Tables\PostsTable;
use Filament\Tests\Fixtures\Tables\UsersTable;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('returns `tableArguments` from `getTableArguments()`', function (): void {
    $component = new TableSelectLivewireComponent;
    $component->tableArguments = ['filter' => 'active', 'sort' => 'name'];

    expect($component->getTableArguments())->toBe(['filter' => 'active', 'sort' => 'name']);
});

it('returns empty array for `getTableArguments()` by default', function (): void {
    $component = new TableSelectLivewireComponent;

    expect($component->getTableArguments())->toBe([]);
});

it('renders a blade string', function (): void {
    $component = new TableSelectLivewireComponent;

    expect($component->render())->toBe('{{ $this->table }}');
});

it('scopes `getTableSessionKeyNamespace()` to the `tableConfiguration`', function (): void {
    $component = new TableSelectLivewireComponent;
    $component->tableConfiguration = base64_encode(PostsTable::class);

    expect($component->getTableSessionKeyNamespace())
        ->toBe(TableSelectLivewireComponent::class . '|' . PostsTable::class);
});

it('scopes the table session keys to the `tableConfiguration`', function (): void {
    $component = new TableSelectLivewireComponent;
    $component->tableConfiguration = base64_encode(PostsTable::class);

    $anotherComponent = new TableSelectLivewireComponent;
    $anotherComponent->tableConfiguration = base64_encode(UsersTable::class);

    $table = md5(TableSelectLivewireComponent::class . '|' . PostsTable::class);

    expect($component->getTableFiltersSessionKey())->toBe("tables.{$table}_filters");
    expect($component->getTableColumnsSessionKey())->toBe("tables.{$table}_columns");
    expect($component->getTableSortSessionKey())->toBe("tables.{$table}_sort");
    expect($component->getTableSearchSessionKey())->toBe("tables.{$table}_search");

    expect($component->getTableFiltersSessionKey())->not->toBe($anotherComponent->getTableFiltersSessionKey());
    expect($component->getTableColumnsSessionKey())->not->toBe($anotherComponent->getTableColumnsSessionKey());
    expect($component->getTableSortSessionKey())->not->toBe($anotherComponent->getTableSortSessionKey());
    expect($component->getTableSearchSessionKey())->not->toBe($anotherComponent->getTableSearchSessionKey());
    expect($component->getTableGroupingSessionKey())->not->toBe($anotherComponent->getTableGroupingSessionKey());
    expect($component->getTablePerPageSessionKey())->not->toBe($anotherComponent->getTablePerPageSessionKey());
    expect($component->getTableColumnSearchesSessionKey())->not->toBe($anotherComponent->getTableColumnSearchesSessionKey());
    expect($component->getHasReorderedTableColumnsSessionKey())->not->toBe($anotherComponent->getHasReorderedTableColumnsSessionKey());
});
