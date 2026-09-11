<?php

use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Tables\PostsTableWithSessionPersistence;
use Filament\Tests\Fixtures\Tables\UsersTableWithSessionPersistence;
use Filament\Tests\TestCase;
use Livewire\Features\SupportTesting\Testable;

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
        livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])
            ->tap(function (Testable $testable): void {
                /** @var TableSelectLivewireComponent $livewire */
                $livewire = $testable->instance();

                $table = $livewire->getTable();

                expect($table->persistsFiltersInSession())->toBeFalse()
                    ->and($table->persistsSearchInSession())->toBeFalse()
                    ->and($table->persistsColumnSearchesInSession())->toBeFalse()
                    ->and($table->persistsSortInSession())->toBeFalse()
                    ->and($table->persistsGroupInSession())->toBeFalse()
                    ->and($table->persistsColumnsInSession())->toBeFalse();
            });
    });

    it('does not write table state to the session', function (): void {
        Post::factory()->count(3)->create();

        livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])
            ->filterTable('is_published')
            ->searchTable('foo')
            ->searchTableColumns(['title' => 'bar'])
            ->sortTable('title')
            ->set('tableGrouping', 'title')
            ->tap(function (Testable $testable): void {
                /** @var TableSelectLivewireComponent $livewire */
                $livewire = $testable->instance();

                expect(session()->has($livewire->getTableFiltersSessionKey()))->toBeFalse()
                    ->and(session()->has($livewire->getTableSearchSessionKey()))->toBeFalse()
                    ->and(session()->has($livewire->getTableColumnSearchesSessionKey()))->toBeFalse()
                    ->and(session()->has($livewire->getTableSortSessionKey()))->toBeFalse()
                    ->and(session()->has($livewire->getTableGroupingSessionKey()))->toBeFalse()
                    ->and(session()->has($livewire->getTableColumnsSessionKey()))->toBeFalse();
            });
    });

    it('does not share table state between table selects with different table configurations', function (): void {
        Post::factory()->count(3)->create();

        livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])
            ->filterTable('is_published')
            ->searchTable('foo')
            ->sortTable('title')
            ->set('tableGrouping', 'title');

        livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(UsersTableWithSessionPersistence::class),
        ])
            ->tap(function (Testable $testable): void {
                /** @var TableSelectLivewireComponent $livewire */
                $livewire = $testable->instance();

                expect($livewire->tableFilters ?? [])->not->toHaveKey('is_published')
                    ->and($livewire->tableSearch)->not->toBe('foo')
                    ->and($livewire->tableSort)->not->toBe('title:asc')
                    ->and($livewire->tableGrouping)->not->toBe('title');
            });
    });

    it('does not load table columns from the session', function (): void {
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

        livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])
            ->tap(function (Testable $testable): void {
                /** @var TableSelectLivewireComponent $livewire */
                $livewire = $testable->instance();

                expect($livewire->isTableColumnToggledHidden('title'))->toBeFalse();
            });
    });
});
