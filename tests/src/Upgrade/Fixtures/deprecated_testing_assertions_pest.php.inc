<?php

namespace Pest\Livewire;

use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// The plugin's public return annotation, without installing deprecated macros.
/** @return Testable<Component> */
function livewire(string $name, array $params = [])
{
    return Livewire::test($name, $params);
}

namespace Filament\Tests\Upgrade\Fixtures\PestTestingAssertions;

use function Pest\Livewire\livewire as testComponent;

function assertions(): void
{
    testComponent('edit-post')->assertFormFieldIsVisible('title')->assertFormFieldIsEnabled('title');
    testComponent('edit-post')->callAction('save')->assertActionHeld(actions: 'save');
    testComponent('edit-post')->assertTableActionsExistInOrder(names: ['edit', 'delete']);
    $testable = \Pest\Livewire\livewire('edit-post');
    $callback = $testable->assertTableActionHeld(...);
}
-----
<?php

namespace Pest\Livewire;

use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// The plugin's public return annotation, without installing deprecated macros.
/** @return Testable<Component> */
function livewire(string $name, array $params = [])
{
    return Livewire::test($name, $params);
}

namespace Filament\Tests\Upgrade\Fixtures\PestTestingAssertions;

use function Pest\Livewire\livewire as testComponent;

function assertions(): void
{
    testComponent('edit-post')->assertFormFieldVisible('title')->assertFormFieldEnabled('title');
    testComponent('edit-post')->callAction('save')->assertActionHalted(actions: 'save');
    testComponent('edit-post')->assertTableRecordActionsExistInOrder(names: ['edit', 'delete']);
    $testable = \Pest\Livewire\livewire('edit-post');
    $callback = $testable->assertTableActionHalted(...);
}
