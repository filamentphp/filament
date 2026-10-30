<?php

namespace Filament\Tests\Upgrade\Fixtures\ActionTestingRegistrations;

use Livewire\Features\SupportTesting\Testable;

Testable::macro('setActionData', static fn (array $data): string => 'custom');

function custom(Testable $testable): void
{
    $testable->setActionData(data: []);
    $testable->assertActionDataSet(data: []);
    $testable->assertHasActionErrors();
    $testable->assertHasNoActionErrors();
}
