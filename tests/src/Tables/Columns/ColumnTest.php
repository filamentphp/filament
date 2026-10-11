<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can be constructed with `make()` and a name', function (): void {
    $column = TextColumn::make('title');

    expect($column)->toBeInstanceOf(Column::class);
    expect($column->getName())->toBe('title');
});

it('can implement `Editable` with a one-argument `updateState()` method', function (): void {
    $column = new class('title') extends Column implements Editable
    {
        use Tables\Columns\Concerns\CanBeValidated;

        public function updateState(mixed $state): mixed
        {
            return $state;
        }
    };

    expect($column->updateState('Updated title'))->toBe('Updated title');
});

it('throws `LogicException` from `make()` when name is blank', function (): void {
    expect(static fn () => TextColumn::make(''))
        ->toThrow(LogicException::class, 'must have a unique name');
});

it('returns `null` for `getDefaultName()`', function (): void {
    expect(Column::getDefaultName())->toBeNull();
});

it('throws `LogicException` from `getTable()` when not mounted', function (): void {
    $column = TextColumn::make('title');

    expect(static fn () => $column->getTable())
        ->toThrow(LogicException::class, 'is not mounted to a table');
});

it('can call `getStateFromRecord()` for a relationship without mounting the column', function (string $container): void {
    $author = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $author->getKey()]);
    $column = TextColumn::make('author.name')->record($post);

    match ($container) {
        'stack' => Stack::make([$column])->getColumns(),
        'nested' => Stack::make([Split::make([$column])])->getColumns(),
        'group' => ColumnGroup::make('Author', [$column])->getColumns(),
        default => null,
    };

    expect($column->hasTable())->toBeFalse()
        ->and($column->getStateFromRecord())->toBe($author->name);
})->with(['none', 'stack', 'nested', 'group']);

it('preserves `$relatedRecord` in column containers across records and rendering paths', function (string $container, bool $combinedUrl, bool $decorated): void {
    $firstAuthor = User::factory()->create(['name' => 'Shared author']);
    $secondAuthor = User::factory()->create(['name' => 'Shared author']);
    $firstPost = Post::factory()->create(['author_id' => $firstAuthor->getKey()]);
    $secondPost = Post::factory()->create(['author_id' => $secondAuthor->getKey()]);
    $firstTeam = Team::factory()->create(['name' => 'Shared team']);
    $secondTeam = Team::factory()->create(['name' => 'Shared team']);
    $thirdTeam = Team::factory()->create(['name' => 'Other team']);
    $firstAuthor->teams()->attach([$firstTeam->getKey(), $secondTeam->getKey()]);
    $secondAuthor->teams()->attach($thirdTeam);

    $component = livewire(RenderRelatedRecordColumnsInContainers::class, compact('container', 'combinedUrl', 'decorated'))
        ->assertSuccessful();

    preg_match_all('/<a\b[^>]*href="([^"]+)"/', $component->html(), $matches);

    expect($matches[1])->toBe($combinedUrl ? [
        "/posts/{$firstPost->getKey()}/authors/{$firstAuthor->getKey()}/Shared%20author",
        "/posts/{$firstPost->getKey()}/teams/{$firstTeam->getKey()}/Shared%20team",
        "/posts/{$firstPost->getKey()}/teams/{$secondTeam->getKey()}/Shared%20team",
        "/posts/{$secondPost->getKey()}/authors/{$secondAuthor->getKey()}/Shared%20author",
        "/posts/{$secondPost->getKey()}/teams/{$thirdTeam->getKey()}/Other%20team",
    ] : [
        "/authors/{$firstAuthor->getKey()}",
        "/teams/{$firstTeam->getKey()}",
        "/teams/{$secondTeam->getKey()}",
        "/authors/{$secondAuthor->getKey()}",
        "/teams/{$thirdTeam->getKey()}",
    ]);

    $column = $component->instance()->getTable()->getColumn('author.teams.name');

    foreach ([$firstPost, $secondPost, $firstPost] as $post) {
        $column->record($post);

        expect(collect($column->getRelatedRecords())->map->getKey()->all())
            ->toBe($post->is($firstPost) ? [$firstTeam->getKey(), $secondTeam->getKey()] : [$thirdTeam->getKey()]);
    }
})->with(['stack', 'nested', 'group'])->with([false, true])->with([false, true]);

it('renders relationship item URLs inside `Stack` accessibly in light and dark modes', function (): void {
    Artisan::call('filament:assets');
    $author = User::factory()->create(['name' => 'Alex Morgan']);
    $post = Post::factory()->create(['author_id' => $author->getKey()]);
    $firstTeam = Team::factory()->create(['name' => 'Research']);
    $secondTeam = Team::factory()->create(['name' => 'Delivery']);
    $author->teams()->attach([$firstTeam->getKey(), $secondTeam->getKey()]);
    $this->actingAs($author);

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/columns-browser-test?relatedRecordLayout=true');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->assertNoSmoke()
            ->assertScript('[...document.querySelectorAll(\'[data-testid="related-record-layout"] a\')].map(anchor => anchor.getAttribute("href"))', [
                "/authors/{$author->getKey()}",
                "/posts/{$post->getKey()}/teams/{$firstTeam->getKey()}/Research",
                "/posts/{$post->getKey()}/teams/{$secondTeam->getKey()}/Delivery",
            ])
            ->assertNoAccessibilityIssues();
    }
});

it('can call `getStateFromRecord()` with keyless records after mounting the column', function (): void {
    $column = livewire(RenderColumnWithCustomLabel::class)
        ->instance()
        ->getTable()
        ->getColumn('title');

    expect($column->record(['title' => 'Array title'])->getStateFromRecord())
        ->toBe('Array title')
        ->and($column->record(new Post(['title' => 'Model title']))->getStateFromRecord())
        ->toBe('Model title');
});

describe('label', function (): void {
    it('generates a label from the column name', function (): void {
        $column = TextColumn::make('first_name');

        expect($column->getLabel())->toBe('First name');
    });

    it('can set a custom label', function (): void {
        $column = TextColumn::make('title')
            ->label('Custom Title');

        expect($column->getLabel())->toBe('Custom Title');
    });
});

describe('alignment', function (): void {
    it('returns `null` for `getAlignment()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getAlignment())->toBeNull();
    });

    it('can set `alignment()`', function (): void {
        $column = TextColumn::make('title')
            ->alignment(Alignment::Center);

        expect($column->getAlignment())->toBe(Alignment::Center);
    });
});

describe('sortable', function (): void {
    it('is not sortable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isSortable())->toBeFalse();
    });

    it('can be made sortable', function (): void {
        $column = TextColumn::make('title')
            ->sortable();

        expect($column->isSortable())->toBeTrue();
    });
});

describe('searchable', function (): void {
    it('is not searchable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isSearchable())->toBeFalse();
    });

    it('can be made searchable', function (): void {
        $column = TextColumn::make('title')
            ->searchable();

        expect($column->isSearchable())->toBeTrue();
    });

    it('is not searchable when the `searchable()` closure returns false', function (): void {
        $column = TextColumn::make('title')
            ->searchable(static fn (): bool => false);

        expect($column->isSearchable())->toBeFalse();
    });
});

describe('visibility', function (): void {
    it('is visible by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isVisible())->toBeTrue();
    });

    it('can be hidden', function (): void {
        $column = TextColumn::make('title')
            ->hidden();

        expect($column->isHidden())->toBeTrue();
    });
});

describe('toggleability', function (): void {
    it('is not toggleable by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->isToggleable())->toBeFalse();
    });

    it('can be made toggleable', function (): void {
        $column = TextColumn::make('title')
            ->toggleable();

        expect($column->isToggleable())->toBeTrue();
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $column = TextColumn::make('title')
            ->placeholder('N/A');

        expect($column->getPlaceholder())->toBe('N/A');
    });
});

describe('width', function (): void {
    it('returns `null` for `getWidth()` by default', function (): void {
        $column = TextColumn::make('title');

        expect($column->getWidth())->toBeNull();
    });

    it('can set `width()`', function (): void {
        $column = TextColumn::make('title')
            ->width('200px');

        expect($column->getWidth())->toBe('200px');
    });
});

describe('grow', function (): void {
    it('can check `canGrow()` default', function (): void {
        // TextColumn defaults to grow=true; other columns may differ
        $column = TextColumn::make('title');

        expect($column->canGrow())->toBeTrue();
    });

    it('can set `grow()`', function (): void {
        $column = TextColumn::make('title')
            ->grow();

        expect($column->canGrow())->toBeTrue();
    });
});

describe('rendering', function (): void {
    it('can render with custom `label()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithCustomLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Custom Title');
    });

    it('can render with `alignment()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithAlignment::class)->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        Post::factory()->create(['content' => null]);
        livewire(RenderColumnWithPlaceholder::class)
            ->assertSuccessful()
            ->assertSeeHtml('N/A');
    });

    it('can render with `width()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithWidth::class)->assertSuccessful();
    });

    it('can render with `hidden()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithHidden::class)->assertSuccessful();
    });

    it('can render with `sortable()`', function (): void {
        Post::factory()->create();
        $html = livewire(RenderColumnWithSortable::class)
            ->assertSuccessful()
            ->html();

        preg_match('/<button[^>]+fi-ta-header-cell-sort-btn[^>]*>/', $html, $matches);

        expect($matches)->toHaveCount(1)
            ->and($matches[0])->not->toContain('wire:loading.attr="disabled"');
    });

    it('can render with `searchable()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithSearchable::class)->assertSuccessful();
    });

    it('can render with `searchable()` using a closure', function (): void {
        $post = Post::factory()->create();

        livewire(RenderColumnWithSearchableClosure::class)
            ->searchTable($post->title)
            ->assertCanSeeTableRecords([$post]);
    });

    it('can render with `toggleable()`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithToggleable::class)->assertSuccessful();
    });

    it('can render with `grow(false)`', function (): void {
        Post::factory()->create();
        livewire(RenderColumnWithGrowFalse::class)->assertSuccessful();
    });
});

class RenderColumnWithCustomLabel extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->label('Custom Title'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithAlignment extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->alignment(Alignment::Center),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithPlaceholder extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('content')->placeholder('N/A'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithWidth extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->width('200px'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithHidden extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title'),
            TextColumn::make('content')->hidden(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSortable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->sortable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSearchable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->searchable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithSearchableClosure extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->searchable(static fn (): bool => true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithToggleable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->toggleable(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderColumnWithGrowFalse extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextColumn::make('title')->grow(false),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderRelatedRecordColumnsInContainers extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public string $container = 'stack';

    public bool $combinedUrl = false;

    public bool $decorated = false;

    public function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('author.name')
                ->tooltip($this->decorated ? 'Author' : null)
                ->url($this->combinedUrl
                    ? static fn (string $state, Post $record, User $relatedRecord): string => "/posts/{$record->getKey()}/authors/{$relatedRecord->getKey()}/" . rawurlencode($state)
                    : static fn (User $relatedRecord): string => "/authors/{$relatedRecord->getKey()}"),
            TextColumn::make('author.teams.name')
                ->listWithLineBreaks($this->decorated)
                ->url($this->combinedUrl
                    ? static fn (string $state, Post $record, Team $relatedRecord): string => "/posts/{$record->getKey()}/teams/{$relatedRecord->getKey()}/" . rawurlencode($state)
                    : static fn (Team $relatedRecord): string => "/teams/{$relatedRecord->getKey()}"),
        ];

        return $table->query(Post::query()->orderBy('id'))->columns([
            match ($this->container) {
                'stack' => Stack::make($columns),
                'nested' => Stack::make([Split::make($columns)]),
                'group' => ColumnGroup::make('Author', $columns),
            },
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
