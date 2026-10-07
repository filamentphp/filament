<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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

it('restores query-string tabs using relative keys and legacy IDs', function (mixed $query, int $expected): void {
    request()->query->replace(['tab' => $query, 'delivery_tab' => 'account']);

    Schema::make(Livewire::make())->key('form')->components([
        $profile = Tabs::make('Profile')->key('profile')->persistTabInQueryString()->activeTab(2)->tabs([
            Tab::make('Account')->key('account')->id('profile-account'),
            Tab::make('Contact')->key('contact')->id('profile-contact'),
        ]),
        Group::make([
            $delivery = Tabs::make('Delivery')->key('profile')->persistTabInQueryString('delivery_tab')->activeTab(2)->tabs([
                Tab::make('Account')->key('account')->id('delivery-account'),
                Tab::make('Contact')->key('contact')->id('delivery-contact'),
            ]),
        ])->key('delivery'),
    ])->fill();

    expect($profile->getActiveTab())->toBe($expected)
        ->and($delivery->getActiveTab())->toBe(1)
        ->and($profile->toHtml())->toContain("activeTab: {$expected}")
        ->and($delivery->toHtml())->toContain('activeTab: 1');
})->with([
    'relative key' => ['account', 1],
    'legacy custom ID' => ['profile-account', 1],
    'absolute key is not a persisted tab key' => ['form.profile.account', 2],
    'stale key' => ['removed', 2],
    'missing value' => [null, 2],
    'array value' => [['account'], 2],
]);

it('prefers query-string tab keys before legacy IDs', function (string $query, int $expected): void {
    request()->query->replace(['tab' => $query]);

    Schema::make(Livewire::make())->key('form')->components([
        $tabs = Tabs::make('Profile')->key('profile')->persistTabInQueryString()->tabs([
            Tab::make('Account')->key('account')->id('contact'),
            Tab::make('Contact')->key('contact')->id('profile-contact'),
            Tab::make('Billing')->key('billing'),
        ]),
    ])->fill();

    expect($tabs->getActiveTab())->toBe($expected);
})->with([
    'relative key before another tab custom ID' => ['contact', 2],
    'legacy custom ID' => ['profile-contact', 2],
    'legacy default absolute ID' => ['form.profile.billing', 3],
]);

it('persists independent tabs across reload and browser history', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/tabs-browser-test?delivery_tab=contact')
        ->assertVisible('#profile-account')
        ->assertVisible('#delivery-contact')
        ->click('#profile-tabs [data-tab-key="contact"]')
        ->assertVisible('#profile-contact');

    expect($browser->script('new URL(location.href).searchParams.get("tab")'))->toBe('contact');
    expect($browser->script('new URL(location.href).searchParams.get("delivery_tab")'))->toBe('contact');

    $browser->script("Livewire.navigate('/wizard-browser-test')");
    $browser->assertVisible('#profile-wizard')
        ->back()->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->forward()->assertVisible('#profile-wizard')
        ->back()->assertVisible('#profile-contact')->assertVisible('#delivery-contact');

    $browser->refresh()
        ->assertVisible('#profile-contact')
        ->assertVisible('#delivery-contact')
        ->assertNoAccessibilityIssues()
        ->navigate('/tabs-browser-test?tab=account&delivery_tab=account')
        ->assertVisible('#profile-account')
        ->assertVisible('#delivery-account')
        ->back()->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->forward()->assertVisible('#profile-account')->assertVisible('#delivery-account')
        ->navigate('/tabs-browser-test?tab=profile-contact&delivery_tab=delivery-contact')
        ->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->navigate('/tabs-browser-test?tab=removed&delivery_tab=delivery-contact')
        ->assertVisible('#profile-account')->assertVisible('#delivery-contact')
        ->assertNoSmoke();

    visit('/tabs-browser-test?tab=contact&delivery_tab=contact')->inDarkMode()
        ->assertVisible('#profile-contact')->assertVisible('#delivery-contact')
        ->assertNoAccessibilityIssues();
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
            expect($badges['1']['badge'])->toBe('42');
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

it('renders `wire:click="$set(...)"` on each tab when `livewireProperty()` is set', function (): void {
    $livewire = new class extends Livewire
    {
        public ?string $activeTab = 'all';
    };

    Schema::make($livewire)
        ->components([
            $tabs = Tabs::make()
                ->livewireProperty('activeTab')
                ->tabs([
                    'all' => Tab::make('All'),
                    'active' => Tab::make('Active'),
                ]),
        ])
        ->fill();

    $html = $tabs->toHtml();

    expect($html)
        ->toContain("\$set('activeTab', 'all')")
        ->toContain("\$set('activeTab', 'active')")
        ->not->toContain('wire:loading.attr="disabled"');
});

it('can call an `Action` nested inside a tab that uses `livewireProperty()`', function (): void {
    livewire(TabsWithLivewirePropertyAction::class)
        ->callAction(TestAction::make('set_value')->schemaComponent('test-tabs.first'))
        ->assertSet('actionCalled', true);
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

class TabsWithLivewirePropertyAction extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?string $activeTab = 'first';

    public bool $actionCalled = false;

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
                    ->livewireProperty('activeTab')
                    ->tabs([
                        'first' => Tab::make('First')
                            ->schema([
                                Action::make('set_value')
                                    ->action(fn (TabsWithLivewirePropertyAction $livewire) => $livewire->actionCalled = true),
                            ]),
                        'second' => Tab::make('Second')
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

    it('can render a tab badge icon with a `BackedEnum`', function (): void {
        livewire(RenderTabsWithBackedEnumBadgeIcon::class)
            ->assertSuccessful()
            ->assertSee('Available');
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
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/tabs-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('supports the keyboard in a non-scrollable `Tabs` overflow popup', function (): void {
    $this->actingAs(User::factory()->create());

    $trigger = '#overflow-tabs .fi-dropdown-trigger button:visible';
    $item = '#overflow-tabs .fi-dropdown-panel .fi-dropdown-list-item:first-child:visible';

    $page = visit('/tabs-browser-test?overflow=1');

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage
            ->resize(375, 812)
            ->assertVisible($trigger)
            ->click($trigger)
            ->assertVisible($item)
            ->assertAttribute($trigger, 'aria-haspopup', 'true')
            ->keys($trigger, 'Tab')
            ->assertScript("document.activeElement.matches('#overflow-tabs .fi-dropdown-panel .fi-dropdown-list-item')", true)
            ->keys($item, 'Enter')
            ->assertMissing($item)
            ->assertAttribute($trigger, 'aria-expanded', 'false')
            ->keys($trigger, 'Enter')
            ->assertVisible($item)
            ->assertScript("Array.from(document.querySelectorAll('#overflow-tabs .fi-dropdown-trigger button')).find((trigger) => trigger.checkVisibility()).getAttribute('aria-controls') === document.querySelector('#overflow-tabs .fi-dropdown-panel').id", true)
            ->assertNoSmoke();

        $themedPage->script('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))).then(() => Promise.all(document.getAnimations().filter(animation => animation.effect.getComputedTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {}))))');
        $themedPage->assertNoAccessibilityIssues();
    }
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

class RenderTabsWithBackedEnumBadgeIcon extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([])
            ->components([
                Tabs::make('Status')
                    ->tabs([
                        Tab::make('Products')
                            ->badge('Available')
                            ->badgeIcon(Heroicon::OutlinedCheckCircle),
                    ]),
            ]);
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
