<?php

use Filament\Forms\Components\TextInput\Actions\CopyAction;
use Filament\Tests\TestCase;
use Illuminate\Support\Js;

uses(TestCase::class);

it('has default name `copy`', function (): void {
    expect(CopyAction::getDefaultName())->toBe('copy');
});

it('has a label', function (): void {
    $action = CopyAction::make();

    expect($action->getLabel())->toBeString()->not->toBeEmpty();
});

it('reads the current input value from the DOM instead of baking in the server-rendered `$state`', function (): void {
    $action = CopyAction::make();

    $handler = $action->getCustomAlpineClickHandler();

    expect($handler)
        ->toContain("\$el.closest('.fi-input-wrp')?.querySelector('input')?.value")
        ->not->toContain(Js::from('test'));
});

describe('copy message', function (): void {
    it('returns default translation when no custom message is set', function (): void {
        $action = CopyAction::make();

        $message = $action->getCopyMessage('test');

        expect($message)->toBeString()->not->toBeEmpty();
    });

    it('returns custom message when set', function (): void {
        $action = CopyAction::make()
            ->copyMessage('Copied!');

        expect($action->getCopyMessage('test'))->toBe('Copied!');
    });

    it('can set `copyMessage()` with a `Closure` that receives `$state`', function (): void {
        $action = CopyAction::make()
            ->copyMessage(static fn (mixed $state): string => "Copied: {$state}");

        expect($action->getCopyMessage('hello'))->toBe('Copied: hello');
    });
});

describe('copy message duration', function (): void {
    it('returns `2000` by default', function (): void {
        $action = CopyAction::make();

        expect($action->getCopyMessageDuration('test'))->toBe(2000);
    });

    it('returns custom duration when set', function (): void {
        $action = CopyAction::make()
            ->copyMessageDuration(5000);

        expect($action->getCopyMessageDuration('test'))->toBe(5000);
    });

    it('can set `copyMessageDuration()` with a `Closure`', function (): void {
        $action = CopyAction::make()
            ->copyMessageDuration(static fn (): int => 3000);

        expect($action->getCopyMessageDuration('test'))->toBe(3000);
    });
});
