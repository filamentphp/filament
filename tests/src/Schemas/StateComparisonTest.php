<?php

use Filament\Schemas\StateComparison;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('compares current values with the base, not the draft', function (): void {
    $comparison = StateComparison::make()
        ->base(['title' => 'Original', 'settings' => ['enabled' => false]])
        ->draft(['title' => 'My draft', 'settings' => ['enabled' => true]])
        ->current(['settings' => ['enabled' => false], 'title' => 'Original']);

    expect($comparison->isStale())->toBeFalse();

    $comparison = StateComparison::make()
        ->base(['title' => 'Original', 'settings' => ['enabled' => false]])
        ->draft(['title' => 'Someone else changed it', 'settings' => ['enabled' => true]])
        ->current(['title' => 'Someone else changed it', 'settings' => ['enabled' => false]]);

    expect($comparison->isStale())->toBeTrue()
        ->and($comparison->getChangedPaths())->toBe(['title']);
});

it('ignores configured paths and descendants without removing their values', function (): void {
    $base = ['title' => 'Original', 'summary' => 'Old summary', 'items' => [['name' => 'A', 'total' => 10]], 'cache' => ['generated' => 'old']];
    $current = ['title' => 'Original', 'summary' => 'New summary', 'items' => [['name' => 'A', 'total' => 20]], 'cache' => ['generated' => 'new']];
    $draft = ['title' => 'Draft', 'summary' => 'Draft summary'];

    $comparison = StateComparison::make()
        ->base($base)
        ->draft($draft)
        ->current($current)
        ->ignoredStatePaths(['summary', 'items.*.total', 'cache']);

    expect($comparison->isStale())->toBeFalse()
        ->and($comparison->getBase())->toBe($base)
        ->and($comparison->getDraft())->toBe($draft)
        ->and($comparison->getCurrent())->toBe($current)
        ->and($comparison->toArray())->toMatchArray(['base' => $base, 'draft' => $draft, 'current' => $current]);

    $current['items'][0]['name'] = 'B';

    expect($comparison->current($current)->getChangedPaths())
        ->toBe(['items.0.name']);

    expect($comparison->ignoredStatePaths([])->getChangedPaths())
        ->toBe(['summary', 'items.0.name', 'items.0.total', 'cache.generated']);
});

it('distinguishes missing, null and differently typed values', function (): void {
    $comparison = StateComparison::make()
        ->base(['removed' => null, 'number' => 1, 'items' => ['first', 'second']])
        ->draft([])
        ->current(['added' => null, 'number' => '1', 'items' => ['second', 'first']]);

    expect($comparison->getChangedPaths())->toBe(['removed', 'number', 'items.0', 'items.1', 'added']);
});

it('separates incoming changes from divergent whole-field conflicts', function (): void {
    $comparison = StateComparison::make()
        ->base(['mine' => 'Original', 'theirs' => 2, 'same' => false, 'items' => [['name' => 'First'], ['name' => 'Second']]])
        ->draft(['mine' => 'Draft', 'theirs' => 2, 'same' => true, 'items' => [['name' => 'Draft'], ['name' => 'Second']]])
        ->current(['mine' => 'Original', 'theirs' => 9, 'same' => true, 'items' => [['name' => 'First'], ['name' => 'External']]]);

    expect($comparison->getIncomingPaths())->toBe(['theirs'])
        ->and($comparison->getConflictingPaths())->toBe(['items'])
        ->and($comparison->hasConflicts())->toBeTrue();

    // A finer boundary changes classification, without replacing comparison.
    $comparison->statePathGroups([
        'items.0.name' => ['items.0.name'],
        'items.1.name' => ['items.1.name'],
    ]);

    expect($comparison->hasConflicts())->toBeFalse()
        ->and($comparison->getIncomingPaths())->toBe(['items.1.name']);
});

it('compares whole-field structures without conflating dotted keys or object key order', function (): void {
    $comparison = StateComparison::make()
        ->base(['files' => ['report.pdf' => 'Original', 'other.pdf' => 'Other']])
        ->draft(['files' => ['report.pdf' => 'Draft', 'other.pdf' => 'Other']])
        ->current(['files' => ['other.pdf' => 'Other', 'report.pdf' => 'Original']]);

    expect($comparison->hasConflicts())->toBeFalse();

    $comparison->current(['files' => ['report' => ['pdf' => 'Original'], 'other.pdf' => 'Other']]);

    expect($comparison->getConflictingPaths())->toBe(['files']);
});

it('preserves configured key ordering inside whole-field boundaries', function (): void {
    $base = ['items' => [['settings' => ['first' => 'A', 'second' => 'B']]]];
    $draft = ['items' => [['settings' => ['second' => 'B', 'first' => 'A']]]];
    $current = ['items' => [['settings' => ['first' => 'External', 'second' => 'B']]]];
    $comparison = StateComparison::make()->base($base)->draft($draft)->current($current)
        ->orderedStatePaths(['items.*.settings']);

    expect($comparison->getConflictingPaths())->toBe(['items'])
        ->and($comparison->getIncomingPaths())->toBe([])
        ->and($comparison->getOrderedStatePaths())->toBe(['items.*.settings'])
        ->and($comparison->toArray()['orderedStatePaths'])->toBe(['items.*.settings']);

    $comparison->current($draft);

    expect($comparison->hasConflicts())->toBeFalse()
        ->and($comparison->getChangedPaths())->toBe(['items'])
        ->and($comparison->isStale())->toBeTrue();

    $comparison->ignoredStatePaths(['items.*.settings.second']);

    expect($comparison->isStale())->toBeFalse();

    $comparison->ignoredStatePaths([])->orderedStatePaths([])->current($current);

    expect($comparison->hasConflicts())->toBeFalse()
        ->and($comparison->getIncomingPaths())->toBe(['items']);
});

it('ignores derived descendants when classifying whole fields without deleting snapshots', function (): void {
    $base = ['items' => [['name' => 'Original', 'total' => 1]]];
    $draft = ['items' => [['name' => 'Draft', 'total' => 2]]];
    $current = ['items' => [['name' => 'Original', 'total' => 9]]];
    $comparison = StateComparison::make()->base($base)->draft($draft)->current($current)
        ->ignoredStatePaths(['items.*.total']);

    expect($comparison->hasConflicts())->toBeFalse()
        ->and($comparison->getIncomingPaths())->toBe([])
        ->and($comparison->getDraft())->toBe($draft);
});
