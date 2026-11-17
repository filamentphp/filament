<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ColumnsBrowserTest;
use Filament\Tests\Tables\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('persists boolean changes through `updateTableColumnState()` and rejects validation and disabled updates', function (string $columnName): void {
    $post = Post::factory()->create(['is_published' => false, 'json' => ['approved' => false]]);
    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true]);

    foreach ([true, false] as $state) {
        $component->call('updateTableColumnState', $columnName, (string) $post->getKey(), $state)
            ->assertReturned($state);

        expect(data_get($post->fresh(), $columnName))->toBe($state);
    }

    $component->set('saveScenario', 'validation')
        ->call('updateTableColumnState', $columnName, (string) $post->getKey(), true);

    expect($component->effects['returns'][0])->toHaveKey('error')
        ->and(data_get($post->fresh(), $columnName))->toBeFalse();

    $component->set('saveScenario', 'disabled')
        ->call('updateTableColumnState', $columnName, (string) $post->getKey(), true)
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect(data_get($post->fresh(), $columnName))->toBeFalse();
})->with(['is_published', 'json.approved']);

it('renders the configured cells instead of the whole table after `updateTableColumnState()`', function (string $saveScenario, array $expectedPartiallyRenderedColumns): void {
    if ($saveScenario === 'partial-row-loop') {
        Post::factory()->count(4)->create();
    }

    $post = Post::factory()->create(['title' => 'Original title', 'rating' => 1]);

    if ($saveScenario === 'partial-row-loop') {
        Post::factory()->count(3)->create();
    }

    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true, 'saveScenario' => $saveScenario]);

    if ($saveScenario === 'partial-row-loop') {
        $component->call('setPage', 2);
    }

    $component->call('updateTableColumnState', 'title', (string) $post->getKey(), 'mixedCase');

    $expectedPartialNames = array_map(
        fn (string $columnName): string => "table.record.{$post->getKey()}.column.{$columnName}",
        $expectedPartiallyRenderedColumns,
    );

    expect($post->fresh())->title->toBe('MIXEDCASE')->rating->toBe(2)
        ->and($component->effects)->not->toHaveKey('html')
        ->and(array_keys($component->effects['partials']))->toBe($expectedPartialNames)
        ->and($component->effects['partials']["table.record.{$post->getKey()}.column.title"])->toContain('value="MIXEDCASE"');

    $partials = $component->effects['partials'];

    if ($saveScenario === 'partial-row-loop') {
        expect($partials["table.record.{$post->getKey()}.column.position"])
            ->toContain('data-row-index="1"', 'data-row-remaining="1"');
    }

    // The full table renders the same cell HTML, so the partial can replace the cell in place.
    $component->call('$refresh');

    foreach ($partials as $partialHtml) {
        expect($component->effects['html'])->toContain(trim($partialHtml));
    }
})->with([
    '`partiallyRenderAfterStateUpdated()`' => ['partial', ['title']],
    '`partiallyRenderColumnsAfterStateUpdated()`' => ['partial-columns', ['title', 'rating']],
    '`partiallyRenderColumnsAfterStateUpdated()` with `rowIndex()`' => ['partial-row-loop', ['title', 'position']],
]);

it('renders the whole table after `updateTableColumnState()` by default', function (): void {
    $post = Post::factory()->create(['title' => 'Original title']);
    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true])
        ->call('updateTableColumnState', 'title', (string) $post->getKey(), 'Updated title');

    expect($component->effects['html'])->toContain('value="Updated title"')->not->toContain('wire:partial="table.record.')
        ->and($component->effects['partials'] ?? [])->toBe([]);
});

it('encodes the record key and column name in `getTableCellPartialName()` without collisions', function (): void {
    $getPartialName = fn (string $recordKey, string $columnName): string => TextInputColumn::make($columnName)
        ->recordKey($recordKey)
        ->getTableCellPartialName();

    expect($getPartialName('1', 'title'))->toBe('table.record.1.column.title')
        ->and($getPartialName("a'b", 'title'))->not->toBe($getPartialName('ab', 'title'))
        ->and($getPartialName('1.column.a', 'b'))->not->toBe($getPartialName('1', 'a.column.b'))
        ->and($getPartialName('"<a\\b>`', 'author.name'))->toMatch('/^[\w.%~-]+$/');
});

describe('authorization', function (): void {
    beforeEach(function (): void {
        $this->actingAs(User::factory()->create());

        Gate::define('updatePublishedPost', fn (User $user, Post $post): bool => (bool) $post->is_published);
        Gate::define('denyPost', fn (User $user, Post $post): Response => Response::deny('Nope.'));
    });

    it('allows updating editable columns without `authorize()`', function (string $columnName): void {
        $post = Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'none'])
            ->assertTableColumnEnabled($columnName, $post);
    })->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

    it('does not apply a default policy check outside of resources', function (): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'none'])
            ->assertTableColumnEnabled('is_published_toggle', $post)
            ->call('updateTableColumnState', 'is_published_toggle', (string) $post->getKey(), false);

        expect($post->fresh()->is_published)->toBeFalse();
    });

    it('denies the default `update` ability when no policy or gate is defined', function (string $columnName): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'default'])
            ->assertTableColumnDisabled($columnName, $post);
    })->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

    it('can opt each editable column type into authorization globally using `configureUsing()`', function (string $columnClass): void {
        Gate::define('update', fn (User $user, Post $post): bool => (bool) $post->is_published);

        $published = Post::factory()->create(['is_published' => true]);
        $draft = Post::factory()->create(['is_published' => false]);

        $columnClass::configureUsing(
            fn (Column $column) => $column->authorize(),
            during: function () use ($columnClass, $published, $draft): void {
                expect($columnClass::make('title')->record($published)->isDisabled())->toBeFalse()
                    ->and($columnClass::make('title')->record($draft)->isDisabled())->toBeTrue()
                    ->and($columnClass::make('title')->authorize(true)->record($draft)->isDisabled())->toBeFalse()
                    ->and($columnClass::make('title')->authorize(null)->record($draft)->isDisabled())->toBeFalse()
                    ->and($columnClass::make('title')->authorize('denyPost')->record($published)->isDisabled())->toBeTrue()
                    ->and($columnClass::make('title')->authorize(true)->disabled()->record($published)->isDisabled())->toBeTrue();
            },
        );

        expect($columnClass::make('title')->record($draft)->isDisabled())->toBeFalse();
    })->with([
        Tables\Columns\ToggleColumn::class,
        Tables\Columns\CheckboxColumn::class,
        TextInputColumn::class,
        Tables\Columns\SelectColumn::class,
    ]);

    it('checks a custom gate ability against the record', function (string $columnName): void {
        $published = Post::factory()->create(['is_published' => true]);
        $draft = Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
            ->assertTableColumnEnabled($columnName, $published)
            ->assertTableColumnDisabled($columnName, $draft);
    })->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

    it('checks a custom gate ability against an array record', function (string $columnClass): void {
        Gate::define('updateArrayRecord', fn (User $user, array $record): bool => $record['is_published']);

        $column = $columnClass::make('title')->authorize('updateArrayRecord');

        $column->record(['title' => 'Published', 'is_published' => true]);

        expect($column->isDisabled())->toBeFalse();

        $column->record(['title' => 'Draft', 'is_published' => false]);

        expect($column->isDisabled())->toBeTrue();
    })->with([
        Tables\Columns\ToggleColumn::class,
        Tables\Columns\CheckboxColumn::class,
        TextInputColumn::class,
        Tables\Columns\SelectColumn::class,
    ]);

    it('accepts a backed enum as the ability', function (): void {
        $published = Post::factory()->create(['is_published' => true]);
        $draft = Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'enum'])
            ->assertTableColumnEnabled('title', $published)
            ->assertTableColumnDisabled('title', $draft);
    });

    it('accepts a static boolean', function (string $authorizationMode, bool $isEnabled): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
            ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
    })->with([
        'true' => ['true', true],
        'false' => ['false', false],
    ]);

    it('accepts a closure returning a boolean with the record injected', function (): void {
        $published = Post::factory()->create(['is_published' => true]);
        $draft = Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'closure-bool'])
            ->assertTableColumnEnabled('title', $published)
            ->assertTableColumnDisabled('title', $draft);
    });

    it('accepts a closure returning a response', function (string $authorizationMode, bool $isEnabled): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
            ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
    })->with([
        'allowed' => ['closure-allow', true],
        'denied' => ['closure-deny', false],
    ]);

    it('exposes the authorization response from the column', function (): void {
        $post = Post::factory()->create();

        $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'gate-deny'])
            ->instance()
            ->getTable()
            ->getColumn('title')
            ->record($post);

        $response = $column->getAuthorizationResponse();

        expect($response->denied())->toBeTrue()
            ->and($response->message())->toBe('Nope.')
            ->and($column->isAuthorized())->toBeFalse()
            ->and($column->isDisabled())->toBeTrue();
    });

    it('is not disabled by authorization when there is no record', function (): void {
        $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'false'])
            ->instance()
            ->getTable()
            ->getColumn('title');

        expect($column->isDisabled())->toBeFalse();
    });

    it('is still disabled by `disabled()` when authorized', function (): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'true', 'shouldDisableColumns' => true])
            ->assertTableColumnDisabled('title', $post);
    });

    it('rejects state updates on the server when unauthorized', function (string $columnName, mixed $state, string $attributeName): void {
        $draft = Post::factory()->create(['is_published' => false, 'title' => 'Original', 'rating' => 1]);
        $originalState = $draft->getAttribute($attributeName);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
            ->call('updateTableColumnState', $columnName, (string) $draft->getKey(), $state)
            ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

        expect($draft->fresh()->getAttribute($attributeName))->toBe($originalState);
    })->with([
        'toggle' => ['is_published_toggle', true, 'is_published'],
        'checkbox' => ['is_published_checkbox', true, 'is_published'],
        'text input' => ['title', 'Changed', 'title'],
        'select' => ['rating', '2', 'rating'],
    ]);

    it('denies the default `update` ability when the gate denies it', function (): void {
        Gate::define('update', fn (User $user, Post $post): bool => (bool) $post->is_published);

        $published = Post::factory()->create(['is_published' => true]);
        $draft = Post::factory()->create(['is_published' => false]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'default'])
            ->assertTableColumnEnabled('title', $published)
            ->assertTableColumnDisabled('title', $draft);
    });

    it('keeps the denial message from a closure response', function (): void {
        $post = Post::factory()->create();

        $column = livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'closure-deny-message'])
            ->instance()
            ->getTable()
            ->getColumn('title')
            ->record($post);

        expect($column->getAuthorizationResponse()->message())->toBe('Not allowed.')
            ->and($column->isDisabled())->toBeTrue();
    });

    it('saves state updates on the server for each editable column type when authorized', function (string $columnName, mixed $state, string $attributeName, mixed $expectedState): void {
        $published = Post::factory()->create(['is_published' => true, 'title' => 'Original', 'rating' => 1]);

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'ability'])
            ->call('updateTableColumnState', $columnName, (string) $published->getKey(), $state);

        expect($published->fresh()->getAttribute($attributeName))->toEqual($expectedState);
    })->with([
        'toggle' => ['is_published_toggle', false, 'is_published', false],
        'checkbox' => ['is_published_checkbox', false, 'is_published', false],
        'text input' => ['title', 'Changed', 'title', 'Changed'],
        'select' => ['rating', '2', 'rating', 2],
    ]);

    it('removes authorization when `authorize(null)` is used', function (string $columnName): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => 'reset'])
            ->assertTableColumnEnabled($columnName, $post);
    })->with(['is_published_toggle', 'is_published_checkbox', 'title', 'rating']);

    it('casts non-boolean closure results', function (string $authorizationMode, bool $isEnabled): void {
        $post = Post::factory()->create();

        livewire(TestTableWithAuthorizedEditableColumns::class, ['authorizationMode' => $authorizationMode])
            ->{$isEnabled ? 'assertTableColumnEnabled' : 'assertTableColumnDisabled'}('title', $post);
    })->with([
        'null' => ['closure-null', false],
        'truthy' => ['closure-truthy', true],
    ]);
});

describe('browser interactions', function (): void {
    beforeEach(function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());
    });

    it('describes validation errors at the focus owner and clears them on correction', function (): void {
        $post = Post::factory()->create();

        foreach ([false, true] as $isDarkMode) {
            $post->refresh()->update(['is_published' => false, 'title' => 'Draft title', 'rating' => 1, 'content' => 'draft', 'json' => ['approved' => false, 'status' => 'draft']]);
            $page = visit('/columns-browser-test?editableColumns=1&saveScenario=validation');

            if ($isDarkMode) {
                $page = $page->inDarkMode();
            }

            foreach ([
                'is_published' => 'input[type="checkbox"]',
                'json-approved' => '[role="switch"]',
                'title' => 'input:not([type="hidden"])',
                'rating' => 'select',
                'content' => '[role="combobox"]',
                'json-status' => 'button[aria-haspopup="listbox"]',
            ] as $name => $controlSelector) {
                $column = '[data-testid="editable-' . $name . '"]';
                $control = $column . ' ' . $controlSelector;

                if ($name === 'title') {
                    $page->assertValue($control, 'Draft title')
                        ->fill($control, 'Updated title')->keys($control, 'Tab');
                } elseif ($name === 'rating') {
                    $page->select($control, '2');
                } elseif ($name === 'content') {
                    $page->keys($control, ['Enter', 'ArrowDown', 'Enter']);
                } elseif ($name === 'json-status') {
                    $page->click($control)->fill($column . ' input[role="combobox"]', 'pub')
                        ->keys($column . ' input[role="combobox"]', ['ArrowDown', 'Enter']);
                } else {
                    $page->click($control);
                }

                $page->assertAttribute($control, 'aria-invalid', 'true');

                if ($isDarkMode) {
                    continue;
                }

                $encodedControl = json_encode($control);
                $page->assertScript(<<<JS
                        (() => {
                            const control = document.querySelector({$encodedControl})
                            return control.getAttribute('aria-describedby').split(' ').some(id => {
                                const description = document.getElementById(id)
                                return description?.checkVisibility() && description.textContent.trim() !== '' && id !== 'editable-column-help'
                            })
                        })()
                        JS, true);

                if ($name !== 'json-approved') {
                    $page->assertAttributeContains($control, 'aria-describedby', 'editable-column-help');
                }
            }

            if ($isDarkMode) {
                $page->assertNoAccessibilityIssues();

                continue;
            }

            expect($post->fresh())->is_published->toBeFalse()->title->toBe('Draft title')->rating->toBe(1)->content->toBe('draft');
            expect($post->fresh()->json)->approved->toBeFalse()->status->toBe('draft');

            $page->assertNoAccessibilityIssues()
                ->fill('[data-testid="editable-title"] input:not([type="hidden"])', 'Corrected title')
                ->keys('[data-testid="editable-title"] input:not([type="hidden"])', 'Tab')
                ->assertValue('[data-testid="editable-title"] input:not([type="hidden"])', 'CORRECTED TITLE')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-invalid')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-busy')
                ->assertAttributeContains('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-describedby', 'editable-column-help')
                ->assertNoSmoke();

            expect($post->fresh()->title)->toBe('CORRECTED TITLE');
        }
    });

    it('displays persisted state after save hooks and `void` callbacks', function (string $scenario, string $expectedState): void {
        $post = Post::factory()->create(['title' => 'Original title']);

        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

        $control = '[data-testid="editable-title"] input:not([type="hidden"])';
        $page->assertValue($control, 'Original title')
            ->fill($control, 'mixedCase')
            ->keys($control, 'Tab')
            ->assertValue($control, $expectedState)
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertNoSmoke();

        expect($post->fresh()->title)->toBe($expectedState);

        if ($scenario === 'partial-summary') {
            $page->assertSeeIn('[data-testid="rating-total"]', '2');
        }
    })->with([
        'normalization' => ['normalize', 'MIXEDCASE'],
        'custom records' => ['custom-records', 'MIXEDCASE'],
        'summary fallback' => ['partial-summary', 'MIXEDCASE'],
    ]);

    it('updates partially rendered cells without rendering the rest of the table', function (string $scenario): void {
        $post = Post::factory()->create(['title' => 'Original title', 'rating' => 1]);

        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

        // Rendering the whole table would morph this cell and remove the attribute added in the browser.
        $page->script(<<<'JS'
            document.querySelector('[data-testid="editable-is_published"]').closest('td').setAttribute('data-testid', 'unrendered-cell')
            JS);

        $control = '[data-testid="editable-title"] input:not([type="hidden"])';
        $page->assertValue($control, 'Original title')
            ->fill($control, 'mixedCase')
            ->keys($control, 'Tab')
            ->assertValue($control, 'MIXEDCASE')
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertPresent('[data-testid="unrendered-cell"]')
            ->assertNoSmoke();

        expect($post->fresh())->title->toBe('MIXEDCASE')->rating->toBe(2);

        if ($scenario === 'partial-row-loop') {
            $page->assertSeeIn('[data-testid="editable-position"]', '1');
        } else {
            $page->assertValue('[data-testid="editable-rating"] select', '2');
        }
    })->with(['partial-columns', 'partial-row-loop']);
});

enum EditableColumnAbility: string
{
    case UpdatePublishedPost = 'updatePublishedPost';
}

class TestTableWithAuthorizedEditableColumns extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $authorizationMode = 'none';

    public bool $shouldDisableColumns = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                $this->configureColumn(Tables\Columns\ToggleColumn::make('is_published_toggle')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published)
                    ->updateStateUsing(fn (Post $record, mixed $state) => $record->update(['is_published' => (bool) $state]))),
                $this->configureColumn(Tables\Columns\CheckboxColumn::make('is_published_checkbox')
                    ->getStateUsing(fn (Post $record): bool => (bool) $record->is_published)
                    ->updateStateUsing(fn (Post $record, mixed $state) => $record->update(['is_published' => (bool) $state]))),
                $this->configureColumn(TextInputColumn::make('title')),
                $this->configureColumn(Tables\Columns\SelectColumn::make('rating')->options([1 => 'Low', 2 => 'High'])),
            ]);
    }

    protected function configureColumn(Column $column): Column
    {
        $column->disabled($this->shouldDisableColumns);

        return match ($this->authorizationMode) {
            'none' => $column,
            'default' => $column->authorize(),
            'ability' => $column->authorize('updatePublishedPost'),
            'enum' => $column->authorize(EditableColumnAbility::UpdatePublishedPost),
            'gate-deny' => $column->authorize('denyPost'),
            'true' => $column->authorize(true),
            'false' => $column->authorize(false),
            'closure-bool' => $column->authorize(fn (Post $record): bool => (bool) $record->is_published),
            'closure-allow' => $column->authorize(fn (): Response => Response::allow()),
            'closure-deny' => $column->authorize(fn (): Response => Response::deny()),
            'closure-deny-message' => $column->authorize(fn (): Response => Response::deny('Not allowed.')),
            'closure-null' => $column->authorize(fn () => null),
            'closure-truthy' => $column->authorize(fn (): int => 1),
            'reset' => $column->authorize(false)->authorize(null),
        };
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
