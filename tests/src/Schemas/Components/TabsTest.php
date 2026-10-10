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

    $browser = visit('/tabs-browser-test?disabled-profile=1&tab=profile-contact');
    foreach ([$browser, $browser->inDarkMode()] as $page) {
        $page->assertVisible('#profile-contact')
            ->assertAttribute('#profile-tabs [data-tab-key="contact"]', 'aria-selected', 'true')
            ->assertNoAccessibilityIssues();
    }
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

it('can opt out of panels using `tabPanels()` and a `Closure`', function (): void {
    $livewire = new class extends Livewire
    {
        public ?string $activeTab = 'all';
    };

    Schema::make($livewire)->components([
        $tabs = Tabs::make('Status')->livewireProperty('activeTab')->tabPanels(static fn (): bool => false)->tabs([
            'all' => Tab::make('All'),
            'active' => Tab::make('Active'),
        ]),
    ])->fill();

    expect($tabs->hasTabPanels())->toBeFalse()
        ->and($tabs->toHtml())->toContain('role="group"', 'aria-pressed="true"')
        ->not->toContain('role="tab"', 'role="tabpanel"', 'aria-controls=');

    expect($tabs->tabPanels()->hasTabPanels())->toBeTrue();
});

it('links stable tab IDs to custom and empty panel IDs without changing keys', function (): void {
    $livewire = new class extends Livewire
    {
        public ?string $activeTab = null;
    };

    Schema::make($livewire)->key('form')->components([
        $tabs = Tabs::make('Details')->key('details')->livewireProperty('activeTab')->tabs([
            '' => $empty = Tab::make('All'),
            '0' => $zero = Tab::make('Zero')->id('zero-panel')->extraAttributes(['id' => 'zero-header']),
        ]),
        $otherTabs = Tabs::make('Other')->key('other')->scrollable(false)->tabs([
            $other = Tab::make(new HtmlString('All &amp; <strong>&quot;other&quot;</strong>'))->key('')->extraAttributes(['id' => 'other-header']),
        ]),
        Group::make([
            Tabs::make()->tabs([$billing = Tab::make('Account')->key('account')]),
        ])->key('billing'),
        Group::make([
            Tabs::make()->tabs([$shipping = Tab::make('Account')->key('account')]),
        ])->key('shipping'),
    ])->fill();

    $html = $tabs->toHtml();
    expect($empty->getKey(isAbsolute: false))->toBe('')
        ->and($zero->getKey(isAbsolute: false))->toBe('0')
        ->and($zero->getPanelId())->toBe('zero-panel')
        ->and($zero->getTabId())->toBe('zero-header')
        ->and(substr_count($html, 'id="zero-header"'))->toBe(1)
        ->and(substr_count($otherTabs->toHtml(), 'id="other-header"'))->toBe(1)
        ->and($otherTabs->toHtml())->toContain('aria-label="More tabs: All &amp; &quot;other&quot;"')
        ->and($empty->getTabId())->not->toBe($other->getTabId())
        ->and($billing->getPanelId())->toBe('form.billing.account')
        ->and($shipping->getPanelId())->toBe('form.shipping.account')
        ->and($billing->getTabId())->not->toBe($shipping->getTabId())
        ->and($html)->toContain(
            'id="' . $empty->getTabId() . '"',
            'aria-labelledby="' . $empty->getTabId() . '"',
            'aria-controls="' . $empty->getPanelId() . '"',
            'id="' . $empty->getPanelId() . '"',
            'aria-controls="zero-panel"',
            'aria-labelledby="zero-header"',
            'id="zero-panel"',
        );
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

it('keeps non-scrollable `Tabs` in one keyboard sequence and accessible tablist', function (): void {
    $this->actingAs(User::factory()->create());

    $trigger = '#overflow-tabs [data-tabs-overflow-trigger]:visible';
    $overview = '#overflow-tabs [data-tab-key="overview"]';
    $contact = '#overflow-tabs [data-tab-key="contact"]';
    $billing = '#overflow-tabs [data-tab-key="billing"]';
    $notifications = '#overflow-tabs [data-tab-key="notifications"]';
    $popup = '#overflow-tabs [x-ref="overflowPanel"]';

    $page = visit('/tabs-browser-test?overflow=1');

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage
            ->resize(375, 812)
            ->assertVisible($trigger)
            ->assertAttribute($trigger, 'aria-hidden', 'true')
            ->assertAttribute($trigger, 'tabindex', '-1')
            ->assertAttribute($overview, 'aria-selected', 'true')
            ->keys($overview, 'ArrowRight')
            ->assertVisible($popup)
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->assertScript('document.activeElement.getAttribute("role")', 'tab')
            ->assertScript('document.activeElement.closest("[aria-hidden=true]") === null', true)
            ->assertScript('document.activeElement.getAttribute("aria-controls") === document.querySelector("#overflow-tabs > [role=tabpanel][aria-labelledby=\"" + document.activeElement.id + "\"]").id', true)
            ->assertAttribute($overview, 'aria-selected', 'true')
            ->keys($contact, 'End')
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->keys($notifications, 'ArrowRight')
            ->assertScript('document.activeElement.dataset.tabKey', 'overview');

        $themedPage->script('document.querySelector("#overflow-tabs [data-tab-key=contact]").disabled = true');
        $themedPage->keys($overview, 'ArrowRight')
            ->assertScript('document.activeElement.dataset.tabKey', 'billing');
        $themedPage->script('document.querySelector("#overflow-tabs [data-tab-key=contact]").disabled = false');
        $themedPage->keys($billing, 'ArrowLeft')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->keys($contact, 'Enter')
            ->assertAttribute($contact, 'aria-selected', 'true')
            ->assertVisible($popup)
            ->assertScript('document.activeElement.dataset.tabKey', 'contact');
        $themedPage->script('document.getElementById("overflow-tabs").style.transform = "scale(.8)"');
        $themedPage->assertScript('(() => { const focused = document.activeElement.getBoundingClientRect(); const row = document.querySelector("#overflow-tabs [data-tab-menu-key=contact]").getBoundingClientRect(); return Math.abs(focused.x - row.x) < 1 && Math.abs(focused.y - row.y) < 1 && Math.abs(focused.width - row.width) < 1 && Math.abs(focused.height - row.height) < 1 })()', true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
        $themedPage->script('document.getElementById("overflow-tabs").style.transform = ""');
        $themedPage->keys($contact, 'End')
            ->resize(375, 180)
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true);
        $themedPage->script('Alpine.$data(document.querySelector("[data-testid=tabs-form]")).showOverflowChoice = true');
        $themedPage->assertVisible('#overflow-tabs [data-tab-key=hidden]')
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true);
        $themedPage->script('Alpine.$data(document.querySelector("[data-testid=tabs-form]")).showOverflowChoice = false');
        $themedPage->keys($notifications, 'Home')
            ->assertScript('document.activeElement.dataset.tabKey', 'overview')
            ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true)
            ->assertScript('document.activeElement.dispatchEvent(new WheelEvent("wheel", { deltaY: 250, ctrlKey: true, bubbles: true, cancelable: true }))', true);

        // Measure the wheel response synchronously, before overflow positioning can scroll the focused row back into view.
        $wheelResponse = $themedPage->script('(() => { const panel = document.querySelector("#overflow-tabs [x-ref=overflowPanel]"); const scrollTop = panel.scrollTop; const isUncancelled = document.activeElement.dispatchEvent(new WheelEvent("wheel", { deltaY: 3, bubbles: true, cancelable: true })); return { isUncancelled, distance: panel.scrollTop - scrollTop }; })()');
        expect($wheelResponse)->toBe(['isUncancelled' => false, 'distance' => 3]);

        $wheelResponse = $themedPage->script('(() => { const panel = document.querySelector("#overflow-tabs [x-ref=overflowPanel]"); panel.scrollTop = 0; const isUncancelled = document.activeElement.dispatchEvent(new WheelEvent("wheel", { deltaY: 1, deltaMode: WheelEvent.DOM_DELTA_LINE, bubbles: true, cancelable: true })); return { isUncancelled, distance: panel.scrollTop }; })()');
        expect($wheelResponse['isUncancelled'])->toBeFalse()
            ->and($wheelResponse['distance'])->toBeGreaterThan(1);

        $wheelResponse = $themedPage->script('(() => { const panel = document.querySelector("#overflow-tabs [x-ref=overflowPanel]"); panel.scrollTop = 0; const isUncancelled = document.activeElement.dispatchEvent(new WheelEvent("wheel", { deltaY: 1, deltaMode: WheelEvent.DOM_DELTA_PAGE, bubbles: true, cancelable: true })); return { isUncancelled, isAtBottom: panel.scrollTop === panel.scrollHeight - panel.clientHeight }; })()');
        expect($wheelResponse)->toBe(['isUncancelled' => false, 'isAtBottom' => true]);

        $themedPage->resize(375, 812)
            ->click('#profile-tabs > [role=tabpanel].fi-active input')
            ->assertScript('document.activeElement.matches("#profile-tabs input")', true)
            ->assertMissing($popup . ':visible')
            ->keys($contact, 'Home')
            ->keys($overview, 'ArrowRight')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->keys($contact, 'Escape')
            ->assertMissing($popup . ':visible')
            ->assertScript('document.activeElement.matches("#overflow-tabs > [role=tabpanel].fi-active")', true)
            ->keys('#overflow-tabs > [role=tabpanel].fi-active', 'Shift+Tab')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->assertVisible($popup)
            ->keys($contact, 'Tab')
            ->assertMissing($popup . ':visible')
            ->assertScript('document.activeElement.matches("#overflow-tabs > [role=tabpanel].fi-active")', true);

        $themedPage->keys($overview, 'End')
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->resize(1920, 900)
            ->assertMissing($trigger)
            ->assertMissing($popup . ':visible')
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->keys($notifications, 'Home')
            ->assertScript('document.activeElement.dataset.tabKey', 'overview');
        $themedPage->script('document.querySelector("#overflow-tabs [data-tab-key=contact] .fi-tabs-item-label").textContent = "Contact information and account preferences ".repeat(8)');
        $themedPage->assertVisible($trigger)
            ->assertVisible($popup)
            ->assertScript('document.activeElement.dataset.tabKey', 'overview');
        $themedPage->script('document.querySelector("#overflow-tabs [data-tab-key=contact] .fi-tabs-item-label").textContent = "Contact information"');
        $themedPage->assertMissing($popup . ':visible')
            ->assertScript('document.activeElement.dataset.tabKey', 'overview')
            ->keys($overview, 'End')
            ->resize(375, 180)
            ->assertVisible($popup)
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true)
            ->keys($notifications, 'Home')
            ->keys($overview, 'ArrowRight')
            ->resize(375, 812)
            ->assertVisible($popup)
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->assertAttribute($contact, 'aria-selected', 'true');
        $themedPage->script('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))).then(() => Promise.all(document.getAnimations().filter(animation => animation.effect.getComputedTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {}))))');
        $themedPage->assertNoAccessibilityIssues()
            ->keys($contact, 'Home')
            ->keys($overview, 'Enter')
            ->assertAttribute($overview, 'aria-selected', 'true')
            ->keys($overview, 'Escape');
    }
});

it('dismisses tab overflow before an enclosing dropdown', function (): void {
    $this->actingAs(User::factory()->create());
    $page = visit('/tabs-browser-test?overflow=1&dropdown=1');
    $dropdownTrigger = '[data-testid=enclosing-dropdown-trigger]';
    $dropdownPanel = '[data-testid=enclosing-dropdown] > [x-ref=panel]';
    $overview = '#overflow-tabs [data-tab-key=overview]';
    $notifications = '#overflow-tabs [data-tab-key=notifications]';

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage->resize(375, 812)
            ->click($dropdownTrigger)
            ->assertVisible($dropdownPanel)
            ->keys($overview, 'End')
            ->assertVisible('#overflow-tabs [x-ref=overflowPanel]')
            ->assertScript('document.activeElement.dataset.tabKey', 'notifications')
            ->keys($notifications, 'Escape')
            ->assertMissing('#overflow-tabs [x-ref=overflowPanel]:visible')
            ->assertVisible($dropdownPanel)
            ->assertAttribute($overview, 'aria-selected', 'true');
        $themedPage->script('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))).then(() => Promise.all(document.getAnimations().filter(animation => animation.effect.getComputedTiming().iterations !== Infinity).map(animation => animation.finished.catch(() => {}))))');
        $themedPage->assertNoAccessibilityIssues()
            ->keys('#overflow-tabs > [role=tabpanel].fi-active', 'Escape')
            ->assertMissing($dropdownPanel . ':visible')
            ->assertScript('document.activeElement.dataset.testid', 'enclosing-dropdown-trigger');
    }
});

it('uses manual activation, roving focus and scoped `tabpanel` relationships', function (): void {
    $this->actingAs(User::factory()->create());
    $browser = visit('/tabs-browser-test?keyboard=1');
    $account = '#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="account"]';
    $contact = '#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="contact"]';
    $readonly = '#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="readonly"]';

    foreach ([$browser, $browser->inDarkMode()] as $page) {
        $page->assertAttribute($account, 'aria-selected', 'true')
            ->keys($account, 'ArrowRight')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->assertAttribute($account, 'aria-selected', 'true')
            ->assertAttribute($contact, 'tabindex', '0')
            ->assertAttribute($account, 'tabindex', '-1')
            ->keys($contact, 'End')
            ->assertScript('document.activeElement.dataset.tabKey', 'readonly')
            ->keys($readonly, 'ArrowRight')
            ->assertScript("document.activeElement.matches('{$account}')", true)
            ->keys($account, 'ArrowLeft')
            ->assertScript('document.activeElement.dataset.tabKey', 'readonly')
            ->keys($readonly, 'Enter')->assertAttribute($readonly, 'aria-selected', 'true')
            ->assertScript('document.querySelector("#keyboard-tabs > [role=tabpanel].fi-active input").disabled', true)
            ->keys($account, 'End')->keys($readonly, 'Home')
            ->keys($account, 'ArrowRight')->keys($contact, 'Enter')
            ->assertAttribute($contact, 'aria-selected', 'true')
            ->assertScript("document.activeElement.matches('{$contact}')", true)
            ->keys($contact, 'Tab')
            ->assertScript('document.activeElement.getAttribute("role")', 'tabpanel')
            ->assertScript('Array.from(document.querySelectorAll("[role=tab]")).every(tab => { const panel = document.getElementById(tab.getAttribute("aria-controls")); return panel && panel.getAttribute("aria-labelledby") === tab.id })', true)
            ->assertScript('new Set(Array.from(document.querySelectorAll("[id]"), element => element.id)).size === document.querySelectorAll("[id]").length', true)
            ->keys($account, 'Enter')
            ->keys('#nested-tabs [data-tab-key="account"]', 'ArrowRight')
            ->assertScript('document.activeElement.closest(".fi-sc-tabs").id', 'nested-tabs')
            ->keys('#nested-tabs [data-tab-key="contact"]', 'Enter')
            ->assertAttribute($account, 'aria-selected', 'true')
            ->refresh()
            ->assertAttribute('#nested-tabs [data-tab-key="contact"]', 'aria-selected', 'true')
            ->assertAttribute($account, 'aria-selected', 'true');

        $page->script('document.querySelector("#keyboard-tabs [role=tablist]").dir = "rtl"');
        $page->keys($account, 'ArrowLeft')->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->keys($contact, 'ArrowRight')->assertScript('document.activeElement.dataset.tabKey', 'account')
            ->assertAttribute('#vertical-tabs [role=tablist]', 'aria-orientation', 'vertical')
            ->keys('#vertical-tabs [data-tab-key="account"]', 'ArrowDown')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->keys('#vertical-tabs [data-tab-key="contact"]', 'Home')
            ->keys('#vertical-tabs [data-tab-key="account"]', 'ArrowUp')
            ->assertScript('document.activeElement.dataset.tabKey', 'contact')
            ->click('#profile-tabs [data-tab-key="contact"]')
            ->click('[data-testid=save]')->assertVisible('#profile-account')
            ->assertNoSmoke()->assertNoAccessibilityIssues();

        $page->hover('[data-testid=hide-all]');
        $page->script('window.keyboardTabHeaders = Array.from(document.querySelectorAll("#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key], #vertical-tabs > [x-ref=tabsHeader] [data-tab-key]"), element => [element, element.disabled]); window.keyboardTabHeaders.forEach(([element]) => element.disabled = true)');
        $page->assertScript('Alpine.$data(document.getElementById("keyboard-tabs")).availableTabs.length', 0)
            ->assertAttribute('#keyboard-tabs > [x-ref=tabsHeader] > [x-ref=tablist]', 'role', 'tablist')
            ->assertAttribute('#vertical-tabs > [x-ref=tabsHeader] > [x-ref=tablist]', 'role', 'tablist')
            ->assertAttribute('#vertical-tabs > [x-ref=tabsHeader] > [x-ref=tablist]', 'aria-orientation', 'vertical')
            ->assertNoAccessibilityIssues();
        $page->script('window.keyboardTabHeaders.forEach(([element, isDisabled]) => element.disabled = isDisabled)');
        $page->assertAttribute($account, 'tabindex', '0')
            ->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});

it('reconciles client-hidden tabs without stealing outside focus', function (): void {
    $this->actingAs(User::factory()->create());
    $browser = visit('/tabs-browser-test?keyboard=1');
    $contact = '#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="contact"]';
    $account = '#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="account"]';

    foreach ([$browser, $browser->inDarkMode()] as $page) {
        $page->assertScript('document.activeElement === document.querySelector("#keyboard-tabs input[autofocus]")', true)
            ->keys('#nested-tabs [data-tab-key="account"]', 'ArrowRight');
        $page->script('document.querySelector(\'#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="account"]\').disabled = true');
        $page->assertAttribute($contact, 'aria-selected', 'true')
            ->assertScript("document.activeElement.matches('{$contact}')", true);
        $page->script('document.querySelector(\'#keyboard-tabs > [x-ref=tabsHeader] [data-tab-key="account"]\').disabled = false');
        $page->keys($account, 'Enter')
            ->assertAttribute($account, 'aria-selected', 'true')
            ->assertVisible('#nested-tabs')
            ->keys('#nested-tabs [data-tab-key="account"]', 'ArrowRight');
        $page->script('document.querySelector(\'#nested-tabs [data-tab-key="contact"]\').disabled = true');
        $page->assertScript('document.activeElement.closest(".fi-sc-tabs").id', 'nested-tabs')
            ->assertScript('document.activeElement.dataset.tabKey', 'account')
            ->assertAttribute($account, 'aria-selected', 'true')
            ->keys($contact, 'Enter')->assertAttribute($contact, 'aria-selected', 'true');
        $page->script('Alpine.$data(document.querySelector("[data-testid=tabs-form]")).showContact = false');
        $page->assertAttribute($account, 'aria-selected', 'true')
            ->assertScript("document.activeElement.matches('{$account}')", true);

        $page->script('Alpine.$data(document.querySelector("[data-testid=tabs-form]")).showContact = true');
        $page->keys($contact, 'Enter')->click('[data-testid=hide-contact]')
            ->assertAttribute($account, 'aria-selected', 'true')
            ->assertScript('document.activeElement.dataset.testid', 'hide-contact')
            ->click('[data-testid=hide-all]')
            ->assertScript('Array.from(document.querySelectorAll("#keyboard-tabs > [role=tabpanel]")).some(panel => panel.checkVisibility())', false)
            ->assertScript('document.querySelectorAll("#keyboard-tabs > [x-ref=tabsHeader] [role=tab][aria-selected=true]").length', 0)
            ->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});

it('retains lazy panel shells and selection through `Livewire` morphs', function (): void {
    $this->actingAs(User::factory()->create());
    $browser = visit('/tabs-browser-test?keyboard=1');
    $empty = '#dynamic-tabs [data-tab-key=""]';
    $zero = '#dynamic-tabs [data-tab-key="0"]';
    $second = '#dynamic-tabs [data-tab-key="second"]';

    foreach ([$browser, $browser->inDarkMode()] as $page) {
        // Test settled colors, not intermediate button colors during hover and `Livewire` transitions.
        $page->script('(() => { const style = document.createElement("style"); style.textContent = "*, *::before, *::after { transition: none !important; animation: none !important; }"; document.head.appendChild(style); })()');

        // Let initial autofocus finish before keyboard navigation, so it cannot steal the tab header focus.
        $page->assertScript('document.activeElement === document.querySelector("#keyboard-tabs input[autofocus]")', true)
            ->assertAttribute($second, 'aria-selected', 'true')
            ->assertScript('document.querySelectorAll("#dynamic-tabs > [role=tabpanel]").length', 4)
            ->assertScript('document.getElementById(document.querySelector(\'#dynamic-tabs [data-tab-key=""]\').getAttribute("aria-controls")).childElementCount', 0)
            ->keys($second, 'Home')->assertScript('document.activeElement.dataset.tabKey', '')
            ->keys($empty, ' ')->assertAttribute($empty, 'aria-selected', 'true')
            ->keys($empty, 'ArrowRight')->keys($zero, 'Enter')
            ->assertAttribute($zero, 'aria-selected', 'true')
            ->assertScript('document.activeElement.dataset.tabKey', '0')
            ->keys('#dynamic-tabs [data-tab-key="unavailable"]', 'Enter')
            ->assertAttribute($zero, 'aria-selected', 'true')
            ->keys($second, 'Enter')->assertAttribute($second, 'aria-selected', 'true');
        $secondId = $page->script('document.querySelector(\'#dynamic-tabs [data-tab-key="second"]\').id');
        $page->script('document.querySelector("#dynamic-tabs > [role=tabpanel].fi-active input").focus(); Alpine.$data(document.querySelector("#dynamic-tabs")).$wire.$set("showSecondTab", false)');
        $page->assertMissing($second)
            ->assertAttribute($zero, 'aria-selected', 'true')
            ->assertScript('document.activeElement.dataset.tabKey', '0');
        $page->script('Alpine.$data(document.querySelector("#dynamic-tabs")).$wire.$set("showSecondTab", true)');
        $page->assertVisible($second)->assertAttribute($second, 'id', $secondId)
            ->keys($second, 'Enter')->assertAttribute($second, 'aria-selected', 'true')
            ->click('[data-testid=remove-tab]')->assertMissing($second)
            ->assertAttribute($zero, 'aria-selected', 'true')
            ->assertScript('document.activeElement.dataset.testid', 'remove-tab')
            ->click('[data-testid=remove-zero]')->assertMissing($zero)
            ->assertAttribute($empty, 'aria-selected', 'true')
            ->assertScript('document.querySelectorAll("#filter-tabs [role=tab], #filter-tabs [role=tabpanel]").length', 0)
            ->keys('#filter-tabs [data-tab-key="all"]', 'Tab')
            ->assertScript('document.activeElement.dataset.tabKey', 'published')
            ->keys('#filter-tabs [data-tab-key="published"]', 'Enter')
            ->assertAttribute('#filter-tabs [data-tab-key="published"]', 'aria-pressed', 'true')
            ->hover('[data-testid=hide-contact]');
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
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
