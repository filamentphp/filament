<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Image;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Profile;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('state updates and relationship persistence', function (): void {
    it('reports a missing persistence target without changing custom `null` callback results', function (): void {
        $post = Post::factory()->create(['author_id' => null]);
        $column = TextInputColumn::make('author.name')->record($post);

        expect($column->updateState('Updated name'))->toBe(['error' => __('filament-tables::table.columns.editable.unavailable')])
            ->and($post->fresh()->author_id)->toBeNull();

        $column->updateStateUsing(static fn (): mixed => null);

        expect($column->updateState('Updated name'))->toBeNull();
    });

    it('persists the constrained singular relationship target', function (bool $hasBeforeStateUpdatedCallback, string $constraintSource): void {
        $author = User::factory()->create();
        $excludedPost = Post::factory()->create([
            'author_id' => $author->getKey(),
            'is_published' => true,
            'rating' => 1,
            'title' => 'Excluded title',
        ]);
        $includedPost = Post::factory()->create([
            'author_id' => $author->getKey(),
            'is_published' => true,
            'rating' => 5,
            'title' => 'Included title',
        ]);

        livewire(TestTableWithConstrainedSingularRelationshipTextInputColumn::class, compact('hasBeforeStateUpdatedCallback', 'constraintSource'))
            ->assertTableColumnEnabled('publishedPost.title', $author)
            ->assertSee('Included title')
            ->assertDontSee('Excluded title')
            ->call('updateTableColumnState', 'publishedPost.title', (string) $author->getKey(), 'Updated title')
            ->assertReturned('Updated title');

        expect($includedPost->refresh()->title)->toBe('Updated title')
            ->and($excludedPost->refresh()->title)->toBe('Excluded title');
    })->with([false, true])->with(['query', 'global-scope', 'filter']);

    it('refuses a singular relationship target excluded by the eager load', function (bool $hasBeforeStateUpdatedCallback, string $constraintSource): void {
        $author = User::factory()->create();
        $excludedPost = Post::factory()->create([
            'author_id' => $author->getKey(),
            'is_published' => true,
            'rating' => 1,
            'title' => 'Excluded title',
        ]);

        livewire(TestTableWithConstrainedSingularRelationshipTextInputColumn::class, compact('hasBeforeStateUpdatedCallback', 'constraintSource'))
            ->assertTableColumnEnabled('publishedPost.title', $author)
            ->assertDontSee('Excluded title')
            ->call('updateTableColumnState', 'publishedPost.title', (string) $author->getKey(), 'Updated title')
            ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

        expect($excludedPost->refresh()->title)->toBe('Excluded title');
    })->with([false, true])->with(['query', 'global-scope', 'filter']);

    it('preserves eager-load constraints on an ancestor of the persistence target', function (): void {
        $author = User::factory()->create(['name' => 'Original name']);
        Profile::factory()->create(['user_id' => $author->getKey(), 'bio' => 'Excluded profile']);

        livewire(TestTableWithNestedConstrainedRelationshipTextInputColumn::class)
            ->call('updateTableColumnState', 'profile.user.name', (string) $author->getKey(), 'Updated name')
            ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

        expect($author->refresh()->name)->toBe('Original name');
    });
});

describe('update callbacks and relationship targets', function (): void {
    it('injects `$state` and a `null` `$relatedRecord` into update callbacks without a relationship', function (): void {
        $calls = [];

        $column = TextInputColumn::make('name')
            ->record(new User(['name' => 'Original name']))
            ->beforeStateUpdated(static function (string $state, ?User $relatedRecord) use (&$calls): void {
                $calls[] = ['before', $state, $relatedRecord];
            })
            ->updateStateUsing(static function (string $state, ?User $relatedRecord) use (&$calls): string {
                $calls[] = ['update', $state, $relatedRecord];

                return strtoupper($state);
            })
            ->afterStateUpdated(static function (string $state, ?User $relatedRecord) use (&$calls): void {
                $calls[] = ['after', $state, $relatedRecord];
            });

        expect($column->updateState('updated'))->toBe('UPDATED')
            ->and($calls)->toBe([
                ['before', 'updated', null],
                ['update', 'updated', null],
                ['after', 'updated', null],
            ]);
    });

    it('resolves one relationship model for all update callbacks without cached rendering state', function (): void {
        $author = User::factory()->create(['name' => 'Original name']);
        $post = Post::factory()->create(['author_id' => $author->getKey()]);
        $callbackRelatedRecords = [];

        $column = TextInputColumn::make('author.name')
            ->record($post)
            ->beforeStateUpdated(static function (User $relatedRecord) use (&$callbackRelatedRecords): void {
                $callbackRelatedRecords[] = $relatedRecord;
            })
            ->updateStateUsing(static function (string $state, Post $record, User $relatedRecord) use (&$callbackRelatedRecords, $post): string {
                expect($state)->toBe('Updated name');
                expect($record)->toBe($post);

                $callbackRelatedRecords[] = $relatedRecord;

                return strtoupper($state);
            })
            ->afterStateUpdated(static function (User $relatedRecord) use (&$callbackRelatedRecords): void {
                $callbackRelatedRecords[] = $relatedRecord;
            });

        expect($column->updateState('Updated name'))->toBe('UPDATED NAME')
            ->and($callbackRelatedRecords)->toHaveCount(3)
            ->and($callbackRelatedRecords[0])->toBe($callbackRelatedRecords[1])
            ->and($callbackRelatedRecords[1])->toBe($callbackRelatedRecords[2])
            ->and($callbackRelatedRecords[0]->is($author))->toBeTrue();
    });

    it('preserves constrained eager loads in `updateStateUsing()`', function (): void {
        $author = User::factory()->create();
        $draft = Post::factory()->create([
            'author_id' => $author->getKey(),
            'is_published' => false,
            'title' => 'Draft title',
        ]);
        $published = Post::factory()->create([
            'author_id' => $author->getKey(),
            'is_published' => true,
            'title' => 'Published title',
        ]);

        livewire(TestTableWithConstrainedRelationshipTextInputColumn::class)
            ->assertSee('Published title')
            ->assertDontSee('Draft title')
            ->call('updateTableColumnState', 'posts.title', (string) $author->getKey(), 'Updated title')
            ->assertHasNoErrors();

        expect($published->refresh()->title)->toBe('Updated title')
            ->and($draft->refresh()->title)->toBe('Draft title');
    });

    it('resolves the default relationship persistence target after `beforeStateUpdated()`', function (bool $shouldAssociate): void {
        $originalAuthor = User::factory()->create(['name' => 'Original author']);
        $replacementAuthor = User::factory()->create(['name' => 'Replacement author']);
        $post = Post::factory()->create(['author_id' => $originalAuthor->getKey()]);
        $afterRelatedRecord = null;

        $column = TextInputColumn::make('author.name')
            ->record($post)
            ->beforeStateUpdated(static function (User $relatedRecord) use ($post, $replacementAuthor, $shouldAssociate): void {
                expect($relatedRecord->is($post->author))->toBeTrue();

                if ($shouldAssociate) {
                    $post->author()->associate($replacementAuthor);
                } else {
                    $post->author_id = $replacementAuthor->getKey();
                }

                $post->save();
            })
            ->afterStateUpdated(static function (User $relatedRecord) use (&$afterRelatedRecord): void {
                $afterRelatedRecord = $relatedRecord;
            });

        expect($column->updateState('Updated replacement'))->toBe('Updated replacement')
            ->and($originalAuthor->refresh()->name)->toBe('Original author')
            ->and($replacementAuthor->refresh()->name)->toBe('Updated replacement')
            ->and($afterRelatedRecord)->toBeInstanceOf(User::class)
            ->and($afterRelatedRecord->is($replacementAuthor))->toBeTrue();
    })->with([false, true]);

    it('persists a missing relationship target created by `beforeStateUpdated()`', function (): void {
        $author = User::factory()->create();

        $column = TextInputColumn::make('publishedPost.title')
            ->record($author)
            ->beforeStateUpdated(static function (?Post $relatedRecord) use ($author): void {
                expect($relatedRecord)->toBeNull();

                $author->publishedPost()->create([
                    'title' => 'Created title',
                    'is_published' => true,
                ]);
            });

        expect($column->updateState('Updated title'))->toBe('Updated title')
            ->and($author->fresh()->publishedPost->title)->toBe('Updated title');
    });

    it('recomputes a nested `MorphTo` persistence path after `beforeStateUpdated()`', function (): void {
        $team = Team::factory()->create(['name' => 'Original team']);
        $user = User::factory()->create(['team_id' => $team->getKey()]);
        $profile = Profile::factory()->create();
        $image = Image::factory()->for($profile, 'imageable')->create();
        $afterRelatedRecord = null;

        $column = TextInputColumn::make('imageable.team.name')
            ->record($image)
            ->beforeStateUpdated(static function () use ($image, $user): void {
                $image->imageable()->associate($user);
                $image->save();
            })
            ->afterStateUpdated(static function (Team $relatedRecord) use (&$afterRelatedRecord): void {
                $afterRelatedRecord = $relatedRecord;
            });

        expect($column->updateState('Updated team'))->toBe('Updated team')
            ->and($team->refresh()->name)->toBe('Updated team')
            ->and($afterRelatedRecord)->toBeInstanceOf(Team::class)
            ->and($afterRelatedRecord->is($team))->toBeTrue();
    });
});

describe('type', function (): void {
    it('can set `type()` and get with `getType()`', function (): void {
        expect(TextInputColumn::make('rating')->type('number')->getType())->toBe('number');
    });

    it('defaults `getType()` to `text`', function (): void {
        expect(TextInputColumn::make('rating')->getType())->toBe('text');
    });

    it('can set `type()` with a `Closure`', function (): void {
        expect(TextInputColumn::make('rating')->type(static fn (): string => 'email')->getType())->toBe('email');
    });
});

describe('mask', function (): void {
    it('can set `mask()` and get with `getMask()`', function (): void {
        expect(TextInputColumn::make('phone')->mask('999-999-9999')->getMask())->toBe('999-999-9999');
    });

    it('defaults `getMask()` to `null`', function (): void {
        expect(TextInputColumn::make('rating')->getMask())->toBeNull();
    });

    it('can set `mask()` with a `Closure`', function (): void {
        expect(TextInputColumn::make('phone')->mask(static fn (): string => '(999) 999-9999')->getMask())->toBe('(999) 999-9999');
    });

    it('can set `mask()` with `RawJs`', function (): void {
        $column = TextInputColumn::make('price')
            ->mask(RawJs::make('$money($input)'));

        expect($column->getMask())->toBeInstanceOf(RawJs::class);
    });
});

describe('affixes', function (): void {
    it('can set `prefix()` and get with `getPrefixLabel()`', function (): void {
        expect(TextInputColumn::make('price')->prefix('$')->getPrefixLabel())->toBe('$');
    });

    it('defaults `getPrefixLabel()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getPrefixLabel())->toBeNull();
    });

    it('can set `suffix()` and get with `getSuffixLabel()`', function (): void {
        expect(TextInputColumn::make('price')->suffix('USD')->getSuffixLabel())->toBe('USD');
    });

    it('defaults `getSuffixLabel()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getSuffixLabel())->toBeNull();
    });

    it('can set `inlinePrefix()` and get with `isPrefixInline()`', function (): void {
        expect(TextInputColumn::make('price')->inlinePrefix()->isPrefixInline())->toBeTrue();
    });

    it('defaults `isPrefixInline()` to `false`', function (): void {
        expect(TextInputColumn::make('price')->isPrefixInline())->toBeFalse();
    });

    it('can set `inlineSuffix()` and get with `isSuffixInline()`', function (): void {
        expect(TextInputColumn::make('price')->inlineSuffix()->isSuffixInline())->toBeTrue();
    });

    it('defaults `isSuffixInline()` to `false`', function (): void {
        expect(TextInputColumn::make('price')->isSuffixInline())->toBeFalse();
    });

    it('can set `prefixIcon()` and get with `getPrefixIcon()`', function (): void {
        expect(TextInputColumn::make('price')->prefixIcon('heroicon-o-currency-dollar')->getPrefixIcon())->toBe('heroicon-o-currency-dollar');
    });

    it('defaults `getPrefixIcon()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getPrefixIcon())->toBeNull();
    });

    it('can set `suffixIcon()` and get with `getSuffixIcon()`', function (): void {
        expect(TextInputColumn::make('price')->suffixIcon('heroicon-o-check')->getSuffixIcon())->toBe('heroicon-o-check');
    });

    it('defaults `getSuffixIcon()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getSuffixIcon())->toBeNull();
    });

    it('can set `prefixIconColor()` and get with `getPrefixIconColor()`', function (): void {
        expect(TextInputColumn::make('price')->prefixIconColor('success')->getPrefixIconColor())->toBe('success');
    });

    it('defaults `getPrefixIconColor()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getPrefixIconColor())->toBeNull();
    });

    it('can set `suffixIconColor()` and get with `getSuffixIconColor()`', function (): void {
        expect(TextInputColumn::make('price')->suffixIconColor('danger')->getSuffixIconColor())->toBe('danger');
    });

    it('defaults `getSuffixIconColor()` to `null`', function (): void {
        expect(TextInputColumn::make('price')->getSuffixIconColor())->toBeNull();
    });

    it('can set `prefix()` with inline and get `isPrefixInline()`', function (): void {
        $column = TextInputColumn::make('price')
            ->prefix('$', isInline: true);

        expect($column->getPrefixLabel())->toBe('$');
        expect($column->isPrefixInline())->toBeTrue();
    });

    it('can set `suffix()` with inline and get `isSuffixInline()`', function (): void {
        $column = TextInputColumn::make('price')
            ->suffix('USD', isInline: true);

        expect($column->getSuffixLabel())->toBe('USD');
        expect($column->isSuffixInline())->toBeTrue();
    });

    it('can set `prefixIcon()` with inline', function (): void {
        $column = TextInputColumn::make('price')
            ->prefixIcon('heroicon-o-dollar', isInline: true);

        expect($column->getPrefixIcon())->toBe('heroicon-o-dollar');
        expect($column->isPrefixInline())->toBeTrue();
    });

    it('can set `prefix()` with a `Closure`', function (): void {
        expect(TextInputColumn::make('price')->prefix(static fn (): string => '€')->getPrefixLabel())->toBe('€');
    });

    it('can set `suffix()` with a `Closure`', function (): void {
        expect(TextInputColumn::make('price')->suffix(static fn (): string => 'EUR')->getSuffixLabel())->toBe('EUR');
    });

    it('can set `prefixIconColor()` with a `Closure`', function (): void {
        expect(TextInputColumn::make('price')->prefixIconColor(static fn (): string => 'info')->getPrefixIconColor())->toBe('info');
    });
});

describe('rendering values', function (): void {
    it('can render', function (): void {
        Post::factory()->count(5)->create();

        livewire(TestTableWithTextInputColumn::class)
            ->assertSuccessful()
            ->assertCanRenderTableColumn('rating');
    });

    it('can display different values', function (): void {
        Post::factory()->create(['rating' => 1]);
        Post::factory()->create(['rating' => 5]);
        Post::factory()->create(['rating' => 10]);

        livewire(TestTableWithTextInputColumn::class)
            ->assertSuccessful();
    });
});

describe('rendering', function (): void {
    it('can render with `type()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithType::class)->assertSuccessful();
    });

    it('can render with `type()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureType::class)->assertSuccessful();
    });

    it('can render with `mask()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithMask::class)->assertSuccessful();
    });

    it('can render with `mask()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureMask::class)->assertSuccessful();
    });

    it('can render with `mask()` using `RawJs`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithRawJsMask::class)->assertSuccessful();
    });

    it('can render with `prefix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefix::class)->assertSuccessful();
    });

    it('can render with `prefix()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosurePrefix::class)->assertSuccessful();
    });

    it('can render with `prefix()` inline', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlinePrefix::class)->assertSuccessful();
    });

    it('can render with `suffix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffix::class)->assertSuccessful();
    });

    it('can render with `suffix()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosureSuffix::class)->assertSuccessful();
    });

    it('can render with `suffix()` inline', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlineSuffix::class)->assertSuccessful();
    });

    it('can render with `prefixIcon()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefixIcon::class)->assertSuccessful();
    });

    it('can render with `suffixIcon()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffixIcon::class)->assertSuccessful();
    });

    it('can render with `prefixIconColor()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithPrefixIconColor::class)->assertSuccessful();
    });

    it('can render with `prefixIconColor()` set via `Closure`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithClosurePrefixIconColor::class)->assertSuccessful();
    });

    it('can render with `suffixIconColor()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithSuffixIconColor::class)->assertSuccessful();
    });

    it('can render with `inlinePrefix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlinePrefixMethod::class)->assertSuccessful();
    });

    it('can render with `inlineSuffix()`', function (): void {
        Post::factory()->create();
        livewire(RenderTextInputColumnWithInlineSuffixMethod::class)->assertSuccessful();
    });
});

class TestTableWithTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                TextInputColumn::make('rating'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithConstrainedSingularRelationshipTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public bool $hasBeforeStateUpdatedCallback = false;

    public string $constraintSource = 'query';

    public function table(Table $table): Table
    {
        $applyEagerLoads = static fn (Builder $query): Builder => $query->with([
            'publishedPost' => static fn ($query) => $query->where('rating', '>=', 5)->orderBy('id'),
        ]);

        return $table
            ->query(match ($this->constraintSource) {
                'global-scope' => User::query()->withGlobalScope('publishedPostRating', $applyEagerLoads),
                'filter' => User::query(),
                default => $applyEagerLoads(User::query()),
            })
            ->filters($this->constraintSource === 'filter' ? [
                Tables\Filters\Filter::make('rating')->default()->baseQuery($applyEagerLoads),
            ] : [])
            ->columns([
                TextInputColumn::make('publishedPost.title')
                    ->disabled(static fn (User $record): bool => ($record->publishedPost?->rating ?? 5) < 5)
                    ->beforeStateUpdated($this->hasBeforeStateUpdatedCallback ? static function (?Post $relatedRecord): void {
                        expect($relatedRecord === null || $relatedRecord->rating >= 5)->toBeTrue();
                    } : null),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithNestedConstrainedRelationshipTextInputColumn extends TestTableWithConstrainedSingularRelationshipTextInputColumn
{
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with([
                'profile' => static fn ($query) => $query->where('bio', 'Included profile'),
            ]))
            ->columns([
                TextInputColumn::make('profile.user.name'),
            ]);
    }
}

class TestTableWithConstrainedRelationshipTextInputColumn extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with([
                'posts' => static fn ($query) => $query->where('is_published', true)->orderBy('id'),
            ]))
            ->columns([
                TextInputColumn::make('posts.title')
                    ->state(static fn (User $record): ?string => $record->posts->first()?->title)
                    ->updateStateUsing(static function (User $record, string $state): string {
                        $record->posts->first()->update(['title' => $state]);

                        return $state;
                    }),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithType extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->type('number'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureType extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->type(static fn (): string => 'email'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask('999-999-9999'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask(static fn (): string => '(999) 999-9999'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithRawJsMask extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('title')->mask(RawJs::make('$money($input)')),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosurePrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix(static fn (): string => '€'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlinePrefix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$', isInline: true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosureSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix(static fn (): string => 'EUR'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlineSuffix extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD', isInline: true),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefixIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffixIcon extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffixIcon('heroicon-o-check'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithPrefixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar')->prefixIconColor('success'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithClosurePrefixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefixIcon('heroicon-o-currency-dollar')->prefixIconColor(static fn (): string => 'info'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithSuffixIconColor extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffixIcon('heroicon-o-check')->suffixIconColor('danger'),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlinePrefixMethod extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->prefix('$')->inlinePrefix(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RenderTextInputColumnWithInlineSuffixMethod extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table->query(Post::query())->columns([
            TextInputColumn::make('rating')->suffix('USD')->inlineSuffix(),
        ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
