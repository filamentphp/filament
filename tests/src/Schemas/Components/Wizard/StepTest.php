<?php

use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with a label', function (): void {
    $step = Step::make('Account');

    expect($step->getLabel())->toBe('Account');
});

it('returns `true` for `canConcealComponents()`', function (): void {
    $step = Step::make('Account');

    expect($step->canConcealComponents())->toBeTrue();
});

describe('description', function (): void {
    it('returns `null` for `getDescription()` by default', function (): void {
        $step = Step::make('Account');

        expect($step->getDescription())->toBeNull();
    });

    it('can set `description()`', function (): void {
        $step = Step::make('Account')->description('Enter your account details');

        expect($step->getDescription())->toBe('Enter your account details');
    });

    it('can set `description()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->description(static fn (): string => 'Dynamic description');

        expect($step->getDescription())->toBe('Dynamic description');
    });

    it('can clear `description()` with `null`', function (): void {
        $step = Step::make('Account')
            ->description('text')
            ->description(null);

        expect($step->getDescription())->toBeNull();
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $step = Step::make('Account');

        expect($step->getIcon())->toBeNull();
    });

    it('can set `icon()` with a string', function (): void {
        $step = Step::make('Account')->icon('heroicon-o-user');

        expect($step->getIcon())->toBe('heroicon-o-user');
    });

    it('can set `icon()` with a `BackedEnum`', function (): void {
        $step = Step::make('Account')->icon(Heroicon::User);

        expect($step->getIcon())->toBe(Heroicon::User);
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->icon(static fn (): string => 'heroicon-o-star');

        expect($step->getIcon())->toBe('heroicon-o-star');
    });

    it('can clear `icon()` with `null`', function (): void {
        $step = Step::make('Account')
            ->icon('heroicon-o-user')
            ->icon(null);

        expect($step->getIcon())->toBeNull();
    });
});

describe('completed icon', function (): void {
    it('returns `null` for `getCompletedIcon()` by default', function (): void {
        $step = Step::make('Account');

        expect($step->getCompletedIcon())->toBeNull();
    });

    it('can set `completedIcon()` with a string', function (): void {
        $step = Step::make('Account')->completedIcon('heroicon-o-check');

        expect($step->getCompletedIcon())->toBe('heroicon-o-check');
    });

    it('can set `completedIcon()` with a `BackedEnum`', function (): void {
        $step = Step::make('Account')->completedIcon(Heroicon::Check);

        expect($step->getCompletedIcon())->toBe(Heroicon::Check);
    });

    it('can set `completedIcon()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->completedIcon(static fn (): string => 'heroicon-o-check-circle');

        expect($step->getCompletedIcon())->toBe('heroicon-o-check-circle');
    });

    it('can clear `completedIcon()` with `null`', function (): void {
        $step = Step::make('Account')
            ->completedIcon('heroicon-o-check')
            ->completedIcon(null);

        expect($step->getCompletedIcon())->toBeNull();
    });
});

describe('validation callbacks', function (): void {
    it('can set `afterValidation()` and call it', function (): void {
        $called = false;

        $step = Step::make('Account')
            ->afterValidation(static function () use (&$called): void {
                $called = true;
            });

        $step->callAfterValidation();

        expect($called)->toBeTrue();
    });

    it('can set `beforeValidation()` and call it', function (): void {
        $called = false;

        $step = Step::make('Account')
            ->beforeValidation(static function () use (&$called): void {
                $called = true;
            });

        $step->callBeforeValidation();

        expect($called)->toBeTrue();
    });

    it('can clear `afterValidation()` with `null`', function (): void {
        $called = false;

        $step = Step::make('Account')
            ->afterValidation(static function () use (&$called): void {
                $called = true;
            })
            ->afterValidation(null);

        $step->callAfterValidation();

        expect($called)->toBeFalse();
    });

    it('can clear `beforeValidation()` with `null`', function (): void {
        $called = false;

        $step = Step::make('Account')
            ->beforeValidation(static function () use (&$called): void {
                $called = true;
            })
            ->beforeValidation(null);

        $step->callBeforeValidation();

        expect($called)->toBeFalse();
    });
});

describe('form wrapper', function (): void {
    it('defaults `hasFormWrapper()` to `true`', function (): void {
        $step = Step::make('Account');

        expect($step->hasFormWrapper())->toBeTrue();
    });

    it('can set `formWrapper()` to `false`', function (): void {
        $step = Step::make('Account')->formWrapper(false);

        expect($step->hasFormWrapper())->toBeFalse();
    });

    it('can set `formWrapper()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->formWrapper(static fn (): bool => false);

        expect($step->hasFormWrapper())->toBeFalse();
    });
});

describe('label', function (): void {
    it('reports `hasCustomLabel()` as `true` after construction', function (): void {
        $step = Step::make('Account');

        expect($step->hasCustomLabel())->toBeTrue();
    });

    it('can set `label()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->label(static fn (): string => 'Dynamic');

        expect($step->getLabel())->toBe('Dynamic');
    });

    it('can set `label()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<strong>Bold</strong>');
        $step = Step::make('Account')->label($htmlable);

        expect($step->getLabel())->toBe($htmlable);
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $step = Step::make('Account');

        expect($step->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $step = Step::make('Account')->hiddenLabel();

        expect($step->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $step = Step::make('Account')
            ->hiddenLabel(static fn (): bool => true);

        expect($step->isLabelHidden())->toBeTrue();
    });

    it('can translate label with `translateLabel()`', function (): void {
        $step = Step::make('Account')
            ->label('validation.required')
            ->translateLabel();

        expect($step->getLabel())->toBe(__('validation.required'));
    });
});
