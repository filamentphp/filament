<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set `native()` to `false` and get with `isNative()`', function (): void {
    expect(SelectColumn::make('status')->native(false)->isNative())->toBeFalse();
});

it('defaults `isNative()` to `true`', function (): void {
    expect(SelectColumn::make('status')->isNative())->toBeTrue();
});

it('can set `searchableOptions()` and get with `areOptionsSearchable()`', function (): void {
    expect(SelectColumn::make('status')->searchableOptions()->areOptionsSearchable())->toBeTrue();
});

it('defaults `areOptionsSearchable()` to `false`', function (): void {
    expect(SelectColumn::make('status')->areOptionsSearchable())->toBeFalse();
});

it('can set `optionsLimit()` and get with `getOptionsLimit()`', function (): void {
    expect(SelectColumn::make('status')->optionsLimit(100)->getOptionsLimit())->toBe(100);
});

it('defaults `getOptionsLimit()` to `50`', function (): void {
    expect(SelectColumn::make('status')->getOptionsLimit())->toBe(50);
});

it('can set `position()` and get with `getPosition()`', function (): void {
    expect(SelectColumn::make('status')->position('bottom')->getPosition())->toBe('bottom');
});

it('defaults `getPosition()` to `null`', function (): void {
    expect(SelectColumn::make('status')->getPosition())->toBeNull();
});

it('can set `wrapOptionLabels()` to `false` and get with `canOptionLabelsWrap()`', function (): void {
    expect(SelectColumn::make('status')->wrapOptionLabels(false)->canOptionLabelsWrap())->toBeFalse();
});

it('defaults `canOptionLabelsWrap()` to `true`', function (): void {
    expect(SelectColumn::make('status')->canOptionLabelsWrap())->toBeTrue();
});

it('can set `allowOptionsHtml()` and get with `isOptionsHtmlAllowed()`', function (): void {
    expect(SelectColumn::make('status')->allowOptionsHtml()->isOptionsHtmlAllowed())->toBeTrue();
});

it('defaults `isOptionsHtmlAllowed()` to `false`', function (): void {
    expect(SelectColumn::make('status')->isOptionsHtmlAllowed())->toBeFalse();
});

it('can set `preloadOptions()` and get with `areOptionsPreloaded()`', function (): void {
    expect(SelectColumn::make('status')->preloadOptions()->areOptionsPreloaded())->toBeTrue();
});

it('defaults `areOptionsPreloaded()` to `false`', function (): void {
    expect(SelectColumn::make('status')->areOptionsPreloaded())->toBeFalse();
});

it('can set `rememberOptions()` and get with `areOptionsRemembered()`', function (): void {
    expect(SelectColumn::make('status')->rememberOptions()->areOptionsRemembered())->toBeTrue();
});

it('can set `searchOptionLabels()` to `false` and get with `shouldSearchOptionLabels()`', function (): void {
    expect(SelectColumn::make('status')->searchOptionLabels(false)->shouldSearchOptionLabels())->toBeFalse();
});

it('defaults `shouldSearchOptionLabels()` to `true`', function (): void {
    expect(SelectColumn::make('status')->shouldSearchOptionLabels())->toBeTrue();
});

it('can set `searchOptionValues()` and get with `shouldSearchOptionValues()`', function (): void {
    expect(SelectColumn::make('status')->searchOptionValues()->shouldSearchOptionValues())->toBeTrue();
});

it('defaults `shouldSearchOptionValues()` to `false`', function (): void {
    expect(SelectColumn::make('status')->shouldSearchOptionValues())->toBeFalse();
});

it('can render', function (): void {
    Post::factory()->count(5)->create();

    livewire(TestTableWithSelectColumn::class)
        ->assertSuccessful()
        ->assertCanRenderTableColumn('rating');
});

it('can display different option values', function (): void {
    Post::factory()->create(['rating' => 1]);
    Post::factory()->create(['rating' => 3]);
    Post::factory()->create(['rating' => 5]);

    livewire(TestTableWithSelectColumn::class)
        ->assertSuccessful();
});

it('can set `native()` with a `Closure`', function (): void {
    expect(SelectColumn::make('status')->native(static fn (): bool => false)->isNative())->toBeFalse();
});

it('can set `optionsLimit()` with a `Closure`', function (): void {
    expect(SelectColumn::make('status')->optionsLimit(static fn (): int => 100)->getOptionsLimit())->toBe(100);
});

it('can set `position()` with a `Closure`', function (): void {
    expect(SelectColumn::make('status')->position(static fn (): string => 'bottom')->getPosition())->toBe('bottom');
});

it('can set `wrapOptionLabels()` with a `Closure`', function (): void {
    expect(SelectColumn::make('status')->wrapOptionLabels(static fn (): bool => false)->canOptionLabelsWrap())->toBeFalse();
});

it('can set `forceOptionsSearchCaseInsensitive()` and get with `isOptionsSearchForcedCaseInsensitive()`', function (): void {
    $column = SelectColumn::make('status');

    expect($column->isOptionsSearchForcedCaseInsensitive())->toBeNull();

    $column->forceOptionsSearchCaseInsensitive();

    expect($column->isOptionsSearchForcedCaseInsensitive())->toBeTrue();

    $column->forceOptionsSearchCaseInsensitive(false);

    expect($column->isOptionsSearchForcedCaseInsensitive())->toBeFalse();
});

it('can set `optionsLoadingMessage()` and get with `getOptionsLoadingMessage()`', function (): void {
    $column = SelectColumn::make('status');

    expect($column->getOptionsLoadingMessage())->toBeString();
    expect($column->getOptionsLoadingMessage())->not->toBeEmpty();

    $column->optionsLoadingMessage('Please wait...');

    expect($column->getOptionsLoadingMessage())->toBe('Please wait...');
});

it('can set `optionsSearchDebounce()` and get with `getOptionsSearchDebounce()`', function (): void {
    $column = SelectColumn::make('status')
        ->optionsSearchDebounce(500);

    expect($column->getOptionsSearchDebounce())->toBe(500);
});

it('can set `noOptionsMessage()` and get with `getNoOptionsMessage()`', function (): void {
    $column = SelectColumn::make('status')
        ->noOptionsMessage('Nothing here');

    expect($column->getNoOptionsMessage())->toBe('Nothing here');
});

it('returns `false` for `hasOptionsRelationship()` by default', function (): void {
    expect(SelectColumn::make('status')->hasOptionsRelationship())->toBeFalse();
});

it('returns `null` for `getOptionsRelationshipName()` by default', function (): void {
    expect(SelectColumn::make('status')->getOptionsRelationshipName())->toBeNull();
});

describe('`getSearchableOptionFields()` logic', function (): void {
    it('returns `[label]` by default', function (): void {
        $column = SelectColumn::make('status');

        expect($column->getSearchableOptionFields())->toBe(['label']);
    });

    it('returns `[value]` when only values searchable', function (): void {
        $column = SelectColumn::make('status')
            ->searchOptionLabels(false)
            ->searchOptionValues();

        expect($column->getSearchableOptionFields())->toBe(['value']);
    });

    it('returns `[label, value]` when both searchable', function (): void {
        $column = SelectColumn::make('status')
            ->searchOptionValues();

        expect($column->getSearchableOptionFields())->toBe(['label', 'value']);
    });
});

describe('`searchableOptions()` with array', function (): void {
    it('sets search columns from array and enables searchable', function (): void {
        $column = SelectColumn::make('status')
            ->searchableOptions(['name', 'email']);

        expect($column->areOptionsSearchable())->toBeTrue();
        expect($column->getOptionsSearchColumns())->toBe(['name', 'email']);
    });

    it('clears search columns when set with boolean', function (): void {
        $column = SelectColumn::make('status')
            ->searchableOptions(['name', 'email'])
            ->searchableOptions(true);

        expect($column->areOptionsSearchable())->toBeTrue();
        expect($column->getOptionsSearchColumns())->toBeNull();
    });
});

describe('`getOptionsSearchResults()` logic', function (): void {
    it('returns empty array when no callback is set', function (): void {
        $column = SelectColumn::make('status');

        expect($column->getOptionsSearchResults('test'))->toBe([]);
    });

    it('returns results from custom callback', function (): void {
        $column = SelectColumn::make('status')
            ->getOptionsSearchResultsUsing(static fn (string $search): array => ['found' => "Result: {$search}"]);

        expect($column->getOptionsSearchResults('hello'))->toBe(['found' => 'Result: hello']);
    });
});

describe('`hasDynamicOptions()` logic', function (): void {
    it('returns `false` when options is a static array', function (): void {
        $column = SelectColumn::make('status')
            ->options(['a' => 'A']);

        expect($column->hasDynamicOptions())->toBeFalse();
    });

    it('returns `true` when options is a `Closure`', function (): void {
        $column = SelectColumn::make('status')
            ->options(static fn (): array => ['a' => 'A']);

        expect($column->hasDynamicOptions())->toBeTrue();
    });
});

describe('`hasDynamicOptionsSearchResults()` logic', function (): void {
    it('returns `false` by default', function (): void {
        $column = SelectColumn::make('status');

        expect($column->hasDynamicOptionsSearchResults())->toBeFalse();
    });

    it('returns `true` when callback is set', function (): void {
        $column = SelectColumn::make('status')
            ->getOptionsSearchResultsUsing(static fn (): array => []);

        expect($column->hasDynamicOptionsSearchResults())->toBeTrue();
    });
});

describe('`hasOptionLabelFromRecordUsingCallback()` logic', function (): void {
    it('returns `false` by default', function (): void {
        $column = SelectColumn::make('status');

        expect($column->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
    });

    it('returns `true` when callback is set', function (): void {
        $column = SelectColumn::make('status')
            ->getOptionLabelFromRecordUsing(static fn ($record): string => $record->name);

        expect($column->hasOptionLabelFromRecordUsingCallback())->toBeTrue();
    });
});

describe('`optionsRelationship()` closure: options', function (): void {
    it('loads options from `BelongsTo` relationship', function (): void {
        $users = User::factory()->count(3)->create();
        Post::factory()->create(['author_id' => $users[0]->id]);

        livewire(TestTableWithRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column) use ($users): bool {
                $options = $column->getOptions();

                expect($options)->toHaveCount(3);
                expect($options[$users[0]->id])->toBe($users[0]->name);
                expect($options[$users[1]->id])->toBe($users[1]->name);

                return true;
            });
    });

    it('returns empty options when searchable and not preloaded', function (): void {
        User::factory()->create();
        Post::factory()->create(['author_id' => User::first()->id]);

        livewire(TestTableWithSearchableRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column): bool {
                $options = $column->getOptions();

                // When searchable + not preloaded, options closure returns null which becomes []
                expect($options)->toBe([]);

                return true;
            });
    });

    it('loads options with `getOptionLabelFromRecordUsing()` callback', function (): void {
        $user = User::factory()->create(['name' => 'John Doe']);
        Post::factory()->create(['author_id' => $user->id]);

        livewire(TestTableWithCustomLabelRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column) use ($user): bool {
                $options = $column->getOptions();

                expect($options[$user->id])->toBe("Author: {$user->name}");

                return true;
            });
    });
});

describe('`optionsRelationship()` closure: search results', function (): void {
    it('returns search results from relationship', function (): void {
        User::factory()->create(['name' => 'Alice Smith']);
        User::factory()->create(['name' => 'Bob Jones']);
        User::factory()->create(['name' => 'Alice Johnson']);
        Post::factory()->create(['author_id' => User::first()->id]);

        livewire(TestTableWithRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column): bool {
                $results = $column->getOptionsSearchResults('Alice');

                expect($results)->toHaveCount(2);
                expect(array_values($results))->each->toContain('Alice');

                return true;
            });
    });
});

describe('`optionsRelationship()` closure: option label', function (): void {
    it('resolves label from title attribute for selected record', function (): void {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $post = Post::factory()->create(['author_id' => $user->id]);

        livewire(TestTableWithRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column) use ($post): bool {
                $column->record($post);

                $label = $column->getOptionLabel();

                expect($label)->toBe('Jane Doe');

                return true;
            });
    });

    it('resolves label using `getOptionLabelFromRecordUsing()` callback', function (): void {
        $user = User::factory()->create(['name' => 'Custom User']);
        $post = Post::factory()->create(['author_id' => $user->id]);

        livewire(TestTableWithCustomLabelRelationshipSelectColumn::class)
            ->assertTableColumnExists('author_id', function (SelectColumn $column) use ($post, $user): bool {
                $column->record($post);

                $label = $column->getOptionLabel();

                expect($label)->toBe("Author: {$user->name}");

                return true;
            });
    });
});

describe('rendering', function (): void {
    it('can render with `native(false)`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithNonNative::class)->assertSuccessful();
    });

    it('can render with `native()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithClosureNative::class)->assertSuccessful();
    });

    it('can render with `searchableOptions()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithSearchable::class)->assertSuccessful();
    });

    it('can render with `optionsLimit()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithOptionsLimit::class)->assertSuccessful();
    });

    it('can render with `optionsLimit()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithClosureOptionsLimit::class)->assertSuccessful();
    });

    it('can render with `position()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithPosition::class)->assertSuccessful();
    });

    it('can render with `position()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithClosurePosition::class)->assertSuccessful();
    });

    it('can render with `wrapOptionLabels(false)`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithNoWrapLabels::class)->assertSuccessful();
    });

    it('can render with `wrapOptionLabels()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithClosureWrapLabels::class)->assertSuccessful();
    });

    it('can render with `allowOptionsHtml()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithAllowHtml::class)->assertSuccessful();
    });

    it('can render with `preloadOptions()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithPreload::class)->assertSuccessful();
    });

    it('can render with `noOptionsMessage()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithNoOptionsMessage::class)->assertSuccessful();
    });

    it('can render with `optionsSearchDebounce()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithSearchDebounce::class)->assertSuccessful();
    });

    it('can render with `optionsLoadingMessage()`', function (): void {
        Post::factory()->create();
        livewire(RenderSelectColumnWithLoadingMessage::class)->assertSuccessful();
    });
});

class TestTableWithRelationshipSelectColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\SelectColumn::make('author_id')
                    ->optionsRelationship('author', 'name'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithSearchableRelationshipSelectColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\SelectColumn::make('author_id')
                    ->optionsRelationship('author', 'name')
                    ->searchableOptions(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithCustomLabelRelationshipSelectColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\SelectColumn::make('author_id')
                    ->optionsRelationship('author', 'name')
                    ->getOptionLabelFromRecordUsing(static fn (User $record): string => "Author: {$record->name}"),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithSelectColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\SelectColumn::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithNonNative extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1', 2 => '2', 3 => '3'])->native(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithClosureNative extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1', 2 => '2'])->native(static fn (): bool => false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithSearchable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1', 2 => '2', 3 => '3'])->searchableOptions(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithOptionsLimit extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1', 2 => '2'])->optionsLimit(100),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithClosureOptionsLimit extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->optionsLimit(static fn (): int => 100),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithPosition extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->position('bottom'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithClosurePosition extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->position(static fn (): string => 'bottom'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithNoWrapLabels extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->wrapOptionLabels(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithClosureWrapLabels extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->wrapOptionLabels(static fn (): bool => false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithAllowHtml extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '<b>1</b>'])->allowOptionsHtml(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithPreload extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1', 2 => '2'])->native(false)->preloadOptions(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithNoOptionsMessage extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->noOptionsMessage('Nothing here'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithSearchDebounce extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->searchableOptions()->optionsSearchDebounce(500),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderSelectColumnWithLoadingMessage extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            Tables\Columns\SelectColumn::make('rating')->options([1 => '1'])->native(false)->optionsLoadingMessage('Please wait...'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
