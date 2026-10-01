<?php

use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('has default name `hidePassword`', function (): void {
    expect(HidePasswordAction::getDefaultName())->toBe('hidePassword');
});

it('has a label', function (): void {
    $action = HidePasswordAction::make();

    expect($action->getLabel())->toBeString()->not->toBeEmpty();
});

it('has an icon', function (): void {
    $action = HidePasswordAction::make();

    expect($action->getIcon())->not->toBeNull();
});

it('transfers focus to the `ShowPasswordAction` after hiding the password', function (): void {
    $action = HidePasswordAction::make();

    expect($action)
        ->getExtraAttributes()->toMatchArray([
            'wire:loading.attr' => false,
            'x-ref' => 'hidePasswordAction',
            'x-show' => 'isPasswordRevealed',
        ])
        ->getCustomAlpineClickHandler()->toBe('setPasswordRevealed(false)');
});
