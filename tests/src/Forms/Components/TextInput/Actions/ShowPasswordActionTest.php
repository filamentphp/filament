<?php

use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('has default name `showPassword`', function (): void {
    expect(ShowPasswordAction::getDefaultName())->toBe('showPassword');
});

it('has a label', function (): void {
    $action = ShowPasswordAction::make();

    expect($action->getLabel())->toBeString()->not->toBeEmpty();
});

it('has an icon', function (): void {
    $action = ShowPasswordAction::make();

    expect($action->getIcon())->not->toBeNull();
});

it('transfers focus to the `HidePasswordAction` after revealing the password', function (): void {
    $action = ShowPasswordAction::make();

    expect($action)
        ->getExtraAttributes()->toMatchArray([
            'wire:loading.attr' => false,
            'x-ref' => 'showPasswordAction',
            'x-show' => '! isPasswordRevealed',
        ])
        ->getCustomAlpineClickHandler()->toBe('setPasswordRevealed(true)');
});
