<?php

use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Tests\Fixtures\Tables\PostsTableWithSessionPersistence;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

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

describe('session persistence', function (): void {
    it('disables session persistence on the table', function (): void {
        $livewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        $table = $livewire->getTable();

        expect($table->persistsFiltersInSession())->toBeFalse()
            ->and($table->persistsSearchInSession())->toBeFalse()
            ->and($table->persistsColumnSearchesInSession())->toBeFalse()
            ->and($table->persistsSortInSession())->toBeFalse()
            ->and($table->persistsGroupInSession())->toBeFalse()
            ->and($table->persistsColumnsInSession())->toBeFalse()
            ->and($table->persistsRecordsPerPageInSession())->toBeFalse();
    });

    it('keeps reordered columns in component state without writing to the session', function (): void {
        $testable = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ]);

        $reorderedTableColumns = array_reverse($testable->instance()->tableColumns);

        $testable
            ->call('applyTableColumnManager', $reorderedTableColumns, true)
            ->set('tableRecordsPerPage', 25)
            ->assertSet('tableColumns.0.name', 'author_id');

        $livewire = $testable->instance();

        expect(array_keys($livewire->getTable()->getColumns()))->toBe(['author_id', 'title'])
            ->and(session()->has($livewire->getTablePerPageSessionKey()))->toBeFalse()
            ->and(session()->has($livewire->getHasReorderedTableColumnsSessionKey()))->toBeFalse();

        $testable
            ->call('applyTableColumnManager', array_reverse($reorderedTableColumns), true)
            ->assertSet('tableColumns.0.name', 'title');

        expect(array_keys($testable->instance()->getTable()->getColumns()))->toBe(['title', 'author_id']);

        $freshLivewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        expect(array_keys($freshLivewire->getTable()->getColumns()))->toBe(['title', 'author_id']);
    });

    it('does not load table state from the session', function (): void {
        session()->put('tables.' . md5(TableSelectLivewireComponent::class) . '_per_page', 25);
        session()->put('tables.' . md5(TableSelectLivewireComponent::class) . '_columns', [
            [
                'type' => TableSelectLivewireComponent::TABLE_COLUMN_MANAGER_COLUMN_TYPE,
                'name' => 'title',
                'label' => 'Title',
                'isHidden' => false,
                'isToggled' => false,
                'isToggleable' => true,
                'isToggledHiddenByDefault' => false,
            ],
        ]);

        $livewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        expect($livewire->tableRecordsPerPage)->toBe(10)
            ->and($livewire->isTableColumnToggledHidden('title'))->toBeFalse()
            ->and(session()->get($livewire->getTablePerPageSessionKey()))->toBe(25);
    });
});
