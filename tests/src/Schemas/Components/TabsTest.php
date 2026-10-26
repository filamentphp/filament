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

it('returns fluent `$this` from `tabs()`', function (): void {
    $tabs = Tabs::make('Test');

    $result = $tabs->tabs([]);

    expect($result)->toBe($tabs);
});

describe('active tab', function (): void {
    it('can set `activeTab()`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->getActiveTab())->toBe(1);

        $tabs->activeTab(3);

        expect($tabs->getActiveTab())->toBe(3);
    });

    it('can set `activeTab()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->activeTab(static fn (): int => 2);

        expect($tabs->getActiveTab())->toBe(2);
    });
});

describe('query-string persistence', function (): void {
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

    it('can set `persistTabInQueryString()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->persistTabInQueryString(static fn (): string => 'dynamicKey');

        expect($tabs->getTabQueryStringKey())->toBe('dynamicKey');
        expect($tabs->isTabPersistedInQueryString())->toBeTrue();
    });

    it('can clear `persistTabInQueryString()` with `null`', function (): void {
        $tabs = Tabs::make('Test')
            ->persistTabInQueryString()
            ->persistTabInQueryString(null);

        expect($tabs->isTabPersistedInQueryString())->toBeFalse();
        expect($tabs->getTabQueryStringKey())->toBeNull();
    });
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

describe('Livewire property', function (): void {
    it('can set `livewireProperty()`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->getLivewireProperty())->toBeNull();

        $tabs->livewireProperty('activeTab');

        expect($tabs->getLivewireProperty())->toBe('activeTab');
    });

    it('can set `livewireProperty()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->livewireProperty(static fn (): string => 'dynamicProp');

        expect($tabs->getLivewireProperty())->toBe('dynamicProp');
    });
});

describe('scrolling', function (): void {
    it('defaults to scrollable', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->isScrollable())->toBeTrue();
    });

    it('can disable `scrollable()`', function (): void {
        $tabs = Tabs::make('Test')
            ->scrollable(false);

        expect($tabs->isScrollable())->toBeFalse();
    });

    it('can set `scrollable()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->scrollable(static fn (): bool => false);

        expect($tabs->isScrollable())->toBeFalse();
    });
});

describe('orientation', function (): void {
    it('can set `vertical()`', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->isVertical())->toBeFalse();

        $tabs->vertical();

        expect($tabs->isVertical())->toBeTrue();
    });

    it('can set `vertical()` with a `Closure`', function (): void {
        $tabs = Tabs::make('Test')
            ->vertical(static fn (): bool => true);

        expect($tabs->isVertical())->toBeTrue();
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

describe('render hooks', function (): void {
    it('defaults to empty arrays for render hooks', function (): void {
        $tabs = Tabs::make('Test');

        expect($tabs->getStartRenderHooks())->toBe([]);
        expect($tabs->getEndRenderHooks())->toBe([]);
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
});

describe('render hook scopes', function (): void {
    it('returns empty array for `getRenderHookScopes()` when Livewire does not implement `HasRenderHookScopes`', function (): void {
        $tabs = Tabs::make('Test')
            ->container(Schema::make(Livewire::make()));

        expect($tabs->getRenderHookScopes())->toBe([]);
    });
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

it('can detect lazy badges through both getter names', function (string $method): void {
    $lazyTab = Tab::make('Lazy')->badge(static fn (): int => 42)->lazyLoadedBadge();
    $tabs = Tabs::make('Settings')->tabs([
        Tab::make('Normal')->badge(10),
        $lazyTab,
    ]);

    Schema::make(Livewire::make())->components([$tabs])->getComponents();

    expect($tabs->{$method}())->toBeTrue();

    $lazyTab->lazyLoadedBadge(false);

    expect($tabs->{$method}())->toBeFalse();
})->with(['hasLazyLoadedBadges', 'hasDeferredBadges']);

it('can return lazy tab badges through both endpoint names', function (string $method): void {
    $component = livewire(TabsWithDeferredBadges::class)
        ->call('callSchemaComponentMethod', 'form.test-tabs', $method)
        ->assertReturned(function (array $badges): bool {
            expect($badges)->toHaveCount(1);
            expect($badges)->toHaveKey('1');
            expect($badges['1']['badge'])->toBe('42');
            expect($badges)->not->toHaveKey('0');

            return true;
        });

    $badges = $component->instance()->getSchemaComponent('form.test-tabs')->getLazyLoadedTabBadges();

    expect($badges)->toHaveCount(1)
        ->and($badges['1']['badge'])->toBe('42');
})->with(['getDeferredTabBadges', 'getLazyLoadedTabBadges']);

it('preserves `getDeferredTabBadges()` overrides when calling `getLazyLoadedTabBadges()`', function (): void {
    app()->bind(Tabs::class, static fn (): Tabs => new class extends Tabs
    {
        public function getDeferredTabBadges(): array
        {
            $badges = parent::getDeferredTabBadges();
            $badges['1']['badge'] = '84';

            return $badges;
        }
    });

    livewire(TabsWithDeferredBadges::class)
        ->call('callSchemaComponentMethod', 'form.test-tabs', 'getLazyLoadedTabBadges')
        ->assertReturned(function (array $badges): bool {
            expect($badges)->toHaveCount(1)
                ->and($badges['1']['badge'])->toBe('84');

            return true;
        });
});

it('can call an `Action` nested inside a tab that uses `livewireProperty()`', function (): void {
    livewire(TabsWithLivewirePropertyAction::class)
        ->callAction(TestAction::make('set_value')->schemaComponent('test-tabs.first'))
        ->assertSet('actionCalled', true);
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

it('persists independent tabs across reload and browser history', function (): void {
    $this->actingAs(User::factory()->create());

    $browser = visit('/tabs-browser-test?delivery_tab=contact')
        ->assertVisible('#profile-account')
        ->assertVisible('#delivery-contact')
        ->click('#profile-tabs [data-tab-key="contact"]')
        ->assertVisible('#profile-contact');

    $browser->assertQueryStringHas('tab', 'contact')
        ->assertQueryStringHas('delivery_tab', 'contact');

    $browser->click('a[href$="/wizard-browser-test"]:visible')
        ->assertVisible('#profile-wizard')
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

it('prioritizes visible native validation errors before revealing invalid fields in another `Tab`', function (string $activeTab, string $visibleField, string $hiddenTab, string $hiddenField): void {
    $this->actingAs(User::factory()->create());

    $page = visit("/tabs-browser-test?native-validation=1&tab={$activeTab}");
    $page->assertVisible("#profile-{$activeTab}");

    foreach (range(1, 2) as $attempt) {
        $page->click('[data-testid="save"]');
        $page->assertVisible("#profile-{$activeTab}")
            ->assertMissing("#profile-{$hiddenTab}:visible")
            ->assertPresent("#profile-{$activeTab} input:focus")
            ->assertMissing('[data-testid="saved"]');
    }

    if ($activeTab === 'account') {
        $page->assertNoAccessibilityIssues();
    }

    $page->fill("#profile-{$activeTab} input", $visibleField)
        ->click('[data-testid="save"]')
        ->assertVisible("#profile-{$hiddenTab}")
        ->assertPresent("#profile-{$hiddenTab} input:focus")
        ->assertMissing('[data-testid="saved"]');

    $page->fill("#profile-{$hiddenTab} input", $hiddenField)
        ->click('[data-testid="save"]')
        ->assertVisible('[data-testid="saved"]');

    if ($activeTab === 'account') {
        $page->inDarkMode()
            ->click('[data-testid="save"]')
            ->assertPresent('#profile-account input:focus')
            ->assertNoAccessibilityIssues();
    }
})->with([
    'visible invalid field before hidden invalid field' => ['account', 'alex', 'contact', '+44 7700 900123'],
    'hidden invalid field before visible invalid field' => ['contact', '+44 7700 900123', 'account', 'alex'],
]);

it('can render `Tabs` in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/tabs-browser-test')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/tabs-browser-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('loads deferred badges in both tab renderers', function (bool $livewireTabs): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/tabs-browser-test?deferred=1&livewire=' . (int) $livewireTabs);

    $page->assertSeeIn('[data-testid="deferred-tab"]', 'Available')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()
        ->assertSeeIn('[data-testid="deferred-tab"]', 'Available')
        ->assertNoAccessibilityIssues();
})->with(['Alpine' => false, 'Livewire' => true]);

it('keeps non-scrollable `Tabs` in one keyboard sequence and accessible tablist', function (): void {
    $this->actingAs(User::factory()->create());

    $trigger = '#overflow-tabs [data-tabs-overflow-trigger]:visible';
    $popup = '#overflow-tabs .fi-sc-tabs-overflow-panel';
    $overview = '#overflow-tabs [data-tab-key="overview"]';
    $contact = '#overflow-tabs [data-tab-key="contact"]';
    $billing = '#overflow-tabs [data-tab-key="billing"]';
    $notifications = '#overflow-tabs [data-tab-key="notifications"]';

    $page = visit('/tabs-browser-test?overflow=1');

    $page
        ->resize(375, 812)
        ->assertVisible($trigger)
        ->assertAttribute($trigger, 'aria-hidden', 'true')
        ->assertAttribute($trigger, 'tabindex', '-1')
        ->assertAttribute($overview, 'aria-selected', 'true')
        ->keys($overview, 'ArrowRight')
        ->assertVisible($contact)
        ->assertVisible($popup)
        ->assertPresent($contact . ':focus')
        ->assertAttribute($contact, 'role', 'tab')
        ->assertScript('document.activeElement.closest("[aria-hidden=true]") === null', true)
        ->assertScript('document.activeElement.getAttribute("aria-controls") === document.querySelector("#overflow-tabs > [role=tabpanel][aria-labelledby=\"" + document.activeElement.id + "\"]").id', true)
        ->assertAttribute($overview, 'aria-selected', 'true')
        ->keys($contact, 'End')
        ->assertPresent($notifications . ':focus')
        ->keys($notifications, 'ArrowRight')
        ->assertPresent($overview . ':focus');

    $page->keys($overview, 'ArrowRight')
        ->assertPresent($contact . ':focus')
        ->keys($contact, 'Enter')
        ->assertAttribute($contact, 'aria-selected', 'true')
        ->assertVisible($contact)
        ->assertPresent($contact . ':focus');
    $page->keys($contact, 'End')
        ->resize(375, 180)
        ->assertPresent($notifications . ':focus')
        ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true);
    $page->keys($notifications, 'Home')
        ->assertPresent($overview . ':focus')
        ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true);

    $page->resize(375, 812)
        ->click('#profile-tabs > [role=tabpanel]:visible input')
        ->assertPresent('#profile-tabs input:focus')
        ->assertMissing($popup)
        ->keys($overview, 'Home')
        ->keys($overview, 'ArrowRight')
        ->assertPresent($contact . ':focus')
        ->keys($contact, 'Escape')
        ->assertMissing($popup)
        ->assertPresent('#overflow-tabs > [role=tabpanel]:focus')
        ->assertScript('document.activeElement.id === document.querySelector("#overflow-tabs [role=tab][aria-selected=true]").getAttribute("aria-controls")', true)
        ->keys(':focus', 'Shift+Tab')
        ->assertPresent($contact . ':focus')
        ->assertVisible($contact)
        ->keys($contact, 'Tab')
        ->assertMissing($popup)
        ->assertPresent('#overflow-tabs > [role=tabpanel]:focus')
        ->assertScript('document.activeElement.id === document.querySelector("#overflow-tabs [role=tab][aria-selected=true]").getAttribute("aria-controls")', true);

    $page->keys($overview, 'End')
        ->assertPresent($notifications . ':focus')
        ->resize(1920, 900)
        ->assertMissing($trigger)
        ->assertMissing($popup)
        ->assertVisible($contact)
        ->assertVisible($notifications)
        ->assertPresent($notifications . ':focus')
        ->keys($notifications, 'Home')
        ->assertPresent($overview . ':focus');
    $page
        ->keys($overview, 'End')
        ->resize(375, 180)
        ->assertVisible($notifications)
        ->assertPresent($notifications . ':focus')
        ->assertScript('document.activeElement.getBoundingClientRect().top >= 0 && document.activeElement.getBoundingClientRect().bottom <= innerHeight', true)
        ->keys($notifications, 'Home')
        ->keys($overview, 'ArrowRight')
        ->resize(375, 812)
        ->assertVisible($contact)
        ->assertPresent($contact . ':focus')
        ->assertAttribute($contact, 'aria-selected', 'true');
    $page->assertNoAccessibilityIssues()
        ->keys($contact, 'Home')
        ->keys($overview, 'Enter')
        ->assertAttribute($overview, 'aria-selected', 'true')
        ->keys($overview, 'Escape');

    $page = $page->inDarkMode();
    $page->resize(375, 812)
        ->assertVisible($trigger)
        ->keys($overview, 'ArrowRight')
        ->assertVisible($popup)
        ->assertPresent($contact . ':focus')
        ->assertNoAccessibilityIssues();
});

it('dismisses tab overflow before an enclosing dropdown', function (): void {
    $this->actingAs(User::factory()->create());
    $page = visit('/tabs-browser-test?overflow=1&dropdown=1');
    $dropdownTrigger = '[data-testid=enclosing-dropdown-trigger]';
    $popup = '#overflow-tabs .fi-sc-tabs-overflow-panel';
    $overview = '#overflow-tabs [data-tab-key=overview]';
    $notifications = '#overflow-tabs [data-tab-key=notifications]';

    $page->resize(375, 812)
        ->click($dropdownTrigger)
        ->assertAttribute($dropdownTrigger, 'aria-expanded', 'true')
        ->assertVisible($overview)
        ->keys($overview, 'End')
        ->assertVisible($notifications)
        ->assertVisible($popup)
        ->assertPresent($notifications . ':focus')
        ->keys($notifications, 'Escape')
        ->assertMissing($popup)
        ->assertPresent('#overflow-tabs > [role=tabpanel]:focus')
        ->assertAttribute($dropdownTrigger, 'aria-expanded', 'true')
        ->assertVisible($overview)
        ->assertAttribute($overview, 'aria-selected', 'true');
    $page->assertNoAccessibilityIssues()
        ->keys('#overflow-tabs > [role=tabpanel]:visible', 'Escape')
        ->assertAttribute($dropdownTrigger, 'aria-expanded', 'false')
        ->assertMissing($overview . ':visible')
        ->assertPresent($dropdownTrigger . ':focus');

    $page->inDarkMode()->resize(375, 812)
        ->click($dropdownTrigger)
        ->assertVisible($overview)
        ->assertNoAccessibilityIssues();
});

it('uses manual activation, roving focus and scoped `tabpanel` relationships', function (bool $isRightToLeft): void {
    $this->actingAs(User::factory()->create());
    $page = visit('/tabs-browser-test?keyboard=1&rtl=' . (int) $isRightToLeft);
    $account = '#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [data-tab-key="account"]';
    $contact = '#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [data-tab-key="contact"]';
    $readonly = '#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [data-tab-key="readonly"]';
    $forwardKey = $isRightToLeft ? 'ArrowLeft' : 'ArrowRight';
    $backwardKey = $isRightToLeft ? 'ArrowRight' : 'ArrowLeft';

    $page->assertAttribute($account, 'aria-selected', 'true')
        ->keys($account, $forwardKey)
        ->assertPresent($contact . ':focus')
        ->assertAttribute($account, 'aria-selected', 'true')
        ->assertAttribute($contact, 'tabindex', '0')
        ->assertAttribute($account, 'tabindex', '-1')
        ->keys($contact, 'End')
        ->assertPresent($readonly . ':focus')
        ->keys($readonly, $forwardKey)
        ->assertPresent($account . ':focus')
        ->keys($account, $backwardKey)
        ->assertPresent($readonly . ':focus')
        ->keys($readonly, 'Enter')->assertAttribute($readonly, 'aria-selected', 'true')
        ->assertDisabled('#keyboard-tabs > [role=tabpanel]:visible input')
        ->keys($account, 'End')->keys($readonly, 'Home')
        ->keys($account, $forwardKey)->keys($contact, 'Enter')
        ->assertAttribute($contact, 'aria-selected', 'true')
        ->assertPresent($contact . ':focus')
        ->keys($contact, 'Tab')
        ->assertPresent('#keyboard-tabs > [role=tabpanel]:focus')
        ->assertScript('Array.from(document.querySelectorAll("[role=tab]")).every(tab => { const panel = document.getElementById(tab.getAttribute("aria-controls")); return panel && panel.getAttribute("aria-labelledby") === tab.id })', true)
        ->assertScript('new Set(Array.from(document.querySelectorAll("[id]"), element => element.id)).size === document.querySelectorAll("[id]").length', true)
        ->keys($account, 'Enter')
        ->keys('#nested-tabs [data-tab-key="account"]', $forwardKey)
        ->assertPresent('#nested-tabs :focus')
        ->keys('#nested-tabs [data-tab-key="contact"]', 'Enter')
        ->assertAttribute($account, 'aria-selected', 'true')
        ->refresh()
        ->assertAttribute('#nested-tabs [data-tab-key="contact"]', 'aria-selected', 'true')
        ->assertAttribute($account, 'aria-selected', 'true');

    $page->keys($account, $forwardKey)->assertPresent($contact . ':focus')
        ->keys($contact, $backwardKey)->assertPresent($account . ':focus')
        ->assertAttribute('#vertical-tabs [role=tablist]', 'aria-orientation', 'vertical')
        ->keys('#vertical-tabs [data-tab-key="account"]', 'ArrowDown')
        ->assertPresent('#vertical-tabs [data-tab-key="contact"]:focus')
        ->keys('#vertical-tabs [data-tab-key="contact"]', 'Home')
        ->keys('#vertical-tabs [data-tab-key="account"]', 'ArrowUp')
        ->assertPresent('#vertical-tabs [data-tab-key="contact"]:focus')
        ->click('#profile-tabs [data-tab-key="contact"]')
        ->click('[data-testid=save]')->assertVisible('#profile-account')
        ->assertNoSmoke()->assertNoAccessibilityIssues();

    $page->inDarkMode()
        ->assertVisible('#keyboard-tabs')
        ->assertNoAccessibilityIssues();
})->with([false, true]);

it('reconciles client-hidden tabs without stealing outside focus', function (): void {
    $this->actingAs(User::factory()->create());
    $page = visit('/tabs-browser-test?keyboard=1');
    $contact = '#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [data-tab-key="contact"]';
    $account = '#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [data-tab-key="account"]';

    $page->keys($contact, 'Enter')->click('[data-testid=hide-contact]')
        ->assertAttribute($account, 'aria-selected', 'true')
        ->assertPresent('[data-testid=hide-contact]:focus')
        ->click('[data-testid=hide-all]')
        ->assertNotPresent('#keyboard-tabs > [role=tabpanel]:visible')
        ->assertMissing('#keyboard-tabs [role=tablist][aria-label="Keyboard tabs"] [role=tab][aria-selected=true]')
        ->assertNoSmoke();
});

it('retains lazy panel shells and selection through `Livewire` morphs', function (): void {
    $this->actingAs(User::factory()->create());
    $page = visit('/tabs-browser-test?keyboard=1');
    $empty = '#dynamic-tabs [data-tab-key=""]';
    $zero = '#dynamic-tabs [data-tab-key="0"]';
    $second = '#dynamic-tabs [data-tab-key="second"]';

    // Let initial autofocus finish before keyboard navigation, so it cannot steal the tab header focus.
    $page->assertPresent('#keyboard-tabs input[autofocus]:focus')
        ->assertAttribute($second, 'aria-selected', 'true')
        ->assertCount('#dynamic-tabs > [role=tabpanel]', 4)
        ->assertScript('document.getElementById(document.querySelector(\'#dynamic-tabs [data-tab-key=""]\').getAttribute("aria-controls")).childElementCount', 0)
        ->keys($second, 'Home')->assertPresent($empty . ':focus')
        ->keys($empty, ' ')->assertAttribute($empty, 'aria-selected', 'true')
        ->keys($empty, 'ArrowRight')->keys($zero, 'Enter')
        ->assertAttribute($zero, 'aria-selected', 'true')
        ->assertPresent($zero . ':focus')
        ->keys('#dynamic-tabs [data-tab-key="unavailable"]', 'Enter')
        ->assertAttribute($zero, 'aria-selected', 'true')
        ->keys($second, 'Enter')->assertAttribute($second, 'aria-selected', 'true');
    $page
        ->click('[data-testid=remove-tab]')->assertMissing($second)
        ->assertAttribute($zero, 'aria-selected', 'true')
        ->assertPresent('[data-testid=remove-tab]:focus')
        ->click('[data-testid=remove-zero]')->assertMissing($zero)
        ->assertAttribute($empty, 'aria-selected', 'true')
        ->assertNotPresent('#filter-tabs [role=tab], #filter-tabs [role=tabpanel]')
        ->keys('#filter-tabs [data-tab-key="all"]', 'Tab')
        ->assertPresent('#filter-tabs [data-tab-key="published"]:focus')
        ->keys('#filter-tabs [data-tab-key="published"]', 'Enter')
        ->assertAttribute('#filter-tabs [data-tab-key="published"]', 'aria-pressed', 'true');
    $page->assertNoSmoke();
});

it('binds named and general exposed method calls to the active `Tab`', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/schema-component-scope-browser-test?scenario=tabs');

    $page->click('#first-tab [data-testid=named-scope]')
        ->assertSeeIn('#first-tab output', 'Tab: First tab: Named')
        ->click('#first-tab [data-testid=general-scope]')
        ->assertSeeIn('#first-tab output', 'Tab: First tab: General')
        ->click('#method-tabs [data-tab-key=second]')
        ->click('#second-tab [data-testid=named-scope]')
        ->assertSeeIn('#second-tab output', 'Tab: Second tab: Named')
        ->click('#second-tab [data-testid=general-scope]')
        ->assertSeeIn('#second-tab output', 'Tab: Second tab: General')
        ->click('#method-tabs [data-tab-key=first]')
        ->click('#first-tab [data-testid=named-scope]')
        ->assertSeeIn('#first-tab output', 'Tab: First tab: Named')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()->assertNoAccessibilityIssues();
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
                            ->lazyLoadedBadge()
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
