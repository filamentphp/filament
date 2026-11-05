<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\ContextualTesting\Current;

use Filament\Actions\Action;
use Livewire\Features\SupportTesting\Testable;
use LogicException;

function syntax(Testable $testable, ?Testable $nullable = null): array
{
    $receiverCalls = 0;
    $argumentCalls = 0;
    $receiver = function () use ($testable, &$receiverCalls): Testable {
        $receiverCalls++;

        return $testable;
    };
    $arguments = function () use (&$argumentCalls): array {
        $argumentCalls++;

        return ['components' => 'textInput', 'actions' => 'exists'];
    };
    $results = [
        $receiver()->assertFormComponentActionExists(...$arguments())->assertFormExists(),
        $testable->assertFormComponentActionHasLabel(
            formName: 'form',
            label: 'From arguments',
            arguments: ['label' => 'From arguments'],
            actions: [['name' => 'conditional']],
            components: 'parent',
        ),
        $testable->assertFormComponentExists('textInput', static fn ($component): bool => $component->getName() === 'textInput'),
    ];
    $results[] = $nullable?->assertFormComponentActionExists(...$arguments())?->assertFormExists();
    $nullable = $testable;
    $results[] = $nullable?->assertFormComponentActionExists(...$arguments())?->assertFormExists();
    $callback = $testable->assertFormComponentActionHasLabel(...);
    $results[] = $callback(label: 'My Action', actions: 'hasLabel', components: 'textInput');

    return [$results, $receiverCalls, $argumentCalls];
}

function tableSyntax(Testable $testable, $record): Testable
{
    $callback = $testable->assertTableActionExists(...);
    $callback(record: $record, checkActionUsing: static fn (Action $action): bool => $action->getRecord()->is($record), actions: 'edit');

    return $testable->assertTableActionDoesNotExist(...[
        'record' => $record->getKey(),
        'actions' => 'edit',
        'checkActionUsing' => static fn (Action $action): bool => ! $action->getRecord()->is($record),
    ]);
}

function shortCircuit(?Testable $nullable): array
{
    return [
        $nullable?->assertFormExists()->assertFormComponentExists(throw new LogicException('Skipped schema component argument was evaluated.')),
        $nullable?->fillForm([])->assertFormExists(name: throw new LogicException('Skipped schema argument was evaluated.')),
    ];
}

function weakCallback(Testable $testable): Testable
{
    $callback = $testable->assertFormExists(...);

    return $callback(123);
}
