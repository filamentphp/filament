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
    $component = function () use (&$argumentCalls): string {
        $argumentCalls++;

        return 'textInput';
    };
    $results = [
        $receiver()->assertFormComponentActionExists($component(), 'exists')->assertFormExists(),
        $testable->assertFormComponentActionHasLabel(
            formName: 'form',
            label: 'From arguments',
            arguments: ['label' => 'From arguments'],
            actions: [['name' => 'conditional']],
            components: 'parent',
        ),
        $testable->assertFormComponentExists('textInput', static fn ($component): bool => $component->getName() === 'textInput'),
    ];
    $results[] = $nullable?->assertFormComponentActionExists($component(), 'exists')?->assertFormExists();
    $nullable = $testable;
    $results[] = $nullable?->assertFormComponentActionExists($component(), 'exists')?->assertFormExists();
    $results[] = $testable->assertFormComponentActionHasLabel(label: 'My Action', actions: 'hasLabel', components: 'textInput');

    return [$results, $receiverCalls, $argumentCalls];
}

function tableSyntax(Testable $testable, $record): Testable
{
    $testable->assertTableActionExists(record: $record, checkActionUsing: static fn (Action $action): bool => $action->getRecord()->is($record), actions: 'edit');
    $testable->assertTableActionExists('parent.footer', record: $record)
        ->assertTableActionExists(['parent', 'footer'], record: $record);
    $testable->assertTableActionExists('exists', record: null);

    return $testable->assertTableActionDoesNotExist(
        record: $record->getKey(),
        actions: 'edit',
        checkActionUsing: static fn (Action $action): bool => ! $action->getRecord()->is($record),
    );
}

function shortCircuit(?Testable $nullable): array
{
    return [
        $nullable?->assertFormExists()->assertFormComponentExists(throw new LogicException('Skipped schema component argument was evaluated.')),
        $nullable?->fillForm([])->assertFormExists(name: throw new LogicException('Skipped schema argument was evaluated.')),
    ];
}
