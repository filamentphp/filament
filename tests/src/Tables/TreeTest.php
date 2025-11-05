<?php

use Filament\Tests\Fixtures\Livewire\CategoryTreeTable;
use Filament\Tests\Fixtures\Models\Category;
use Filament\Tests\Tables\TestCase;

uses(TestCase::class);

function createCategory(string $name, int $position, ?Category $parent = null): Category
{
    return Category::create([
        'name' => $name,
        'position' => $position,
        'parent_id' => $parent?->getKey(),
    ]);
}

function makeCategoryTreeComponent(): CategoryTreeTable
{
    /** @var CategoryTreeTable $component */
    $component = app(CategoryTreeTable::class);

    $component->mountInteractsWithTable();
    $component->bootedInteractsWithTable();

    return $component;
}

it('builds a nested tree dataset from a records data source', function (): void {
    $rootA = createCategory('Root A', 1);
    $childA1 = createCategory('Child A1', 1, $rootA);
    $childA2 = createCategory('Child A2', 2, $rootA);
    $rootB = createCategory('Root B', 2);

    $component = makeCategoryTreeComponent();

    $records = $component->getTableRecords();

    expect($records)
        ->toHaveCount(2)
        ->and(array_values($records->keys()->all()))->toBe([$rootA->getKey(), $rootB->getKey()]);

    $first = $records->first();

    expect($first->children)
        ->toHaveCount(2);

    $firstChild = $first->children->first();

    expect($firstChild->getAttribute('__filament_tree_parent'))->toBe((string) $rootA->getKey())
        ->and($firstChild->getAttribute('__filament_tree_depth'))->toBe(1);
});

it('reorders tree records and updates parent assignment', function (): void {
    $rootA = createCategory('Root A', 1);
    $childA1 = createCategory('Child A1', 1, $rootA);
    $childA2 = createCategory('Child A2', 2, $rootA);
    $rootB = createCategory('Root B', 2);

    $component = makeCategoryTreeComponent();

    $component->reorderTreeTable([
        ['id' => $rootB->getKey(), 'position' => 1, 'parent' => null],
        ['id' => $childA2->getKey(), 'position' => 1, 'parent' => $rootB->getKey()],
        ['id' => $rootA->getKey(), 'position' => 2, 'parent' => null],
        ['id' => $childA1->getKey(), 'position' => 1, 'parent' => $rootA->getKey()],
    ]);

    expect($childA2->refresh())
        ->parent_id->toBe($rootB->getKey())
        ->position->toBe(1);

    expect($rootB->refresh()->position)->toBe(1);
    expect($rootA->refresh()->position)->toBe(2);
    expect($childA1->refresh()->parent_id)->toBe($rootA->getKey());
});
