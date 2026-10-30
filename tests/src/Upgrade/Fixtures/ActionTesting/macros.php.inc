<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionTesting;

use Closure;
use Livewire\Features\SupportTesting\Testable;

class InheritedTestable extends Testable
{
    public array $calls = [];
}

class TestableHolder
{
    public function __construct(public Testable $testable) {}
}

function propertyReceiver(?TestableHolder $holder): ?Testable
{
    return $holder?->testable->setActionData([]);
}

function computed(Testable $testable, array &$events): Testable
{
    $events[] = 'receiver';

    return $testable;
}

function state(array &$events): array
{
    $events[] = 'state';

    return ['title' => 'Changed'];
}

function ignored(array &$events): string
{
    $events[] = 'ignored';

    return 'not-a-schema';
}

function exercise(Testable $testable, array &$events, ?Testable $nullable = null): array
{
    $stored = computed($testable, $events);
    $results = [];
    $results[] = $stored->setActionData(data: state($events));
    $results[] = computed($testable, $events)->assertActionDataSet(data: ['title' => 'Changed']);
    $results[] = $testable->setActionData(...[state($events), ignored($events)])
        ->assertActionDataSet(['title' => 'Changed'], ignored($events))
        ->assertHasNoActionErrors();
    $results[] = $testable->assertActionDataSet(data: static function (array $state): array {
        return ['title' => $state['title']];
    });
    $results[] = $testable->assertHasNoActionErrors(...['keys' => ['title']]);
    $results[] = $nullable?->setActionData(state($events));
    $results[] = computed($testable, $events)?->setActionData(state($events));
    $results[] = $nullable?->setActionData(state($events))->assertActionDataSet(state($events))->assertHasNoActionErrors();
    $results[] = $testable->assertSchemaStateSet(['title' => 'Changed'])->setActionData(['title' => 'Changed']);
    $results[] = $testable->assertHasNoFormErrors()->assertHasNoActionErrors();

    return $results;
}

function errors(Testable $testable): Testable
{
    return $testable->assertHasActionErrors(keys: ['title' => 'required'])->assertHasFormErrors()->assertHasActionErrors();
}

function inherited(InheritedTestable $testable): Testable
{
    return $testable->setActionData(data: [])->assertHasNoActionErrors();
}

function callback(Testable $testable, string $method): Closure
{
    return match ($method) {
        'setActionData' => $testable->setActionData(...),
        'assertActionDataSet' => $testable->assertActionDataSet(...),
        'assertHasActionErrors' => $testable->assertHasActionErrors(...),
        'assertHasNoActionErrors' => $testable->assertHasNoActionErrors(...),
    };
}
