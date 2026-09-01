<?php

use Filament\Tests\Fixtures\Resources\Posts\Pages\CreatePostUsingWizard;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('it validates the current wizard step when moving to the next wizard step', function (): void {
    livewire(CreatePostUsingWizard::class)
        ->goToWizardStep(2)
        ->assertHasFormErrors(['title'])
        ->assertHasNoFormErrors(['content'])
        ->fillForm(['title' => 'Test title'])
        ->goToWizardStep(2)
        ->assertHasNoFormErrors();
});

it('it verifies what wizard step we are currently in', function (): void {
    livewire(CreatePostUsingWizard::class)
        ->assertWizardCurrentStep(1)
        ->fillForm(['title' => 'Test title'])
        ->goToWizardStep(2)
        ->assertWizardCurrentStep(2);
});

it('it goes to the next wizard step', function (): void {
    livewire(CreatePostUsingWizard::class)
        ->fillForm(['title' => 'Test title'])
        ->goToNextWizardStep()
        ->assertWizardCurrentStep(2);
});

it('it goes to the previous wizard step', function (): void {
    livewire(CreatePostUsingWizard::class)
        ->fillForm(['title' => 'Test title'])
        ->goToNextWizardStep()
        ->assertWizardCurrentStep(2)
        ->goToPreviousWizardStep()
        ->assertWizardCurrentStep(1);
});
