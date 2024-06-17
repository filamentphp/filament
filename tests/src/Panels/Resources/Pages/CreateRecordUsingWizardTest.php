<?php

use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreatePostUsingWizard;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('it validates the current wizard step when moving to the next wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->nextFormWizardStep()
        ->assertHasFormErrors(['title'])
        ->assertHasNoFormErrors(['content'])
        ->fillForm(['title' => 'Test title'])
        ->nextFormWizardStep()
        ->assertHasNoFormErrors()
        ->nextFormWizardStep(currentStep: 1)
        ->assertHasFormErrors(['content'])
        ->assertHasNoFormErrors(['title']);
});
