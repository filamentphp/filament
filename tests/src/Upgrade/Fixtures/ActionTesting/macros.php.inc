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

function compatibilityAliases(Testable $testable, ?Testable $nullable = null): array
{
    $results = [];
    $results[] = $testable->setFormComponentActionData(data: ['title' => 'Changed'])->assertFormComponentActionDataSet(data: ['title' => 'Changed'])->assertHasNoFormComponentActionErrors(keys: ['title']);
    $results[] = $testable->setInfolistActionData(['title' => 'Changed'])->assertInfolistActionDataSet(data: static fn (array $state): array => ['title' => $state['title']])->assertHasNoInfolistActionErrors();
    $results[] = $testable->setTableActionData(data: ['title' => 'Changed'])->assertTableActionDataSet(data: ['title' => 'Changed'])->assertHasNoTableActionErrors();
    $results[] = $testable->setTableBulkActionData(['title' => 'Changed'])->assertTableBulkActionDataSet(['title' => 'Changed'])->assertHasNoTableBulkActionErrors();

    $setData = $testable->setTableBulkActionData(...);
    $assertData = $testable->assertFormComponentActionDataSet(...);
    $results[] = $setData(data: ['title' => 'Changed']);
    $results[] = $assertData(data: static fn (array $state): array => ['title' => $state['title']]);
    $results[] = $testable->setInfolistActionData(...['data' => ['title' => 'Changed']]);
    $results[] = $testable->assertTableActionDataSet(...(static function (): iterable {
        yield 'data' => ['title' => 'Changed'];
    })());

    $results[] = $nullable?->setTableActionData(...throw new \LogicException('Arguments must be skipped'))->assertHasNoTableActionErrors();

    return $results;
}

function compatibilityErrors(Testable $testable): Testable
{
    return $testable->assertHasFormComponentActionErrors(keys: ['title' => 'required'])
        ->assertHasInfolistActionErrors(['title' => 'required'])
        ->assertHasTableActionErrors()
        ->assertHasTableBulkActionErrors(keys: ['title' => 'required']);
}
