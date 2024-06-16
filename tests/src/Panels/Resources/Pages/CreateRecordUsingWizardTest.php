<?php

use Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages\CreatePostUsingWizard;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('moves to next wizard step', function () {
    livewire(CreatePostUsingWizard::class)
        ->nextFormWizardStep()
        ->assertHasErrors(['data.title'])
        ->assertHasNoErrors(['data.content'])
        ->nextFormWizardStep(currentStep: 1)
        ->assertHasErrors(['data.content'])
        ->assertHasNoErrors(['data.title']);
});



