<?php

namespace Filament\Tests\Tables\Filters;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Filters\DateRangeFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

class DateRangeFilterTest extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
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
                DateRangeFilter::make('created_at'),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

describe('filtering records', function (): void {
    it('can render table with `DateRangeFilter`', function (): void {
        Post::factory()->count(5)->create();

        livewire(DateRangeFilterTest::class)
            ->assertSuccessful();
    });

    it('can filter records by `date_from`', function (): void {
        $oldPosts = Post::factory()->count(2)->create(['created_at' => '2024-01-01']);
        $newPosts = Post::factory()->count(3)->create(['created_at' => '2024-06-01']);

        livewire(DateRangeFilterTest::class)
            ->assertCanSeeTableRecords($oldPosts->merge($newPosts))
            ->filterTable('created_at', ['date_from' => '2024-03-01', 'date_until' => null])
            ->assertCanSeeTableRecords($newPosts)
            ->assertCanNotSeeTableRecords($oldPosts);
    });

    it('can filter records by `date_until`', function (): void {
        $oldPosts = Post::factory()->count(2)->create(['created_at' => '2024-01-01']);
        $newPosts = Post::factory()->count(3)->create(['created_at' => '2024-06-01']);

        livewire(DateRangeFilterTest::class)
            ->assertCanSeeTableRecords($oldPosts->merge($newPosts))
            ->filterTable('created_at', ['date_from' => null, 'date_until' => '2024-03-01'])
            ->assertCanSeeTableRecords($oldPosts)
            ->assertCanNotSeeTableRecords($newPosts);
    });

    it('can filter records by both `date_from` and `date_until`', function (): void {
        $earlyPosts = Post::factory()->count(2)->create(['created_at' => '2024-01-01']);
        $midPosts = Post::factory()->count(2)->create(['created_at' => '2024-04-01']);
        $latePosts = Post::factory()->count(2)->create(['created_at' => '2024-09-01']);

        livewire(DateRangeFilterTest::class)
            ->filterTable('created_at', ['date_from' => '2024-03-01', 'date_until' => '2024-06-01'])
            ->assertCanSeeTableRecords($midPosts)
            ->assertCanNotSeeTableRecords($earlyPosts)
            ->assertCanNotSeeTableRecords($latePosts);
    });

    it('can reset `DateRangeFilter` to show all records', function (): void {
        $oldPosts = Post::factory()->count(2)->create(['created_at' => '2024-01-01']);
        $newPosts = Post::factory()->count(3)->create(['created_at' => '2024-06-01']);

        livewire(DateRangeFilterTest::class)
            ->filterTable('created_at', ['date_from' => '2024-03-01', 'date_until' => null])
            ->assertCanNotSeeTableRecords($oldPosts)
            ->resetTableFilters()
            ->assertCanSeeTableRecords($oldPosts->merge($newPosts));
    });
});

describe('options and properties', function (): void {
    it('can get `getAttribute()` from filter name by default', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->getAttribute())->toBe('created_at');
    });

    it('can set `attribute()` to override the queried column', function (): void {
        $filter = DateRangeFilter::make('published')
            ->attribute('published_at');

        expect($filter->getAttribute())->toBe('published_at');
    });

    it('returns two `DatePicker` fields from `getSchemaComponents()` by default', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->getSchemaComponents())->toHaveCount(2);
    });

    it('has correct `getResetState()`', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->getResetState())->toBe([
            'date_from' => null,
            'date_until' => null,
        ]);
    });

    it('can set `fromLabel()`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->fromLabel('Start date');

        expect($filter->getFromLabel())->toBe('Start date');
    });

    it('can set `fromLabel()` with a `Closure`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->fromLabel(static fn (): string => 'Dynamic from');

        expect($filter->getFromLabel())->toBe('Dynamic from');
    });

    it('can set `untilLabel()`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->untilLabel('End date');

        expect($filter->getUntilLabel())->toBe('End date');
    });

    it('can set `untilLabel()` with a `Closure`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->untilLabel(static fn (): string => 'Dynamic until');

        expect($filter->getUntilLabel())->toBe('Dynamic until');
    });

    it('defaults `isInline()` to `false`', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->isInline())->toBeFalse();
    });

    it('can set `inline()`', function (): void {
        $filter = DateRangeFilter::make('created_at')->inline();

        expect($filter->isInline())->toBeTrue();
    });

    it('can set `inline()` to `false`', function (): void {
        $filter = DateRangeFilter::make('created_at')->inline()->inline(false);

        expect($filter->isInline())->toBeFalse();
    });

    it('wraps pickers in a `Grid` when `inline()` is set', function (): void {
        $filter = DateRangeFilter::make('created_at')->inline();

        $components = $filter->getSchemaComponents();

        expect($components)->toHaveCount(1)
            ->and($components[0])->toBeInstanceOf(\Filament\Schemas\Components\Grid::class);
    });

    it('defaults `hasUntil()` to `true`', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->hasUntil())->toBeTrue();
    });

    it('can disable the until picker with `withoutUntil()`', function (): void {
        $filter = DateRangeFilter::make('created_at')->withoutUntil();

        expect($filter->hasUntil())->toBeFalse();
    });

    it('returns one field from `getSchemaComponents()` when `withoutUntil()` is set', function (): void {
        $filter = DateRangeFilter::make('created_at')->withoutUntil();

        expect($filter->getSchemaComponents())->toHaveCount(1);
    });

    it('defaults `isNative()` to `true`', function (): void {
        $filter = DateRangeFilter::make('created_at');

        expect($filter->isNative())->toBeTrue();
    });

    it('can set `native()` to `false`', function (): void {
        $filter = DateRangeFilter::make('created_at')->native(false);

        expect($filter->isNative())->toBeFalse();
    });

    it('can set `native()` with a `Closure`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->native(static fn (): bool => false);

        expect($filter->isNative())->toBeFalse();
    });

    it('can modify the from `DatePicker` using `modifyFromDatePickerUsing()`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->modifyFromDatePickerUsing(function (DatePicker $datePicker): DatePicker {
                return $datePicker->placeholder('Pick a start date');
            });

        $components = $filter->getSchemaComponents();

        expect($components[0])->toBeInstanceOf(DatePicker::class);
    });

    it('can modify the until `DatePicker` using `modifyUntilDatePickerUsing()`', function (): void {
        $filter = DateRangeFilter::make('created_at')
            ->modifyUntilDatePickerUsing(function (DatePicker $datePicker): DatePicker {
                return $datePicker->placeholder('Pick an end date');
            });

        $components = $filter->getSchemaComponents();

        expect($components[1])->toBeInstanceOf(DatePicker::class);
    });
});
