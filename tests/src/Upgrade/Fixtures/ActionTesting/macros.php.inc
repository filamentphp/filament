<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionTesting;

use Livewire\Features\SupportTesting\Testable;

function exercise(Testable $testable, ?Testable $nullable = null): array
{
    $results = [];
    $results[] = $testable->setActionData(data: ['title' => 'Changed'])
        ->assertActionDataSet(data: ['title' => 'Changed'])
        ->assertHasNoActionErrors();
    $results[] = $testable->assertActionDataSet(data: static function (array $state): array {
        return ['title' => $state['title']];
    });
    $results[] = $testable->assertHasNoActionErrors(keys: ['title']);
    $results[] = $nullable?->setActionData(['title' => 'Changed'])->assertActionDataSet(['title' => 'Changed']);
    $results[] = $testable?->setActionData(['title' => 'Changed'])->assertActionDataSet(['title' => 'Changed']);

    return $results;
}

function errors(Testable $testable): Testable
{
    return $testable->assertHasActionErrors(keys: ['title' => 'required'])->assertHasFormErrors()->assertHasActionErrors();
}
