<?php

use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tests\Fixtures\Livewire\SpatieTagsColumnTable;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('type', function (): void {
    it('defaults `getType()` to `AllTagTypes`', function (): void {
        $column = SpatieTagsColumn::make('tags');

        expect($column->getType())->toBeInstanceOf(AllTagTypes::class);
    });

    it('defaults `isAnyTagTypeAllowed()` to `true`', function (): void {
        $column = SpatieTagsColumn::make('tags');

        expect($column->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` with a string', function (): void {
        $column = SpatieTagsColumn::make('tags')
            ->type('category');

        expect($column->getType())->toBe('category');
        expect($column->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` with a `Closure`', function (): void {
        $column = SpatieTagsColumn::make('tags')
            ->type(static fn (): string => 'dynamic');

        expect($column->getType())->toBe('dynamic');
        expect($column->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` to `AllTagTypes` to allow any type', function (): void {
        $column = SpatieTagsColumn::make('tags')
            ->type('category')
            ->type(new AllTagTypes);

        expect($column->getType())->toBeInstanceOf(AllTagTypes::class);
        expect($column->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` to `null`', function (): void {
        $column = SpatieTagsColumn::make('tags')
            ->type(null);

        expect($column->getType())->toBeNull();
        expect($column->isAnyTagTypeAllowed())->toBeFalse();
    });
});

it('is configured as a badge by default', function (): void {
    $column = SpatieTagsColumn::make('tags');

    expect($column->isBadge())->toBeTrue();
});

describe('eager loading', function (): void {
    it('can apply eager loading to a query', function (): void {
        $column = SpatieTagsColumn::make('tags');

        $query = Article::query();
        $result = $column->applyEagerLoading($query);

        expect($result->getEagerLoads())->toHaveKey('tags');
    });

    it('does not apply eager loading when column is hidden', function (): void {
        $column = SpatieTagsColumn::make('tags')
            ->hidden();

        $query = Article::query();
        $result = $column->applyEagerLoading($query);

        expect($result->getEagerLoads())->not->toHaveKey('tags');
    });
});

describe('rendering with tags', function (): void {
    it('can render column for a record with tags', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel', 'PHP']);

        livewire(SpatieTagsColumnTable::class)
            ->assertTableColumnExists('tags')
            ->assertCanRenderTableColumn('tags');
    });

    it('can render column for a record without tags', function (): void {
        Article::factory()->create();

        livewire(SpatieTagsColumnTable::class)
            ->assertTableColumnExists('tags')
            ->assertCanRenderTableColumn('tags');
    });

    it('can render column with typed tags', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('Laravel', 'framework');

        livewire(SpatieTagsColumnTable::class, ['tagType' => 'framework'])
            ->assertTableColumnExists('tags')
            ->assertCanRenderTableColumn('tags');
    });
});
