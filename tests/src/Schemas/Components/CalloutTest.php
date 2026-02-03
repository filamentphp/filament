<?php

namespace Filament\Tests\Schemas\Components;

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Schema;
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

it('has no accessibility issues in light mode', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/callout-browser-test')
        ->assertNoAccessibilityIssues();
});

it('has no accessibility issues in dark mode', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/callout-browser-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
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
