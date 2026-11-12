<?php

use Filament\Forms\EditConflictHandler;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can configure and reset ignored paths and whole-field groups', function (): void {
    $base = ['title' => 'Original', 'summary' => 'Original', 'derived' => 'Original'];
    $draft = [
        'state' => ['title' => 'Draft', 'summary' => 'Original', 'derived' => 'Draft'],
        'statePathGroups' => ['title' => ['title'], 'summary' => ['summary'], 'derived' => ['derived']],
        'orderedStatePaths' => [],
    ];
    $current = [...$draft, 'state' => ['title' => 'Original', 'summary' => 'Saved', 'derived' => 'Saved']];
    $handler = EditConflictHandler::make()->ignoredStatePaths(['derived'])->statePathGroups([
        'content' => ['title', 'summary'],
        'derived' => ['derived'],
    ]);

    expect($handler->compare($base, $draft, $current)->getConflictingPaths())->toBe(['content']);

    $handler->ignoredStatePaths([])->statePathGroups(null);

    expect($handler->compare($base, $draft, $current)->getConflictingPaths())->toBe(['derived'])
        ->and($handler->compare($base, $draft, $current)->getIncomingPaths())->toBe(['summary']);
});

it('keeps a reused configured handler separate from each editor context', function (): void {
    $handler = EditConflictHandler::make();
    $form = Schema::make(new Livewire);
    $pageHandler = $handler->context($form, ['conflictingPaths' => ['title']], static fn (): Schema => $form);
    $pageAction = $pageHandler->getAction();
    $actionHandler = $handler->context($form, null, static fn (): Schema => $form, discardMethod: 'discardActionFormChanges');

    expect($handler->getOutcome())->toBeNull()
        ->and($pageHandler->getOutcome())->toBe(['conflictingPaths' => ['title']])
        ->and($pageAction->isDisabled())->toBeFalse()
        ->and($actionHandler->getAction()->isDisabled())->toBeTrue();
});
