<?php

namespace Filament\Tests\Schemas\Components;

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithCallout::class)
        ->assertSuccessful();
});

it('can render with `heading()`', function (): void {
    livewire(TestComponentWithCalloutHeading::class)
        ->assertSuccessful()
        ->assertSee('Test Heading');
});

it('can render with `description()`', function (): void {
    livewire(TestComponentWithCalloutDescription::class)
        ->assertSuccessful()
        ->assertSee('Test description text');
});

it('can use `danger()` status', function (): void {
    livewire(TestComponentWithCalloutDanger::class)
        ->assertSuccessful();
});

it('can use `info()` status', function (): void {
    livewire(TestComponentWithCalloutInfo::class)
        ->assertSuccessful();
});

it('can use `success()` status', function (): void {
    livewire(TestComponentWithCalloutSuccess::class)
        ->assertSuccessful();
});

it('can use `warning()` status', function (): void {
    livewire(TestComponentWithCalloutWarning::class)
        ->assertSuccessful();
});

it('can use custom `color()`', function (): void {
    livewire(TestComponentWithCalloutColor::class)
        ->assertSuccessful();
});

it('can use custom `icon()`', function (): void {
    livewire(TestComponentWithCalloutIcon::class)
        ->assertSuccessful();
});

it('can use `actions()`', function (): void {
    livewire(TestComponentWithCalloutActions::class)
        ->assertSuccessful()
        ->assertSeeHtml('Learn More');
});

it('can call footer `actions()`', function (): void {
    livewire(TestComponentWithCallableAction::class)
        ->callAction(TestAction::make('set_value')->schemaComponent())
        ->assertSet('actionCalled', true);
});

it('can use `controlActions()`', function (): void {
    livewire(TestComponentWithCalloutControlActions::class)
        ->assertSuccessful()
        ->assertSeeHtml('dismiss');
});

it('can call `controlActions()`', function (): void {
    livewire(TestComponentWithCallableControlsAction::class)
        ->callAction(TestAction::make('set_value')->schemaComponent())
        ->assertSet('actionCalled', true);
});

it('has no accessibility issues in light mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/callout-browser-test')
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues in dark mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/callout-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

describe('rendering', function (): void {
    it('can render with `status()` set via `Closure`', function (): void {
        livewire(RenderCalloutWithClosureStatus::class)->assertSuccessful();
    });

    it('can render with `heading()` set via `Closure`', function (): void {
        livewire(RenderCalloutWithClosureHeading::class)
            ->assertSuccessful()
            ->assertSee('Dynamic Heading');
    });

    it('can render with `description()` set via `Closure`', function (): void {
        livewire(RenderCalloutWithClosureDescription::class)
            ->assertSuccessful()
            ->assertSee('Dynamic desc');
    });

    it('can render with `color()` set via `Closure`', function (): void {
        livewire(RenderCalloutWithClosureColor::class)->assertSuccessful();
    });

    it('can render with `icon()` set via `Closure`', function (): void {
        livewire(RenderCalloutWithClosureIcon::class)->assertSuccessful();
    });
});

describe('Closure support', function (): void {
    it('can set `status()` with a `Closure`', function (): void {
        $callout = Callout::make('Test')
            ->status(static fn (): string => 'warning');

        expect($callout->getStatus())->toBe('warning');
    });

    it('can set `heading()` with a `Closure`', function (): void {
        $callout = Callout::make(static fn (): string => 'Dynamic Heading');

        expect($callout->getHeading())->toBe('Dynamic Heading');
    });

    it('can set `description()` with a `Closure`', function (): void {
        $callout = Callout::make('Test')
            ->description(static fn (): string => 'Dynamic desc');

        expect($callout->getDescription())->toBe('Dynamic desc');
    });

    it('can set `color()` with a `Closure`', function (): void {
        $callout = Callout::make('Test')
            ->color(static fn (): string => 'primary');

        expect($callout->getColor())->toBe('primary');
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $callout = Callout::make('Test')
            ->icon(static fn () => Heroicon::Check);

        expect($callout->getIcon())->toBe(Heroicon::Check);
    });
});

describe('computed icon and color from status', function (): void {
    it('returns a default icon for `danger` status', function (): void {
        $callout = Callout::make('Error')->danger();

        expect($callout->getIcon())->not->toBeNull();
    });

    it('returns a default icon for `info` status', function (): void {
        $callout = Callout::make('Info')->info();

        expect($callout->getIcon())->not->toBeNull();
    });

    it('returns a default icon for `success` status', function (): void {
        $callout = Callout::make('Done')->success();

        expect($callout->getIcon())->not->toBeNull();
    });

    it('returns a default icon for `warning` status', function (): void {
        $callout = Callout::make('Warn')->warning();

        expect($callout->getIcon())->not->toBeNull();
    });

    it('returns `null` icon when no status and no custom icon', function (): void {
        $callout = Callout::make('Plain');

        expect($callout->getIcon())->toBeNull();
    });

    it('returns status as `getIconColor()` when no custom icon color', function (): void {
        $callout = Callout::make('Warn')->warning();

        expect($callout->getIconColor())->toBe('warning');
    });

    it('returns status as `getColor()` when no explicit color', function (): void {
        $callout = Callout::make('Error')->danger();

        expect($callout->getColor())->toBe('danger');
    });

    it('returns explicit color from `getColor()` when `color()` is set', function (): void {
        $callout = Callout::make('Test')
            ->danger()
            ->color('primary');

        expect($callout->getColor())->toBe('primary');
    });
});

describe('slot methods', function (): void {
    it('returns fluent `$this` from `footer()`', function (): void {
        $callout = Callout::make('Test');

        expect($callout->footer([]))->toBe($callout);
    });

    it('returns fluent `$this` from `controls()`', function (): void {
        $callout = Callout::make('Test');

        expect($callout->controls([]))->toBe($callout);
    });

    it('returns fluent `$this` from `actions()`', function (): void {
        $callout = Callout::make('Test');

        expect($callout->actions([]))->toBe($callout);
    });
});

class TestComponentWithCallout extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Notice'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutHeading extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Test Heading'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutDescription extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Heading')
                    ->description('Test description text'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutDanger extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Error')
                    ->description('Something went wrong.')
                    ->danger(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutInfo extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Information')
                    ->description('Here is some helpful information.')
                    ->info(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutSuccess extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Success')
                    ->description('Operation completed successfully.')
                    ->success(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutWarning extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Warning')
                    ->description('Please be careful.')
                    ->warning(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Custom Color')
                    ->description('Using a custom color.')
                    ->color('purple'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Custom Icon')
                    ->description('With a custom icon.')
                    ->icon('heroicon-o-bell')
                    ->iconColor('primary'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutActions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Notice with Actions')
                    ->description('This callout has actions.')
                    ->info()
                    ->actions([
                        Action::make('learn_more')
                            ->label('Learn More'),
                        Action::make('dismiss')
                            ->label('Dismiss'),
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCallableAction extends Livewire
{
    public bool $actionCalled = false;

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Notice')
                    ->actions([
                        Action::make('set_value')
                            ->label('Set Value')
                            ->action(fn (TestComponentWithCallableAction $livewire) => $livewire->actionCalled = true),
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCalloutControlActions extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Notice with Control Actions')
                    ->description('This callout has control actions.')
                    ->info()
                    ->controlActions([
                        Action::make('dismiss')
                            ->icon('heroicon-o-x-mark')
                            ->iconButton(),
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCallableControlsAction extends Livewire
{
    public bool $actionCalled = false;

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Callout::make('Notice')
                    ->controlActions([
                        Action::make('set_value')
                            ->action(fn (TestComponentWithCallableControlsAction $livewire) => $livewire->actionCalled = true),
                    ]),
            ])
            ->statePath('data');
    }
}

class RenderCalloutWithClosureStatus extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Callout::make('Notice')->status(static fn (): string => 'warning'),
        ])->statePath('data');
    }
}

class RenderCalloutWithClosureHeading extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Callout::make(static fn (): string => 'Dynamic Heading'),
        ])->statePath('data');
    }
}

class RenderCalloutWithClosureDescription extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Callout::make('Test')->description(static fn (): string => 'Dynamic desc'),
        ])->statePath('data');
    }
}

class RenderCalloutWithClosureColor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Callout::make('Test')->color(static fn (): string => 'primary'),
        ])->statePath('data');
    }
}

class RenderCalloutWithClosureIcon extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Callout::make('Test')->icon(static fn () => Heroicon::Check),
        ])->statePath('data');
    }
}
