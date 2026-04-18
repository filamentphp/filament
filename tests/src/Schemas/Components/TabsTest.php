<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

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

it('can set `activeTab()`', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->getActiveTab())->toBe(1);

    $tabs->activeTab(3);

    expect($tabs->getActiveTab())->toBe(3);
});

it('can set `persistTabInQueryString()`', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->isTabPersistedInQueryString())->toBeFalse();

    $tabs->persistTabInQueryString();

    expect($tabs->isTabPersistedInQueryString())->toBeTrue();
    expect($tabs->getTabQueryStringKey())->toBe('tab');
});

it('can set custom key for `persistTabInQueryString()`', function (): void {
    $tabs = Tabs::make('Test')
        ->persistTabInQueryString('activeTab');

    expect($tabs->getTabQueryStringKey())->toBe('activeTab');
});

it('can set `startRenderHooks()`', function (): void {
    $tabs = Tabs::make('Test')
        ->startRenderHooks(['hook-a', 'hook-b']);

    expect($tabs->getStartRenderHooks())->toBe(['hook-a', 'hook-b']);
});

it('can set `endRenderHooks()`', function (): void {
    $tabs = Tabs::make('Test')
        ->endRenderHooks(['hook-c']);

    expect($tabs->getEndRenderHooks())->toBe(['hook-c']);
});

it('can set `livewireProperty()`', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->getLivewireProperty())->toBeNull();

    $tabs->livewireProperty('activeTab');

    expect($tabs->getLivewireProperty())->toBe('activeTab');
});

it('defaults to scrollable', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->isScrollable())->toBeTrue();
});

it('can disable `scrollable()`', function (): void {
    $tabs = Tabs::make('Test')
        ->scrollable(false);

    expect($tabs->isScrollable())->toBeFalse();
});

it('can set `vertical()`', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->isVertical())->toBeFalse();

    $tabs->vertical();

    expect($tabs->isVertical())->toBeTrue();
});

it('can set `activeTab()` with a `Closure`', function (): void {
    $tabs = Tabs::make('Test')
        ->activeTab(static fn (): int => 2);

    expect($tabs->getActiveTab())->toBe(2);
});

it('can clear `persistTabInQueryString()` with `null`', function (): void {
    $tabs = Tabs::make('Test')
        ->persistTabInQueryString()
        ->persistTabInQueryString(null);

    expect($tabs->isTabPersistedInQueryString())->toBeFalse();
    expect($tabs->getTabQueryStringKey())->toBeNull();
});

it('can set `scrollable()` with a `Closure`', function (): void {
    $tabs = Tabs::make('Test')
        ->scrollable(static fn (): bool => false);

    expect($tabs->isScrollable())->toBeFalse();
});

it('can set `vertical()` with a `Closure`', function (): void {
    $tabs = Tabs::make('Test')
        ->vertical(static fn (): bool => true);

    expect($tabs->isVertical())->toBeTrue();
});

it('can set `livewireProperty()` with a `Closure`', function (): void {
    $tabs = Tabs::make('Test')
        ->livewireProperty(static fn (): string => 'dynamicProp');

    expect($tabs->getLivewireProperty())->toBe('dynamicProp');
});

it('returns fluent `$this` from `tabs()`', function (): void {
    $tabs = Tabs::make('Test');

    $result = $tabs->tabs([]);

    expect($result)->toBe($tabs);
});

it('defaults to empty arrays for render hooks', function (): void {
    $tabs = Tabs::make('Test');

    expect($tabs->getStartRenderHooks())->toBe([]);
    expect($tabs->getEndRenderHooks())->toBe([]);
});

it('can set `persistTabInQueryString()` with a `Closure`', function (): void {
    $tabs = Tabs::make('Test')
        ->persistTabInQueryString(static fn (): string => 'dynamicKey');

    expect($tabs->getTabQueryStringKey())->toBe('dynamicKey');
    expect($tabs->isTabPersistedInQueryString())->toBeTrue();
});

describe('tab persistence', function (): void {
    it('defaults `isTabPersisted()` to `false`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->isTabPersisted())->toBeFalse();
    });

    it('can set `persistTab()`', function (): void {
        $tabs = Tabs::make('Test')->persistTab();

        expect($tabs->isTabPersisted())->toBeTrue();
    });

    it('can set `persistTab()` to `false`', function (): void {
        $tabs = Tabs::make('Test')->persistTab()->persistTab(false);

        expect($tabs->isTabPersisted())->toBeFalse();
    });

    it('can set `persistTab()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->persistTab(static fn (): bool => true);

        expect($tabs->isTabPersisted())->toBeTrue();
    });
});

describe('label', function (): void {
    it('can be constructed with a label', function (): void {
        $tabs = Tabs::make('Settings');

        expect($tabs->getLabel())->toBe('Settings');
    });

    it('returns `null` for `getLabel()` when no label given', function (): void {
        $tabs = Tabs::make();

        expect($tabs->getLabel())->toBeNull();
    });

    it('can set `label()` with a `Closure`', function (): void {
        $tabs = Tabs::make()
            ->label(static fn (): string => 'Dynamic');

        expect($tabs->getLabel())->toBe('Dynamic');
    });

    it('can set `label()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<strong>Bold</strong>');
        $tabs = Tabs::make()->label($htmlable);

        expect($tabs->getLabel())->toBe($htmlable);
    });

    it('reports `hasCustomLabel()` as `false` by default', function (): void {
        $tabs = Tabs::make();

        expect($tabs->hasCustomLabel())->toBeFalse();
    });

    it('reports `hasCustomLabel()` as `true` after `label()` is set', function (): void {
        $tabs = Tabs::make('Label');

        expect($tabs->hasCustomLabel())->toBeTrue();
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $tabs = Tabs::make('Test')->hiddenLabel();

        expect($tabs->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->hiddenLabel(static fn (): bool => true);

        expect($tabs->isLabelHidden())->toBeTrue();
    });

    it('can translate label with `translateLabel()`', function (): void {
        $tabs = Tabs::make()
            ->label('validation.required')
            ->translateLabel();

        expect($tabs->getLabel())->toBe(__('validation.required'));
    });
});

describe('containment', function (): void {
    it('defaults `isContained()` to `true`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->isContained())->toBeTrue();
    });

    it('can set `contained()` to `false`', function (): void {
        $tabs = Tabs::make('Test')->contained(false);

        expect($tabs->isContained())->toBeFalse();
    });

    it('can set `contained()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->contained(static fn (): bool => false);

        expect($tabs->isContained())->toBeFalse();
    });
});

describe('extra Alpine attributes', function (): void {
    it('returns empty array for `getExtraAlpineAttributes()` by default', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->getExtraAlpineAttributes())->toBe([]);
    });

    it('can set `extraAlpineAttributes()`', function (): void {
        $tabs = Tabs::make('Test')
            ->extraAlpineAttributes(['x-on:click' => 'open = true']);

        expect($tabs->getExtraAlpineAttributes())->toBe(['x-on:click' => 'open = true']);
    });

    it('can merge `extraAlpineAttributes()`', function (): void {
        $tabs = Tabs::make('Test')
            ->extraAlpineAttributes(['x-on:click' => 'open = true'])
            ->extraAlpineAttributes(['x-bind:class' => 'active'], merge: true);

        $attributes = $tabs->getExtraAlpineAttributes();

        expect($attributes)->toHaveKey('x-on:click', 'open = true');
        expect($attributes)->toHaveKey('x-bind:class', 'active');
    });

    it('can set `extraAlpineAttributes()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->extraAlpineAttributes(static fn (): array => ['x-data' => '{}']);

        expect($tabs->getExtraAlpineAttributes())->toBe(['x-data' => '{}']);
    });
});

describe('render hook scopes', function (): void {
    it('returns empty array for `getRenderHookScopes()` when Livewire does not implement `HasRenderHookScopes`', function (): void {
        $tabs = Tabs::make('Test')
            ->container(Schema::make(Livewire::make()));

        expect($tabs->getRenderHookScopes())->toBe([]);
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

describe('rendering', function (): void {
    it('can render basic `Tabs`', function (): void {
        livewire(RenderTabs::class)->assertSuccessful();
    });

    it('can render with `scrollable(false)`', function (): void {
        livewire(RenderTabsWithScrollableFalse::class)->assertSuccessful();
    });

    it('can render with `scrollable()` set via `Closure`', function (): void {
        livewire(RenderTabsWithClosureScrollable::class)->assertSuccessful();
    });

    it('can render with `vertical()`', function (): void {
        livewire(RenderTabsWithVertical::class)->assertSuccessful();
    });

    it('can render with `vertical()` set via `Closure`', function (): void {
        livewire(RenderTabsWithClosureVertical::class)->assertSuccessful();
    });

    it('can render with `contained(false)`', function (): void {
        livewire(RenderTabsWithContainedFalse::class)->assertSuccessful();
    });

    it('can render with `persistTabInQueryString()`', function (): void {
        livewire(RenderTabsWithPersistTab::class)->assertSuccessful();
    });

    it('can render with `persistTab()`', function (): void {
        livewire(RenderTabsWithPersistTabLocal::class)->assertSuccessful();
    });

    it('can render with label', function (): void {
        livewire(RenderTabsWithLabel::class)->assertSuccessful()->assertSee('My Tabs');
    });

    it('can render with `label()` set via `Closure`', function (): void {
        livewire(RenderTabsWithClosureLabel::class)->assertSuccessful()->assertSee('Dynamic');
    });
});

it('can render `Tabs` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/tabs-browser-test')
            ->assertSee('Account')
            ->assertSee('Contact')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/tabs-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderTabs extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1'), Tab::make('Tab 2')])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithScrollableFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->scrollable(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithClosureScrollable extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->scrollable(static fn (): bool => false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithVertical extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->vertical()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithClosureVertical extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->vertical(static fn (): bool => true)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithContainedFalse extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->contained(false)]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithPersistTab extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->persistTabInQueryString()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithPersistTabLocal extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('Test')->tabs([Tab::make('Tab 1')])->persistTab()]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make('My Tabs')->tabs([Tab::make('Tab 1')])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}

class RenderTabsWithClosureLabel extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema->state([])->components([Tabs::make(static fn (): string => 'Dynamic')->tabs([Tab::make('Tab 1')])]);
    }

    public function render(): string
    {
        return '<div>{{ $this->infolist }}</div>';
    }
}
