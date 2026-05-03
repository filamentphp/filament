<?php

namespace Filament\Tests\Tables\Filters;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Livewire\Livewire as TestLivewireFixture;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('filtering records', function (): void {
    it('can render table with `SelectFilter`', function (): void {
        Post::factory()->count(5)->create();

        livewire(TestTableWithSelectFilter::class)
            ->assertSuccessful();
    });

    it('can filter records by relationship', function (): void {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();

        $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);

        livewire(TestTableWithSelectFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
            ->filterTable('author', $author1->getKey())
            ->assertCanSeeTableRecords($postsWithAuthor1)
            ->assertCanNotSeeTableRecords($postsWithAuthor2);
    });

    it('can filter records by attribute options', function (): void {
        $postsWithRating1 = Post::factory()->count(2)->create(['rating' => 1]);
        $postsWithRating5 = Post::factory()->count(3)->create(['rating' => 5]);

        livewire(TestTableWithSelectFilter::class)
            ->assertCanSeeTableRecords($postsWithRating1->merge($postsWithRating5))
            ->filterTable('rating', 1)
            ->assertCanSeeTableRecords($postsWithRating1)
            ->assertCanNotSeeTableRecords($postsWithRating5);
    });

    it('can reset `SelectFilter` to show all records', function (): void {
        $author = User::factory()->create();

        $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithSelectFilter::class)
            ->filterTable('author', $author->getKey())
            ->assertCanNotSeeTableRecords($postsWithoutAuthor)
            ->resetTableFilters()
            ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor));
    });

    it('can filter records with no relationship using `hasEmptyRelationshipOption`', function (): void {
        $author = User::factory()->create();

        $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithEmptyRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
            ->filterTable('author', '__empty')
            ->assertCanSeeTableRecords($postsWithoutAuthor)
            ->assertCanNotSeeTableRecords($postsWithAuthor);
    });

    it('can filter records by specific relationship value using `hasEmptyRelationshipOption`', function (): void {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();

        $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithEmptyRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
            ->filterTable('author', $author1->getKey())
            ->assertCanSeeTableRecords($postsWithAuthor1)
            ->assertCanNotSeeTableRecords($postsWithAuthor2)
            ->assertCanNotSeeTableRecords($postsWithoutAuthor);
    });

    it('can filter records with no relationship using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
        $author = User::factory()->create();

        $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
            ->filterTable('author', ['__empty'])
            ->assertCanSeeTableRecords($postsWithoutAuthor)
            ->assertCanNotSeeTableRecords($postsWithAuthor);
    });

    it('can filter records by specific relationship values using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();
        $author3 = User::factory()->create();

        $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
        $postsWithAuthor3 = Post::factory()->count(2)->create(['author_id' => $author3->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithAuthor3)->merge($postsWithoutAuthor))
            ->filterTable('author', [$author1->getKey(), $author2->getKey()])
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
            ->assertCanNotSeeTableRecords($postsWithAuthor3)
            ->assertCanNotSeeTableRecords($postsWithoutAuthor);
    });

    it('can filter records by relationship values combined with empty option using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();

        $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
        $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

        livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
            ->filterTable('author', ['__empty', $author1->getKey()])
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithoutAuthor))
            ->assertCanNotSeeTableRecords($postsWithAuthor2);
    });
});

class TestTableWithSelectFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextColumn::make('author.name'),
                Tables\Columns\TextColumn::make('rating'),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name'),
                SelectFilter::make('rating')
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

class TestTableWithEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextColumn::make('author.name'),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithMultipleEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                Tables\Columns\TextColumn::make('author.name'),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true)
                    ->multiple(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

describe('options and properties', function (): void {
    it('can get `getOptions()` from static array', function (): void {
        $filter = SelectFilter::make('status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]);

        expect($filter->getOptions())->toBe([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
    });

    it('can get `getOptions()` from closure', function (): void {
        $filter = SelectFilter::make('status')
            ->options(fn (): array => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]);

        expect($filter->getOptions())->toBe([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
    });

    it('can get `getOptions()` from enum class string', function (): void {
        $filter = SelectFilter::make('status')
            ->options(StringBackedEnum::class);

        $options = $filter->getOptions();

        expect($options)->toBe([
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ]);
    });

    it('returns empty array for `getOptions()` when no options set', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getOptions())->toBe([]);
    });

    it('can check `isMultiple()` returns correct value', function (): void {
        $singleFilter = SelectFilter::make('status');
        $multipleFilter = SelectFilter::make('status')->multiple();

        expect($singleFilter->isMultiple())->toBeFalse();
        expect($multipleFilter->isMultiple())->toBeTrue();
    });

    it('can check `isSearchable()` returns correct value', function (): void {
        $nonSearchableFilter = SelectFilter::make('status');
        $searchableFilter = SelectFilter::make('status')->searchable();

        expect($nonSearchableFilter->getSearchable())->toBeFalse();
        expect($searchableFilter->getSearchable())->toBeTrue();
    });

    it('can get options limit using `getOptionsLimit()`', function (): void {
        $defaultFilter = SelectFilter::make('status');
        $limitedFilter = SelectFilter::make('status')->optionsLimit(100);

        expect($defaultFilter->getOptionsLimit())->toBe(50);
        expect($limitedFilter->getOptionsLimit())->toBe(100);
    });

    it('can check `queriesRelationships()` returns `true` when relationship is set', function (): void {
        $withoutRelationship = SelectFilter::make('status')
            ->options(['active' => 'Active']);

        $withRelationship = SelectFilter::make('author')
            ->relationship('author', 'name');

        expect($withoutRelationship->queriesRelationships())->toBeFalse();
        expect($withRelationship->queriesRelationships())->toBeTrue();
    });

    it('can get `getRelationshipName()` from string', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship('author', 'name');

        expect($filter->getRelationshipName())->toBe('author');
    });

    it('can get `getRelationshipName()` from closure', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship(fn (): string => 'author', 'name');

        expect($filter->getRelationshipName())->toBe('author');
    });

    it('can get `getRelationshipTitleAttribute()`', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship('author', 'name');

        expect($filter->getRelationshipTitleAttribute())->toBe('name');
    });

    it('can check `hasEmptyRelationshipOption()` returns `true` when `hasEmptyOption` is set', function (): void {
        $withoutEmptyOption = SelectFilter::make('author')
            ->relationship('author', 'name');

        $withEmptyOption = SelectFilter::make('author')
            ->relationship('author', 'name', hasEmptyOption: true);

        expect($withoutEmptyOption->hasEmptyRelationshipOption())->toBeFalse();
        expect($withEmptyOption->hasEmptyRelationshipOption())->toBeTrue();
    });

    it('can get default empty relationship option label', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship('author', 'name', hasEmptyOption: true);

        expect($filter->getEmptyRelationshipOptionLabel())->toBe(__('filament-tables::table.filters.select.relationship.empty_option_label'));
    });

    it('can get custom empty relationship option label', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship('author', 'name', hasEmptyOption: true)
            ->emptyRelationshipOptionLabel('No Author');

        expect($filter->getEmptyRelationshipOptionLabel())->toBe('No Author');
    });

    it('can check `isPreloaded()` returns correct value', function (): void {
        $notPreloaded = SelectFilter::make('author')
            ->relationship('author', 'name');

        $preloaded = SelectFilter::make('author')
            ->relationship('author', 'name')
            ->preload();

        expect($notPreloaded->isPreloaded())->toBeFalse();
        expect($preloaded->isPreloaded())->toBeTrue();
    });

    it('can check `isNative()` returns correct value', function (): void {
        $nativeFilter = SelectFilter::make('status');
        $nonNativeFilter = SelectFilter::make('status')->native(false);

        expect($nativeFilter->isNative())->toBeTrue();
        expect($nonNativeFilter->isNative())->toBeFalse();
    });

    it('can check `canSelectPlaceholder()` returns correct value', function (): void {
        $withPlaceholder = SelectFilter::make('status');
        $withoutPlaceholder = SelectFilter::make('status')->selectablePlaceholder(false);

        expect($withPlaceholder->canSelectPlaceholder())->toBeTrue();
        expect($withoutPlaceholder->canSelectPlaceholder())->toBeFalse();
    });

    it('can get `getAttribute()` returns filter name by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getAttribute())->toBe('status');
    });

    it('can get custom `getAttribute()`', function (): void {
        $filter = SelectFilter::make('status')
            ->attribute('custom_status');

        expect($filter->getAttribute())->toBe('custom_status');
    });

    it('can set `forceSearchCaseInsensitive()` and get with `isSearchForcedCaseInsensitive()`', function (): void {
        $filter = SelectFilter::make('status')->forceSearchCaseInsensitive();

        expect($filter->isSearchForcedCaseInsensitive())->toBeTrue();
    });

    it('can set `forceSearchCaseInsensitive()` to `false` and get with `isSearchForcedCaseInsensitive()`', function (): void {
        $filter = SelectFilter::make('status')->forceSearchCaseInsensitive(false);

        expect($filter->isSearchForcedCaseInsensitive())->toBeFalse();
    });

    it('defaults `isSearchForcedCaseInsensitive()` to `null`', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->isSearchForcedCaseInsensitive())->toBeNull();
    });
});

describe('form field generation', function (): void {
    it('can get `getFormField()` returns `Select` component', function (): void {
        $filter = SelectFilter::make('status')
            ->options(['active' => 'Active', 'inactive' => 'Inactive']);

        $formField = $filter->getFormField();

        expect($formField)->toBeInstanceOf(Select::class);
        expect($formField->getName())->toBe('value');
    });

    it('can get `getFormField()` returns `Select` component with `values` name for multiple filter', function (): void {
        $filter = SelectFilter::make('status')
            ->multiple()
            ->options(['active' => 'Active', 'inactive' => 'Inactive']);

        $formField = $filter->getFormField();

        expect($formField)->toBeInstanceOf(Select::class);
        expect($formField->getName())->toBe('values');
        expect($formField->isMultiple())->toBeTrue();
    });

    it('can get `getFormField()` with relationship configuration', function (): void {
        $filter = SelectFilter::make('author')
            ->relationship('author', 'name')
            ->searchable()
            ->preload();

        $formField = $filter->getFormField();

        expect($formField)->toBeInstanceOf(Select::class);
        expect($formField->isSearchable())->toBeTrue();
        expect($formField->isPreloaded())->toBeTrue();
    });

    it('returns empty options when relationship is searchable without preload via `getFormField()`', function (): void {
        User::factory()->count(3)->create();

        livewire(TestTableWithSearchableRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter): bool {
                $formField = $filter->getFormField();

                expect($formField->getOptions())->toBe([]);

                return true;
            });
    });
});

describe('relationship filtering', function (): void {
    it('can filter records by relationship with preload', function (): void {
        $author1 = User::factory()->create();
        $author2 = User::factory()->create();

        $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);

        livewire(TestTableWithPreloadedRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
            ->filterTable('author', $author1->getKey())
            ->assertCanSeeTableRecords($postsWithAuthor1)
            ->assertCanNotSeeTableRecords($postsWithAuthor2);
    });

    it('can filter records by relationship with `modifyQueryUsing`', function (): void {
        $author1 = User::factory()->create(['name' => 'Alpha User']);
        $author2 = User::factory()->create(['name' => 'Beta User']);

        $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);

        livewire(TestTableWithModifiedRelationshipFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
            ->filterTable('author', $author1->getKey())
            ->assertCanSeeTableRecords($postsWithAuthor1)
            ->assertCanNotSeeTableRecords($postsWithAuthor2);
    });

    it('can filter records by relationship with custom option labels', function (): void {
        $author1 = User::factory()->create(['name' => 'John', 'email' => 'john@example.com']);
        $author2 = User::factory()->create(['name' => 'Jane', 'email' => 'jane@example.com']);

        $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
        $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);

        livewire(TestTableWithCustomRelationshipLabelFilter::class)
            ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
            ->filterTable('author', $author1->getKey())
            ->assertCanSeeTableRecords($postsWithAuthor1)
            ->assertCanNotSeeTableRecords($postsWithAuthor2);
    });

    class TestTableWithSearchableRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                    Tables\Columns\TextColumn::make('author.name'),
                ])
                ->filters([
                    SelectFilter::make('author')
                        ->relationship('author', 'name')
                        ->searchable(),
                ]);
        }

        public function render(): View
        {
            return view('livewire.table');
        }
    }

    class TestTableWithPreloadedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                    Tables\Columns\TextColumn::make('author.name'),
                ])
                ->filters([
                    SelectFilter::make('author')
                        ->relationship('author', 'name')
                        ->preload(),
                ]);
        }

        public function render(): View
        {
            return view('livewire.table');
        }
    }

    class TestTableWithModifiedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                    Tables\Columns\TextColumn::make('author.name'),
                ])
                ->filters([
                    SelectFilter::make('author')
                        ->relationship(
                            'author',
                            'name',
                            modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                        )
                        ->preload(),
                ]);
        }

        public function render(): View
        {
            return view('livewire.table');
        }
    }

    class TestTableWithCustomRelationshipLabelFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                    Tables\Columns\TextColumn::make('author.name'),
                ])
                ->filters([
                    SelectFilter::make('author')
                        ->relationship('author', 'name')
                        ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                        ->preload(),
                ]);
        }

        public function render(): View
        {
            return view('livewire.table');
        }
    }
});

it('can set `multiple()` with a `Closure`', function (): void {
    $filter = SelectFilter::make('status')
        ->multiple(static fn (): bool => true);

    expect($filter->isMultiple())->toBeTrue();
});

it('can set `native()` with a `Closure`', function (): void {
    $filter = SelectFilter::make('status')
        ->native(static fn (): bool => false);

    expect($filter->isNative())->toBeFalse();
});

it('can set `optionsLimit()` with a `Closure`', function (): void {
    $filter = SelectFilter::make('status')
        ->optionsLimit(static fn (): int => 200);

    expect($filter->getOptionsLimit())->toBe(200);
});

it('can set `static()` to prevent query modification', function (): void {
    $filter = SelectFilter::make('status');

    $result = $filter->static();

    expect($result)->toBe($filter);
});

it('can set deprecated `column()` as alias for `attribute()`', function (): void {
    $filter = SelectFilter::make('status')
        ->column('custom_column');

    expect($filter->getAttribute())->toBe('custom_column');
});

it('returns fluent `$this` from `getOptionLabelFromRecordUsing()`', function (): void {
    $filter = SelectFilter::make('author');

    $result = $filter->getOptionLabelFromRecordUsing(static fn ($record): string => $record->name);

    expect($result)->toBe($filter);
});

describe('Closure support', function (): void {
    it('can set `attribute()` with a `Closure`', function (): void {
        $filter = SelectFilter::make('status')
            ->attribute(static fn (): string => 'custom_col');

        expect($filter->getAttribute())->toBe('custom_col');
    });

    it('can set `selectablePlaceholder()` with a `Closure`', function (): void {
        $filter = SelectFilter::make('status')
            ->selectablePlaceholder(static fn (): bool => false);

        expect($filter->canSelectPlaceholder())->toBeFalse();
    });

    it('can set `searchable()` with a `Closure`', function (): void {
        $filter = SelectFilter::make('status')
            ->searchable(static fn (): bool => true);

        expect($filter->getSearchable())->toBeTrue();
    });

    it('can set `placeholder()` with a `Closure`', function (): void {
        $filter = SelectFilter::make('status')
            ->placeholder(static fn (): string => 'Pick one...');

        expect($filter->getPlaceholder())->toBe('Pick one...');
    });
});

describe('option callbacks', function (): void {
    it('returns `null` for `getOptionLabelUsingCallback()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getOptionLabelUsingCallback())->toBeNull();
    });

    it('returns callback from `getOptionLabelUsingCallback()` when set', function (): void {
        $callback = static fn (): string => 'label';
        $filter = SelectFilter::make('status')
            ->getOptionLabelUsing($callback);

        expect($filter->getOptionLabelUsingCallback())->toBe($callback);
    });

    it('returns `null` for `getOptionLabelsUsingCallback()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getOptionLabelsUsingCallback())->toBeNull();
    });

    it('returns callback from `getOptionLabelsUsingCallback()` when set', function (): void {
        $callback = static fn (): array => [];
        $filter = SelectFilter::make('status')
            ->getOptionLabelsUsing($callback);

        expect($filter->getOptionLabelsUsingCallback())->toBe($callback);
    });

    it('returns `null` for `getSearchResultsUsingCallback()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getSearchResultsUsingCallback())->toBeNull();
    });

    it('returns callback from `getSearchResultsUsingCallback()` when set', function (): void {
        $callback = static fn (): array => [];
        $filter = SelectFilter::make('status')
            ->getSearchResultsUsing($callback);

        expect($filter->getSearchResultsUsingCallback())->toBe($callback);
    });
});

describe('relationship defaults', function (): void {
    it('returns `false` for `queriesRelationships()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->queriesRelationships())->toBeFalse();
    });

    it('returns `null` for `getRelationshipName()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getRelationshipName())->toBeNull();
    });

    it('returns `null` for `getRelationshipTitleAttribute()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getRelationshipTitleAttribute())->toBeNull();
    });

    it('returns `null` for `getModifyRelationshipQueryUsing()` by default', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getModifyRelationshipQueryUsing())->toBeNull();
    });
});

describe('placeholder', function (): void {
    it('returns a default translated placeholder', function (): void {
        $filter = SelectFilter::make('status');

        expect($filter->getPlaceholder())->toBeString()->not->toBeEmpty();
    });
});

describe('relationship branch coverage', function (): void {
    /**
     * Wires the SelectFilter form field to a Schema with the table's model so that
     * relationship-based methods like `getOptions()` can resolve the model instance.
     */
    $wireField = function (SelectFilter $filter, string $modelClass = Post::class): Select {
        return $filter->getFormField()->container(
            Schema::make(TestLivewireFixture::make())->model($modelClass),
        );
    };

    it('returns relationship options via `getOptionsFromRelationship()` when preloaded', function () use ($wireField): void {
        $author1 = User::factory()->create(['name' => 'Alpha']);
        $author2 = User::factory()->create(['name' => 'Beta']);

        livewire(TestTableWithPreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author1, $author2, $wireField): bool {
                $select = $wireField($filter);

                $options = $filter->getOptionsFromRelationship($select);

                expect($options)->toMatchArray([
                    $author1->getKey() => 'Alpha',
                    $author2->getKey() => 'Beta',
                ]);

                return true;
            });
    });

    it('returns `null` from `getOptionsFromRelationship()` when searchable without preload', function () use ($wireField): void {
        User::factory()->count(3)->create();

        livewire(TestTableWithSearchableRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                expect($filter->getOptionsFromRelationship($select))->toBeNull();

                return true;
            });
    });

    it('includes the empty relationship option key in `getOptionsFromRelationship()` when `hasEmptyOption` is set', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha']);

        livewire(TestTableWithPreloadedEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $options = $filter->getOptionsFromRelationship($select);

                expect($options)->toHaveKey('__empty');
                expect($options[$author->getKey()])->toBe('Alpha');

                return true;
            });
    });

    it('uses `getOptionLabelFromRecordUsing()` callback inside `getOptionsFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'email' => 'a@example.com']);

        livewire(TestTableWithCustomLabelPreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $options = $filter->getOptionsFromRelationship($select);

                expect($options[$author->getKey()])->toBe('Alpha (a@example.com)');

                return true;
            });
    });

    it('respects a custom limit set in `modifyQueryUsing()` for `getOptionsFromRelationship()`', function () use ($wireField): void {
        User::factory()->count(20)->create();

        livewire(TestTableWithLimitedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                expect($filter->getOptionsFromRelationship($select))->toHaveCount(3);
                expect($filter->getOptionsLimit())->toBe(3);

                return true;
            });
    });

    it('preserves an existing `orderBy` from `modifyQueryUsing()` in `getOptionsFromRelationship()`', function () use ($wireField): void {
        User::factory()->create(['name' => 'Beta']);
        User::factory()->create(['name' => 'Alpha']);
        User::factory()->create(['name' => 'Gamma']);

        livewire(TestTableWithOrderedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $options = $filter->getOptionsFromRelationship($select);

                expect(array_values($options))->toBe(['Gamma', 'Beta', 'Alpha']);

                return true;
            });
    });

    it('handles JSON path `titleAttribute` in `getOptionsFromRelationship()`', function () use ($wireField): void {
        User::factory()->create(['name' => 'Alpha', 'json' => ['nickname' => 'Ace']]);
        User::factory()->create(['name' => 'Beta', 'json' => ['nickname' => 'Bee']]);

        livewire(TestTableWithJsonPathRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $options = $filter->getOptionsFromRelationship($select);

                expect(array_values($options))->toEqualCanonicalizing(['Ace', 'Bee']);

                return true;
            });
    });

    it('returns search results via `getSearchResultsFromRelationship()` matching `applySearchConstraint`', function () use ($wireField): void {
        User::factory()->create(['name' => 'Alpha']);
        User::factory()->create(['name' => 'Beta']);
        User::factory()->create(['name' => 'Aleph']);

        livewire(TestTableWithSearchableRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $results = $filter->getSearchResultsFromRelationship($select, 'Al');

                expect(array_values($results))->toEqualCanonicalizing(['Alpha', 'Aleph']);

                return true;
            });
    });

    it('adds the empty relationship option key in `getSearchResultsFromRelationship()` when search matches the empty option label', function () use ($wireField): void {
        User::factory()->create(['name' => 'Alpha']);

        livewire(TestTableWithSearchableEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);
                $emptyLabel = $filter->getEmptyRelationshipOptionLabel();

                $results = $filter->getSearchResultsFromRelationship($select, $emptyLabel);

                expect($results)->toHaveKey('__empty');

                return true;
            });
    });

    it('omits the empty relationship option key in `getSearchResultsFromRelationship()` when search does not match', function () use ($wireField): void {
        User::factory()->create(['name' => 'Alpha']);

        livewire(TestTableWithSearchableEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $results = $filter->getSearchResultsFromRelationship($select, 'Alpha');

                expect($results)->not->toHaveKey('__empty');

                return true;
            });
    });

    it('uses `getOptionLabelFromRecordUsing()` callback inside `getSearchResultsFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'email' => 'a@example.com']);

        livewire(TestTableWithCustomLabelSearchableRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $results = $filter->getSearchResultsFromRelationship($select, 'Alpha');

                expect($results[$author->getKey()])->toBe('Alpha (a@example.com)');

                return true;
            });
    });

    it('respects a custom limit set in `modifyQueryUsing()` for `getSearchResultsFromRelationship()`', function () use ($wireField): void {
        User::factory()->count(10)->create(['name' => 'Match Me']);

        livewire(TestTableWithLimitedSearchableRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $results = $filter->getSearchResultsFromRelationship($select, 'Match');

                expect($results)->toHaveCount(2);
                expect($filter->getOptionsLimit())->toBe(2);

                return true;
            });
    });

    it('returns the empty option label from `getOptionLabelFromRelationship()` when state is the empty option key', function () use ($wireField): void {
        livewire(TestTableWithPreloadedEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);
                $select->state('__empty');

                expect($filter->getOptionLabelFromRelationship($select))->toBe($filter->getEmptyRelationshipOptionLabel());

                return true;
            });
    });

    it('returns `null` from `getOptionLabelFromRelationship()` when no record is selected', function () use ($wireField): void {
        livewire(TestTableWithPreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);
                $select->state('999999');

                expect($filter->getOptionLabelFromRelationship($select))->toBeNull();

                return true;
            });
    });

    it('returns the title attribute via `data_get` in `getOptionLabelFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha']);

        livewire(TestTableWithPreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);
                $select->state((string) $author->getKey());

                expect($filter->getOptionLabelFromRelationship($select))->toBe('Alpha');

                return true;
            });
    });

    it('uses `getOptionLabelFromRecordUsing()` callback inside `getOptionLabelFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'email' => 'a@example.com']);

        livewire(TestTableWithCustomLabelPreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);
                $select->state((string) $author->getKey());

                expect($filter->getOptionLabelFromRelationship($select))->toBe('Alpha (a@example.com)');

                return true;
            });
    });

    it('handles JSON path `titleAttribute` in `getOptionLabelFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'json' => ['nickname' => 'Ace']]);

        livewire(TestTableWithJsonPathRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);
                $select->state((string) $author->getKey());

                expect($filter->getOptionLabelFromRelationship($select))->toBe('Ace');

                return true;
            });
    });

    it('returns labels for matching keys via `getOptionLabelsFromRelationship()`', function () use ($wireField): void {
        $author1 = User::factory()->create(['name' => 'Alpha']);
        $author2 = User::factory()->create(['name' => 'Beta']);

        livewire(TestTableWithMultiplePreloadedRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author1, $author2, $wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, [(string) $author1->getKey(), (string) $author2->getKey()]);

                expect($labels)->toMatchArray([
                    $author1->getKey() => 'Alpha',
                    $author2->getKey() => 'Beta',
                ]);

                return true;
            });
    });

    it('adds the empty option label inside `getOptionLabelsFromRelationship()` when the empty key is in values', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha']);

        livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, ['__empty', (string) $author->getKey()]);

                expect($labels)->toHaveKey('__empty');
                expect($labels[$author->getKey()])->toBe('Alpha');

                return true;
            });
    });

    it('filters out the empty option key from query values inside `getOptionLabelsFromRelationship()`', function () use ($wireField): void {
        livewire(TestTableWithMultipleEmptyRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, ['__empty']);

                expect($labels)->toBe(['__empty' => $filter->getEmptyRelationshipOptionLabel()]);

                return true;
            });
    });

    it('uses `getOptionLabelFromRecordUsing()` callback inside `getOptionLabelsFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'email' => 'a@example.com']);

        livewire(TestTableWithCustomLabelMultipleRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, [(string) $author->getKey()]);

                expect($labels[$author->getKey()])->toBe('Alpha (a@example.com)');

                return true;
            });
    });

    it('handles JSON path `titleAttribute` in `getOptionLabelsFromRelationship()`', function () use ($wireField): void {
        $author = User::factory()->create(['name' => 'Alpha', 'json' => ['nickname' => 'Ace']]);

        livewire(TestTableWithJsonPathMultipleRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author, $wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, [(string) $author->getKey()]);

                expect($labels[$author->getKey()])->toBe('Ace');

                return true;
            });
    });

    it('applies `modifyQueryUsing()` inside `getOptionLabelsFromRelationship()`', function () use ($wireField): void {
        $author1 = User::factory()->create(['name' => 'Alpha']);
        $author2 = User::factory()->create(['name' => 'Beta']);

        livewire(TestTableWithFilteredMultipleRelationshipFilter::class)
            ->assertTableFilterExists('author', function (SelectFilter $filter) use ($author1, $author2, $wireField): bool {
                $select = $wireField($filter);

                $labels = $filter->getOptionLabelsFromRelationship($select, [(string) $author1->getKey(), (string) $author2->getKey()]);

                expect($labels)->toBe([$author1->getKey() => 'Alpha']);

                return true;
            });
    });

    it('filters by `BelongsToThrough` relationship via `apply()`', function (): void {
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $user1 = User::factory()->create(['team_id' => $team1->getKey()]);
        $user2 = User::factory()->create(['team_id' => $team2->getKey()]);

        $postsForTeam1 = Post::factory()->count(2)->create(['author_id' => $user1->getKey()]);
        $postsForTeam2 = Post::factory()->count(3)->create(['author_id' => $user2->getKey()]);

        livewire(TestTableWithBelongsToThroughFilter::class)
            ->assertCanSeeTableRecords($postsForTeam1->merge($postsForTeam2))
            ->filterTable('team', $team1->getKey())
            ->assertCanSeeTableRecords($postsForTeam1)
            ->assertCanNotSeeTableRecords($postsForTeam2);
    });
});

class TestTableWithPreloadedEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true)
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithCustomLabelPreloadedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithLimitedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', modifyQueryUsing: fn ($query) => $query->limit(3))
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithOrderedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', modifyQueryUsing: fn ($query) => $query->orderBy('name', 'desc'))
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithJsonPathRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'json->nickname')
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithSearchableEmptyRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true)
                    ->searchable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithCustomLabelSearchableRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                    ->searchable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithLimitedSearchableRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', modifyQueryUsing: fn ($query) => $query->limit(2))
                    ->searchable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithMultiplePreloadedRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithCustomLabelMultipleRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                    ->multiple()
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithJsonPathMultipleRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'json->nickname')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithFilteredMultipleRelationshipFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('author')
                    ->relationship('author', 'name', modifyQueryUsing: fn ($query) => $query->where('name', 'Alpha'))
                    ->multiple()
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class TestTableWithBelongsToThroughFilter extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
            ])
            ->filters([
                SelectFilter::make('team')
                    ->relationship('team', 'id')
                    ->preload(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
