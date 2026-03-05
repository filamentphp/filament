<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('defaults `isBadgeDeferred()` to `false`', function (): void {
    $tab = Tab::make('Test');

    expect($tab->isBadgeDeferred())->toBeFalse();
});

it('can set `deferBadge()`', function (): void {
    $tab = Tab::make('Test')->deferBadge();

    expect($tab->isBadgeDeferred())->toBeTrue();
});

it('can unset `deferBadge()`', function (): void {
    $tab = Tab::make('Test')->deferBadge()->deferBadge(false);

    expect($tab->isBadgeDeferred())->toBeFalse();
});

it('can detect deferred badges with `hasDeferredBadges()`', function (): void {
    livewire(TabsWithDeferredBadges::class)
        ->assertOk();
});

it('can return deferred tab badges with `getDeferredTabBadges()`', function (): void {
    livewire(TabsWithDeferredBadges::class)
        ->call('callSchemaComponentMethod', 'form.test-tabs', 'getDeferredTabBadges')
        ->assertReturned(function (array $badges): bool {
            expect($badges)->toHaveCount(1);
            expect($badges)->toHaveKey('1');
            expect($badges['1']['badge'])->toBe(42);
            expect($badges)->not->toHaveKey('0');

            return true;
        });
});

class TabsWithDeferredBadges extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Test')
                    ->key('test-tabs')
                    ->tabs([
                        Tab::make('Normal Tab')
                            ->badge(10)
                            ->schema([]),
                        Tab::make('Deferred Tab')
                            ->badge(static fn (): int => 42)
                            ->deferBadge()
                            ->schema([]),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
