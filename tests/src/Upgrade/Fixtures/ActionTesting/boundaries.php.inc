<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionTestingBoundaries;

use Filament\Actions\Testing\TestsActions;
use Livewire\Features\SupportTesting\Testable;
use stdClass;

class CustomTestable extends Testable
{
    public function setActionData(array $data): string
    {
        return 'custom';
    }
}

trait CustomDispatch
{
    public function __call($method, $params)
    {
        return 'custom';
    }
}

class DispatchTestable extends Testable
{
    use CustomDispatch;
}

class InheritedDispatchTestable extends DispatchTestable {}

class CustomFluentTestable extends Testable
{
    public function fillForm(array $state): object
    {
        return new stdClass;
    }
}

function customChain(CustomFluentTestable $testable): void
{
    $testable->fillForm([])->setActionData([]);
}

function boundaries(TestsActions $mixin, CustomTestable $custom, InheritedDispatchTestable $dispatch, Testable $testable, object $unknown, string $method): void
{
    $mixin->setActionData();
    $mixin->assertActionDataSet();
    $mixin->assertHasActionErrors();
    $callback = $mixin->assertHasNoActionErrors(...);
    $custom->setActionData(['title' => 'Custom']);
    $dispatch->setActionData(['title' => 'Custom']);
    $testable->{$method}(['title' => 'Dynamic']);
    $callback = [$testable, 'setActionData'];
    $testable->customMacro()->setActionData([]);
    $testable->get('title')->assertHasNoActionErrors();
    $unknown->setActionData([]);
    $testable->SETACTIONDATA([]);
}
