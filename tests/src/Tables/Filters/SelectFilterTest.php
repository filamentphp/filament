<?php

namespace Filament\Tests\Tables\Filters;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

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
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\SelectFilter::make('rating')
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
                Tables\Filters\SelectFilter::make('author')
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
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name', hasEmptyOption: true)
                    ->multiple(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

it('can get `getOptions()` from static array', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status')
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
    $filter = Tables\Filters\SelectFilter::make('status')
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
    $filter = Tables\Filters\SelectFilter::make('status')
        ->options(StringBackedEnum::class);

    $options = $filter->getOptions();

    expect($options)->toBe([
        'one' => 'One',
        'two' => 'Two',
        'three' => 'Three',
    ]);
});

it('returns empty array for `getOptions()` when no options set', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status');

    expect($filter->getOptions())->toBe([]);
});

it('can check `isMultiple()` returns correct value', function (): void {
    $singleFilter = Tables\Filters\SelectFilter::make('status');
    $multipleFilter = Tables\Filters\SelectFilter::make('status')->multiple();

    expect($singleFilter->isMultiple())->toBeFalse();
    expect($multipleFilter->isMultiple())->toBeTrue();
});

it('can check `isSearchable()` returns correct value', function (): void {
    $nonSearchableFilter = Tables\Filters\SelectFilter::make('status');
    $searchableFilter = Tables\Filters\SelectFilter::make('status')->searchable();

    expect($nonSearchableFilter->getSearchable())->toBeFalse();
    expect($searchableFilter->getSearchable())->toBeTrue();
});

it('can get options limit using `getOptionsLimit()`', function (): void {
    $defaultFilter = Tables\Filters\SelectFilter::make('status');
    $limitedFilter = Tables\Filters\SelectFilter::make('status')->optionsLimit(100);

    expect($defaultFilter->getOptionsLimit())->toBe(50);
    expect($limitedFilter->getOptionsLimit())->toBe(100);
});

it('can check `queriesRelationships()` returns `true` when relationship is set', function (): void {
    $withoutRelationship = Tables\Filters\SelectFilter::make('status')
        ->options(['active' => 'Active']);

    $withRelationship = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name');

    expect($withoutRelationship->queriesRelationships())->toBeFalse();
    expect($withRelationship->queriesRelationships())->toBeTrue();
});

it('can get `getRelationshipName()` from string', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name');

    expect($filter->getRelationshipName())->toBe('author');
});

it('can get `getRelationshipName()` from closure', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
        ->relationship(fn (): string => 'author', 'name');

    expect($filter->getRelationshipName())->toBe('author');
});

it('can get `getRelationshipTitleAttribute()`', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name');

    expect($filter->getRelationshipTitleAttribute())->toBe('name');
});

it('can check `hasEmptyRelationshipOption()` returns `true` when `hasEmptyOption` is set', function (): void {
    $withoutEmptyOption = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name');

    $withEmptyOption = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name', hasEmptyOption: true);

    expect($withoutEmptyOption->hasEmptyRelationshipOption())->toBeFalse();
    expect($withEmptyOption->hasEmptyRelationshipOption())->toBeTrue();
});

it('can get default empty relationship option label', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name', hasEmptyOption: true);

    expect($filter->getEmptyRelationshipOptionLabel())->toBe(__('filament-tables::table.filters.select.relationship.empty_option_label'));
});

it('can get custom empty relationship option label', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name', hasEmptyOption: true)
        ->emptyRelationshipOptionLabel('No Author');

    expect($filter->getEmptyRelationshipOptionLabel())->toBe('No Author');
});

it('can check `isPreloaded()` returns correct value', function (): void {
    $notPreloaded = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name');

    $preloaded = Tables\Filters\SelectFilter::make('author')
        ->relationship('author', 'name')
        ->preload();

    expect($notPreloaded->isPreloaded())->toBeFalse();
    expect($preloaded->isPreloaded())->toBeTrue();
});

it('can check `isNative()` returns correct value', function (): void {
    $nativeFilter = Tables\Filters\SelectFilter::make('status');
    $nonNativeFilter = Tables\Filters\SelectFilter::make('status')->native(false);

    expect($nativeFilter->isNative())->toBeTrue();
    expect($nonNativeFilter->isNative())->toBeFalse();
});

it('can check `canSelectPlaceholder()` returns correct value', function (): void {
    $withPlaceholder = Tables\Filters\SelectFilter::make('status');
    $withoutPlaceholder = Tables\Filters\SelectFilter::make('status')->selectablePlaceholder(false);

    expect($withPlaceholder->canSelectPlaceholder())->toBeTrue();
    expect($withoutPlaceholder->canSelectPlaceholder())->toBeFalse();
});

it('can get `getAttribute()` returns filter name by default', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status');

    expect($filter->getAttribute())->toBe('status');
});

it('can get custom `getAttribute()`', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status')
        ->attribute('custom_status');

    expect($filter->getAttribute())->toBe('custom_status');
});

it('can get `getFormField()` returns `Select` component', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status')
        ->options(['active' => 'Active', 'inactive' => 'Inactive']);

    $formField = $filter->getFormField();

    expect($formField)->toBeInstanceOf(Select::class);
    expect($formField->getName())->toBe('value');
});

it('can get `getFormField()` returns `Select` component with `values` name for multiple filter', function (): void {
    $filter = Tables\Filters\SelectFilter::make('status')
        ->multiple()
        ->options(['active' => 'Active', 'inactive' => 'Inactive']);

    $formField = $filter->getFormField();

    expect($formField)->toBeInstanceOf(Select::class);
    expect($formField->getName())->toBe('values');
    expect($formField->isMultiple())->toBeTrue();
});

it('can get `getFormField()` with relationship configuration', function (): void {
    $filter = Tables\Filters\SelectFilter::make('author')
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
        ->assertTableFilterExists('author', function (Tables\Filters\SelectFilter $filter): bool {
            $formField = $filter->getFormField();

            expect($formField->getOptions())->toBe([]);

            return true;
        });
});

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
                Tables\Filters\SelectFilter::make('author')
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
                Tables\Filters\SelectFilter::make('author')
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
                Tables\Filters\SelectFilter::make('author')
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
                Tables\Filters\SelectFilter::make('author')
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
