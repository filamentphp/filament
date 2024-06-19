<?php

use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreatePostUsingWizard;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('it validates the current wizard step when moving to the next wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->goToWizardStep(2)
        ->assertHasFormErrors(['title'])
        ->assertHasNoFormErrors(['content'])
        ->fillForm(['title' => 'Test title'])
        ->goToWizardStep(2)
        ->assertHasNoFormErrors()
        ->goToWizardStep(-1) // last step
        ->assertHasFormErrors(['content'])
        ->assertHasNoFormErrors(['title']);
});

it('it verifies what wizard step we are currently in', function () {
    livewire(CreatePostUsingWizard::class)
        ->assertWizardCurrentStepIs(1)
        ->fillForm(['title' => 'Test title'])
        ->goToWizardStep(2)
        ->assertWizardCurrentStepIs(2);
});

it('it goes to the last wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->fillForm(['content' => 'Test content'])
        ->assertWizardCurrentStepIs(1)
        ->goToWizardStep(-1)
        ->assertWizardCurrentStepIs(3);
});

it('it goes to the next wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->fillForm(['title' => 'Test title'])
        ->goToNextWizardStep()
        ->assertWizardCurrentStepIs(2);
});

it('it goes to the previous wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->fillForm(['title' => 'Test title'])
        ->goToNextWizardStep()
        ->assertWizardCurrentStepIs(2)
        ->goToPreviousWizardStep()
        ->assertWizardCurrentStepIs(1);
});
