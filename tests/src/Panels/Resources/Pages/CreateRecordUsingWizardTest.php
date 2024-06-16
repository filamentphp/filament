<?php

use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreatePostUsingWizard;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('moves to next wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->nextFormWizardStep()
        ->assertHasFormErrors(['title'])
        ->assertHasNoFormErrors(['content'])
        ->nextFormWizardStep(currentStep: 1)
        ->assertHasFormErrors(['content'])
        ->assertHasNoFormErrors(['title']);
});
