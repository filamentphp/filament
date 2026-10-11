<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\CanPersistTab;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Schemas\Schema;
use Filament\Schemas\View\SchemaIconAlias;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Attributes\Renderless;

use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

class Tabs extends Component implements HasEmbeddedView
{
    use CanPersistTab;
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;
    use HasLabel;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.tabs';

    protected int | Closure $activeTab = 1;

    protected string | Closure | null $tabQueryStringKey = null;

    /**
     * @var array<string>
     */
    protected array $startRenderHooks = [];

    /**
     * @var array<string>
     */
    protected array $endRenderHooks = [];

    protected string | Closure | null $livewireProperty = null;

    protected bool | Closure $isScrollable = true;

    protected bool | Closure $isVertical = false;

    protected bool | Closure $hasTabPanels = true;

    final public function __construct(string | Htmlable | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(function (Tabs $component): ?string {
            $label = $component->getLabel();

            if (blank($label)) {
                return null;
            }

            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($label, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::tabs" : 'tabs');
        }, isInheritable: false);
    }

    /**
     * @param  array<Tab> | Closure  $tabs
     */
    public function tabs(array | Closure $tabs): static
    {
        $this->components($tabs);

        return $this;
    }

    /**
     * @return array<Component | Action | ActionGroup | string | Htmlable> | Schema
     */
    public function getDefaultChildComponents(): array | Schema
    {
        $components = parent::getDefaultChildComponents();

        if (blank($this->getLivewireProperty()) || (! is_array($components))) {
            return $components;
        }

        // Each tab's key must match the array key written into the Livewire
        // property, so `$set(...)` can activate it. This is done here rather than
        // during rendering so the key is settled before any absolute keys (such
        // as those of nested actions) are computed and cached.
        foreach ($components as $tabKey => $tab) {
            if (! $tab instanceof Tab) {
                continue;
            }

            $tab->key(strval($tabKey));
        }

        return $components;
    }

    public function activeTab(int | Closure $activeTab): static
    {
        $this->activeTab = $activeTab;

        return $this;
    }

    public function persistTabInQueryString(string | Closure | null $key = 'tab'): static
    {
        $this->tabQueryStringKey = $key;

        return $this;
    }

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());

            if (is_string($queryStringTab)) {
                $tabs = $this->getChildSchema()->getComponents();

                foreach ($tabs as $index => $tab) {
                    if ($tab->getKey(isAbsolute: false) !== $queryStringTab) {
                        continue;
                    }

                    return $index + 1;
                }

                foreach ($tabs as $index => $tab) {
                    if ($tab->getId() !== $queryStringTab) {
                        continue;
                    }

                    return $index + 1;
                }
            }
        }

        return $this->evaluate($this->activeTab);
    }

    public function getTabQueryStringKey(): ?string
    {
        return $this->evaluate($this->tabQueryStringKey);
    }

    public function isTabPersistedInQueryString(): bool
    {
        return filled($this->getTabQueryStringKey());
    }

    /**
     * @param  array<string>  $hooks
     */
    public function startRenderHooks(array $hooks): static
    {
        $this->startRenderHooks = $hooks;

        return $this;
    }

    /**
     * @param  array<string>  $hooks
     */
    public function endRenderHooks(array $hooks): static
    {
        $this->endRenderHooks = $hooks;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getStartRenderHooks(): array
    {
        return $this->startRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getEndRenderHooks(): array
    {
        return $this->endRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof HasRenderHookScopes)) {
            return [];
        }

        return $livewire->getRenderHookScopes();
    }

    public function livewireProperty(string | Closure | null $property): static
    {
        $this->livewireProperty = $property;

        return $this;
    }

    public function getLivewireProperty(): ?string
    {
        return $this->evaluate($this->livewireProperty);
    }

    public function scrollable(bool | Closure $condition = true): static
    {
        $this->isScrollable = $condition;

        return $this;
    }

    public function isScrollable(): bool
    {
        return (bool) $this->evaluate($this->isScrollable);
    }

    public function vertical(bool | Closure $condition = true): static
    {
        $this->isVertical = $condition;

        return $this;
    }

    public function isVertical(): bool
    {
        return (bool) $this->evaluate($this->isVertical);
    }

    public function tabPanels(bool | Closure $condition = true): static
    {
        $this->hasTabPanels = $condition;

        return $this;
    }

    public function hasTabPanels(): bool
    {
        return (bool) $this->evaluate($this->hasTabPanels);
    }

    public function toEmbeddedHtml(): string
    {
        if (filled($this->getLivewireProperty())) {
            return $this->toEmbeddedHtmlForLivewireProperty();
        }

        $activeTab = $this->getActiveTab();
        $hasDeferredBadges = $this->hasDeferredBadges();
        $id = $this->getId();
        $isContained = $this->isContained();
        $isScrollable = $this->isScrollable();
        $isVertical = $this->isVertical();
        $label = $this->getLabel();
        $renderHookScopes = $this->getRenderHookScopes();
        $tabs = array_values(array_filter(
            $this->getChildSchema()->getComponents(),
            static fn ($component): bool => $component instanceof Tab,
        ));
        $tabsKey = $this->getKey();

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'id' => $id,
                'wire:key' => $this->getLivewireKey() . '.container',
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge($this->getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-sc-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        $navAttributes = (new FilamentComponentAttributeBag)
            ->merge(['x-ref' => 'tabsHeader'])
            ->class([
                'fi-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        if (! $isScrollable) {
            $navAttributes = $navAttributes->merge([
                'x-bind:class' => '{ \'fi-invisible\': ! overflowReady }',
            ], escape: false);
        }

        $navAttributes = $navAttributes->merge(['x-cloak' => true], escape: false);

        $deferredBadgesXData = '';

        if ($hasDeferredBadges) {
            $deferredBadgesXData = '{
                deferredBadges: {},
                isLoadingDeferredBadges: true,

                async init() {
                    try {
                        const badges = await $wire.callSchemaComponentMethod(' . Js::from($tabsKey) . ', \'getDeferredTabBadges\')
                        this.deferredBadges = badges ?? {}
                    } finally {
                        this.isLoadingDeferredBadges = false
                    }
                },
            }';

            $navAttributes = $navAttributes->merge([
                'x-data' => $deferredBadgesXData,
            ], escape: false);
        }

        $alpineComponentSrc = FilamentAsset::getAlpineComponentSrc('tabs', 'filament/schemas');

        ob_start(); ?>

        <div
            x-data="tabsSchemaComponent({
                activeTab: <?= Js::from($activeTab) ?>,
                hasTabPanels: <?= Js::from($this->hasTabPanels()) ?>,
                isScrollable: <?= Js::from($isScrollable) ?>,
                isVertical: <?= Js::from($isVertical) ?>,
                isTabPersisted: <?= Js::from($this->isTabPersisted()) ?>,
                isTabPersistedInQueryString: <?= Js::from($this->isTabPersistedInQueryString()) ?>,
                livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                schemaKey: <?= Js::from($this->getRootContainer()->getKey()) ?>,
                tab: <?php if ($this->isTabPersisted() && filled($id)) { ?>$persist(null).as(<?= Js::from($id) ?>)<?php } else { ?><?= Js::from(null) ?><?php } ?>,
                tabQueryStringKey: <?= Js::from($this->getTabQueryStringKey()) ?>,
            })"
            x-load
            x-load-src="<?= e($alpineComponentSrc) ?>"
            <?php if ($this->hasTabPanels() && ! $isScrollable) { ?>
            x-on:resize.window="scheduleUpdate(true)"
            <?php } ?>
            wire:ignore.self
            <?= $outerAttributes->toHtml() ?>
        >
            <div <?= $navAttributes->toHtml() ?>>
                <?php foreach ($this->getStartRenderHooks() as $startRenderHook) { ?>
                    <?= FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
                <div
                    x-ref="tablist"
                    role="<?= $this->hasTabPanels() ? 'tablist' : 'group' ?>"
                    <?php if ($this->hasTabPanels()) { ?>x-bind:role="hasVisibleTabHeaders ? 'tablist' : 'group'"<?php } ?>
                    aria-label="<?= e($label ?? __('filament::components/tabs.label')) ?>"
                    <?php if ($this->hasTabPanels() && $isVertical) { ?>x-bind:aria-orientation="hasVisibleTabHeaders ? 'vertical' : null"<?php } ?>
                    x-on:keydown="handleKeydown($event)"
                    <?php if ($this->hasTabPanels() && ! $isScrollable) { ?>
                    x-on:dropdown-escape="if (isOverflowOpen) { $event.preventDefault(); $event.stopPropagation(); dismissOverflow() }"
                    x-on:wheel="scrollOverflow($event)"
                    x-on:touchstart="scrollOverflow($event)"
                    x-on:touchmove="scrollOverflow($event)"
                    <?php } ?>
                    class="fi-sc-tabs-tablist"
                >

                <?php foreach ($tabs as $index => $tab) {
                    $isTabBadgeDeferred = $tab->isBadgeDeferred();
                    $tabBadge = $isTabBadgeDeferred ? null : $tab->getBadge();
                    $tabBadgeColor = $isTabBadgeDeferred ? null : $tab->getBadgeColor($tabBadge);
                    $tabBadgeIcon = $isTabBadgeDeferred ? null : $tab->getBadgeIcon($tabBadge);
                    $tabBadgeIconPosition = $isTabBadgeDeferred ? null : $tab->getBadgeIconPosition($tabBadge);
                    $tabBadgeTooltip = $isTabBadgeDeferred ? null : $tab->getBadgeTooltip($tabBadge);
                    $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                    $tabIcon = $tab->getIcon();
                    $tabIconPosition = $tab->getIconPosition();
                    $tabKey = $tab->getKey(isAbsolute: false);
                    $tabLabel = $tab->getLabel();
                    $tabVisibilityJs = $tab->getTabVisibilityJs();

                    $tabItemAttributes = (new FilamentComponentAttributeBag)
                        ->merge([
                            'id' => e($tab->getTabId()),
                            'role' => $this->hasTabPanels() ? 'tab' : null,
                            'aria-controls' => $this->hasTabPanels() ? e($tab->getPanelId()) : null,
                            'tabindex' => $this->hasTabPanels() ? -1 : 0,
                            'x-bind:tabindex' => 'getTabIndex(' . Js::from($tabKey) . ')',
                            'data-tab-key' => $tabKey,
                            'x-bind:data-tab-available' => "Boolean({$tabVisibilityJs}).toString()",
                            $this->hasTabPanels() ? 'x-bind:aria-selected' : 'x-bind:aria-pressed' => 'isTabSelected(' . Js::from($tabKey) . ')',
                            'x-on:click' => 'selectTab(' . Js::from($tabKey) . ', $event)',
                            'x-show' => $this->hasTabPanels() ? $tabVisibilityJs : "({$tabVisibilityJs}) && ! overflowTabs.includes(" . Js::from($tabKey) . ')',
                        ], escape: false)
                        ->merge($tabExtraAttributeBag->getAttributes(), escape: false)
                        ->class([
                            'fi-tabs-item',
                        ]);

                    ?>
                    <button
                        type="button"
                        x-bind:class="{
                            'fi-active': isTabSelected(<?= Js::from($tabKey) ?>),
                            <?php if ($this->hasTabPanels()) { ?>
                            'fi-sc-tabs-header-overflow': overflowTabs.includes(<?= Js::from($tabKey) ?>),
                            'fi-sc-tabs-header-overflow-open': isOverflowOpen && overflowTabs.includes(<?= Js::from($tabKey) ?>),
                            <?php } ?>
                        }"
                        <?= $tabItemAttributes->toHtml() ?>
                    >
                        <?php if ($tabIcon && $tabIconPosition === IconPosition::Before) { ?>
                            <?= generate_icon_html($tabIcon)?->toHtml() ?>
                        <?php } ?>

                        <span class="fi-tabs-item-label">
                            <?= e($tabLabel) ?>
                        </span>

                        <?php if ($tabIcon && $tabIconPosition === IconPosition::After) { ?>
                            <?= generate_icon_html($tabIcon)?->toHtml() ?>
                        <?php } ?>

                        <?php if (filled($tabBadge)) { ?>
                            <?= $this->generateTabBadgeHtml($tabBadge, $tabBadgeColor, $tabBadgeIcon, $tabBadgeIconPosition, $tabBadgeTooltip) ?>
                        <?php } elseif ($isTabBadgeDeferred) { ?>
                            <?= $this->generateDeferredBadgePlaceholderHtml(Js::from($index)) ?>
                        <?php } ?>
                    </button>
                <?php } ?>

                </div>

                <?php if (! $isScrollable) { ?>
                    <div
                        <?php if (! $this->hasTabPanels()) { ?>x-data="filamentDropdown"<?php } ?>
                        <?php if ($this->hasTabPanels()) { ?>aria-hidden="true"<?php } ?>
                        class="fi-dropdown"
                    >
                        <div
                            x-on:mousedown="if ($event.button === 0) { <?= $this->hasTabPanels() ? '$event.preventDefault(); toggleOverflow()' : 'toggle($event)' ?> }"
                            class="fi-dropdown-trigger"
                        >
                            <?php foreach ($tabs as $index => $tab) {
                                $isTabBadgeDeferred = $tab->isBadgeDeferred();
                                $tabBadge = $isTabBadgeDeferred ? null : $tab->getBadge();
                                $tabBadgeColor = $isTabBadgeDeferred ? null : $tab->getBadgeColor($tabBadge);
                                $tabBadgeTooltip = $isTabBadgeDeferred ? null : $tab->getBadgeTooltip($tabBadge);
                                $tabKey = $tab->getKey(isAbsolute: false);
                                $tabLabel = $tab->getLabel();
                                $triggerTabAttributes = (new FilamentComponentAttributeBag)
                                    ->merge([
                                        'id' => null,
                                        'role' => null,
                                        'aria-selected' => null,
                                        'aria-pressed' => null,
                                        'aria-controls' => null,
                                        'tabindex' => $this->hasTabPanels() ? -1 : 0,
                                        'aria-hidden' => $this->hasTabPanels() ? 'true' : null,
                                        'x-bind:tabindex' => null,
                                        'x-bind:aria-selected' => null,
                                        'x-bind:aria-pressed' => null,
                                        'data-tabs-overflow-trigger' => true,
                                        'data-tab-trigger-key' => $tabKey,
                                        'aria-label' => e(__('filament-schemas::components.tabs.actions.more.label') . ': ' . ($tabLabel instanceof Htmlable ? html_entity_decode(strip_tags($tabLabel->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8') : $tabLabel)),
                                        'x-show' => 'overflowReady && overflowTabs.includes(' . Js::from($tabKey) . ') && isTabSelected(' . Js::from($tabKey) . ')',
                                    ], escape: false)
                                    ->merge($tab->getExtraAttributes(), escape: false)
                                    ->class(['fi-tabs-item', 'fi-active']);
                                ?>
                                <button type="button" <?= $triggerTabAttributes->toHtml() ?>>
                                    <?= generate_icon_html(Heroicon::ChevronDown, alias: SchemaIconAlias::COMPONENTS_TABS_DROPDOWN_TRIGGER_BUTTON)?->toHtml() ?>
                                    <span class="fi-tabs-item-label"><?= e($tabLabel) ?></span>
                                    <?php if (filled($tabBadge)) { ?>
                                        <?= $this->generateTabBadgeHtml($tabBadge, $tabBadgeColor, tooltip: $tabBadgeTooltip) ?>
                                    <?php } elseif ($isTabBadgeDeferred) { ?>
                                        <?= $this->generateDeferredBadgePlaceholderHtml(Js::from($index)) ?>
                                    <?php } ?>
                                </button>
                            <?php } ?>

                            <button
                                type="button"
                                data-tabs-overflow-trigger
                                data-tabs-overflow-ellipsis
                                <?php if ($this->hasTabPanels()) { ?>tabindex="-1" aria-hidden="true"<?php } ?>
                                aria-label="<?= e(__('filament-schemas::components.tabs.actions.more.label')) ?>"
                                class="fi-tabs-item"
                                x-show="! overflowReady || (overflowTabs.some(key => availableTabs.includes(key)) && ! overflowTabs.includes(tab))"
                            >
                                <span class="fi-tabs-item-label">
                                    <?= generate_icon_html(Heroicon::EllipsisHorizontal, alias: SchemaIconAlias::COMPONENTS_TABS_MORE_TABS_BUTTON)?->toHtml() ?>
                                </span>
                            </button>
                        </div>

                        <div
                            x-cloak
                            <?php if ($this->hasTabPanels()) { ?>
                            x-ref="overflowPanel"
                            x-show="isOverflowOpen"
                            aria-hidden="true"
                            x-on:scroll="scheduleUpdate()"
                            x-on:click.outside="if (! $el.closest('.fi-sc-tabs').querySelector('[x-ref=tablist]').contains($event.target) && ! getOverflowTrigger()?.contains($event.target)) dismissOverflow()"
                            class="fi-dropdown-panel fi-sc-tabs-overflow-panel"
                            <?php } else { ?>
                            x-float.placement.<?= e(__('filament-panels::layout.direction') === 'ltr' ? 'bottom-start' : 'bottom-end') ?>.flip.offset="{ offset: 8 }"
                            x-ref="panel"
                            x-transition:enter-start="fi-opacity-0"
                            x-transition:leave-end="fi-opacity-0"
                            x-on:keydown.home.prevent.stop="focusMenuItem('first')"
                            x-on:keydown.end.prevent.stop="focusMenuItem('last')"
                            class="fi-dropdown-panel"
                            <?php } ?>
                        >
                            <div class="fi-dropdown-list">
                                <?php foreach ($tabs as $index => $tab) {
                                    $isTabBadgeDeferred = $tab->isBadgeDeferred();
                                    $tabBadge = $isTabBadgeDeferred ? null : $tab->getBadge();
                                    $tabBadgeColor = $isTabBadgeDeferred ? null : $tab->getBadgeColor($tabBadge);
                                    $tabBadgeTooltip = $isTabBadgeDeferred ? null : $tab->getBadgeTooltip($tabBadge);
                                    $tabIcon = $tab->getIcon();
                                    $tabKey = $tab->getKey(isAbsolute: false);
                                    $tabLabel = $tab->getLabel();
                                    $tabVisibilityJs = $tab->getTabVisibilityJs();
                                    $tabExtraAttributeBag = $tab->getExtraAttributeBag();

                                    $dropdownItemAttributes = (new FilamentComponentAttributeBag)
                                        ->merge([
                                            'role' => $this->hasTabPanels() ? null : 'menuitem',
                                            'type' => $this->hasTabPanels() ? null : 'button',
                                            'data-tab-menu-key' => $tabKey,
                                            'aria-disabled' => ($tabExtraAttributeBag->get('disabled') || ($tabExtraAttributeBag->get('aria-disabled') === 'true')) ? 'true' : null,
                                            'x-bind:aria-disabled' => 'availableTabs.includes(' . Js::from($tabKey) . ") ? 'false' : 'true'",
                                            'x-on:click' => $this->hasTabPanels() ? null : "if (\$el.getAttribute('aria-disabled') !== 'true') selectTab(" . Js::from($tabKey) . ', $event, true)',
                                            'x-show' => "({$tabVisibilityJs}) && overflowTabs.includes(" . Js::from($tabKey) . ')',
                                        ], escape: false)
                                        ->class(['fi-dropdown-list-item', 'fi-sc-tabs-overflow-placeholder' => $this->hasTabPanels()])
                                        ->color(ItemComponent::class, 'gray');
                                    ?>
                                    <<?= $this->hasTabPanels() ? 'div' : 'button' ?> <?= $dropdownItemAttributes->toHtml() ?>>
                                        <?php if ($tabIcon) { ?>
                                            <?= generate_icon_html($tabIcon, attributes: (new FilamentComponentAttributeBag)->color(IconComponent::class, 'gray'))?->toHtml() ?>
                                        <?php } ?>

                                        <span class="fi-dropdown-list-item-label">
                                            <?= e($tabLabel) ?>
                                        </span>

                                        <?php if (filled($tabBadge)) { ?>
                                            <span
                                                <?php if ($tabBadgeTooltip) { ?>
                                                    x-tooltip="{
                                                        content: <?= Js::from($tabBadgeTooltip) ?>,
                                                        theme: $store.theme,
                                                        allowHTML: <?= Js::from($tabBadgeTooltip instanceof Htmlable) ?>,
                                                    }"
                                                <?php } ?>
                                                <?= (new FilamentComponentAttributeBag)->color(BadgeComponent::class, $tabBadgeColor ?? 'primary')->class(['fi-badge'])->toHtml() ?>
                                            >
                                                <?= e($tabBadge) ?>
                                            </span>
                                        <?php } elseif ($isTabBadgeDeferred) { ?>
                                            <span
                                                x-show="isLoadingDeferredBadges"
                                                x-cloak
                                                class="fi-dropdown-list-item-badge-placeholder"
                                            >
                                                <?= generate_loading_indicator_html(size: IconSize::Small)->toHtml() ?>
                                            </span>

                                            <template
                                                x-if="
                                                    ! isLoadingDeferredBadges &&
                                                        deferredBadges[<?= Js::from($index) ?>]?.badge != null
                                                "
                                            >
                                                <span
                                                    x-bind:class="'fi-badge ' + (deferredBadges[<?= Js::from($index) ?>]?.badgeColorClasses ?? '')"
                                                    x-bind:style="deferredBadges[<?= Js::from($index) ?>]?.badgeColorStyles ?? ''"
                                                    x-init="
                                                        let tooltip = deferredBadges[<?= Js::from($index) ?>]?.badgeTooltip
                                                        if (tooltip) {
                                                            window.tippy?.($el, {
                                                                content: tooltip,
                                                                theme: $store.theme,
                                                            })
                                                        }
                                                    "
                                                >
                                                    <span class="fi-badge-label-ctn">
                                                        <span
                                                            class="fi-badge-label"
                                                            x-text="deferredBadges[<?= Js::from($index) ?>]?.badge"
                                                        ></span>
                                                    </span>
                                                </span>
                                            </template>
                                        <?php } ?>
                                    </<?= $this->hasTabPanels() ? 'div' : 'button' ?>>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php foreach ($this->getEndRenderHooks() as $endRenderHook) { ?>
                    <?= FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
            </div>

            <?php foreach ($tabs as $tab) { ?>
                <?= $tab->toHtml() ?>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }

    protected function toEmbeddedHtmlForLivewireProperty(): string
    {
        $livewireProperty = $this->getLivewireProperty();
        $hasDeferredBadges = $this->hasDeferredBadges();
        $id = $this->getId();
        $isContained = $this->isContained();
        $isVertical = $this->isVertical();
        $label = $this->getLabel();
        $renderHookScopes = $this->getRenderHookScopes();
        $tabsKey = $this->getKey();

        // Tab keys are overridden with their array keys in
        // `getDefaultChildComponents()`.
        /** @var array<array-key, Tab> $tabs */
        $tabs = array_filter(
            $this->getChildSchema()->getComponents(withOriginalKeys: true),
            static fn ($component): bool => $component instanceof Tab,
        );

        $activeTab = strval($this->getLivewire()->{$livewireProperty});

        $outerAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'id' => $id,
                'wire:key' => $this->getLivewireKey() . '.container',
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        $navAttributes = (new FilamentComponentAttributeBag)
            ->merge(['x-ref' => 'tabsHeader'])
            ->class([
                'fi-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        if ($hasDeferredBadges) {
            $navAttributes = $navAttributes->merge([
                'x-data' => '{
                    deferredBadges: {},
                    isLoadingDeferredBadges: true,

                    async init() {
                        try {
                            const badges = await $wire.callSchemaComponentMethod(' . Js::from($tabsKey) . ', \'getDeferredTabBadges\')
                            this.deferredBadges = badges ?? {}
                        } finally {
                            this.isLoadingDeferredBadges = false
                        }
                    },
                }',
            ], escape: false);
        }

        ob_start(); ?>

        <div
            x-data="tabsSchemaComponent({
                activeTab: <?= Js::from($this->getActiveTab()) ?>,
                hasTabPanels: <?= Js::from($this->hasTabPanels()) ?>,
                isScrollable: true,
                isVertical: <?= Js::from($isVertical) ?>,
                livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                livewireProperty: <?= Js::from($livewireProperty) ?>,
                tab: <?= Js::from($activeTab) ?>,
            })"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('tabs', 'filament/schemas')) ?>"
            wire:ignore.self
            <?= $outerAttributes->toHtml() ?>
        >
            <input type="hidden" x-ref="tabsData" data-active-tab="<?= e($activeTab) ?>" />
            <div <?= $navAttributes->toHtml() ?>>
                <?php foreach ($this->getStartRenderHooks() as $startRenderHook) { ?>
                    <?= FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
                <div
                    x-ref="tablist"
                    role="<?= $this->hasTabPanels() ? 'tablist' : 'group' ?>"
                    <?php if ($this->hasTabPanels()) { ?>x-bind:role="hasVisibleTabHeaders ? 'tablist' : 'group'"<?php } ?>
                    aria-label="<?= e($label ?? __('filament::components/tabs.label')) ?>"
                    <?php if ($this->hasTabPanels() && $isVertical) { ?>x-bind:aria-orientation="hasVisibleTabHeaders ? 'vertical' : null"<?php } ?>
                    x-on:keydown="handleKeydown($event)"
                    class="fi-sc-tabs-tablist"
                >

                <?php
                    $livewire = $this->getLivewire();
        $canGenerateTabLabel = method_exists($livewire, 'generateTabLabel');
        ?>

                <?php foreach ($tabs as $tabKey => $tab) {
                    $tabKey = strval($tabKey);
                    $isTabBadgeDeferred = $tab->isBadgeDeferred();
                    $tabBadge = $isTabBadgeDeferred ? null : $tab->getBadge();
                    $tabBadgeColor = $isTabBadgeDeferred ? null : $tab->getBadgeColor($tabBadge);
                    $tabBadgeIcon = $isTabBadgeDeferred ? null : $tab->getBadgeIcon($tabBadge);
                    $tabBadgeIconPosition = $isTabBadgeDeferred ? null : $tab->getBadgeIconPosition($tabBadge);
                    $tabBadgeTooltip = $isTabBadgeDeferred ? null : $tab->getBadgeTooltip($tabBadge);
                    $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                    $tabIcon = $tab->getIcon();
                    $tabIconPosition = $tab->getIconPosition();
                    $tabLabel = $tab->getLabel() ?? ($canGenerateTabLabel ? $livewire->generateTabLabel($tabKey) : null);
                    $isActive = $activeTab === $tabKey;
                    $tabVisibilityJs = $tab->getTabVisibilityJs();

                    $wireClickValue = filled($tabKey)
                        ? "\$set('{$livewireProperty}', '" . addslashes($tabKey) . "')"
                        : "\$set('{$livewireProperty}', null)";

                    $tabItemAttributes = (new FilamentComponentAttributeBag)
                        ->merge([
                            'id' => e($tab->getTabId()),
                            'role' => $this->hasTabPanels() ? 'tab' : null,
                            'aria-controls' => $this->hasTabPanels() ? e($tab->getPanelId()) : null,
                            $this->hasTabPanels() ? 'aria-selected' : 'aria-pressed' => $isActive ? 'true' : 'false',
                            $this->hasTabPanels() ? 'x-bind:aria-selected' : 'x-bind:aria-pressed' => 'isTabSelected(' . Js::from($tabKey) . ')',
                            'tabindex' => $this->hasTabPanels() ? ($isActive ? 0 : -1) : 0,
                            'x-bind:tabindex' => 'getTabIndex(' . Js::from($tabKey) . ')',
                            'data-tab-key' => $tabKey,
                            'x-bind:data-tab-available' => "Boolean({$tabVisibilityJs}).toString()",
                            'x-show' => $tabVisibilityJs,
                            'x-on:click.capture' => 'if (! selectTab(' . Js::from($tabKey) . ', $event)) { $event.preventDefault(); $event.stopImmediatePropagation() }',
                            'x-bind:class' => "{ 'fi-active': isTabSelected(" . Js::from($tabKey) . ') }',
                            'type' => 'button',
                            'wire:click' => $wireClickValue,
                        ], escape: false)
                        ->merge($tabExtraAttributeBag->getAttributes(), escape: false)
                        ->class([
                            'fi-tabs-item',
                            'fi-active' => $isActive,
                        ]);
                    ?>
                    <button <?= $tabItemAttributes->toHtml() ?>>
                        <?php if ($tabIcon && $tabIconPosition === IconPosition::Before) { ?>
                            <?= generate_icon_html($tabIcon)?->toHtml() ?>
                        <?php } ?>

                        <span class="fi-tabs-item-label">
                            <?= e($tabLabel) ?>
                        </span>

                        <?php if ($tabIcon && $tabIconPosition === IconPosition::After) { ?>
                            <?= generate_icon_html($tabIcon)?->toHtml() ?>
                        <?php } ?>

                        <?php if (filled($tabBadge)) { ?>
                            <?= $this->generateTabBadgeHtml($tabBadge, $tabBadgeColor, $tabBadgeIcon, $tabBadgeIconPosition, $tabBadgeTooltip) ?>
                        <?php } elseif ($isTabBadgeDeferred) { ?>
                            <?= $this->generateDeferredBadgePlaceholderHtml(Js::from($tabKey)) ?>
                        <?php } ?>
                    </button>
                <?php } ?>

                </div>
                <?php foreach ($this->getEndRenderHooks() as $endRenderHook) { ?>
                    <?= FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
            </div>

            <?php foreach ($tabs as $tab) { ?>
                <?= $tab->toHtml() ?>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }

    /**
     * @param  string | array<string> | null  $color
     */
    protected function generateTabBadgeHtml(
        string | int | float $badge,
        string | array | null $color = null,
        string | BackedEnum | Htmlable | null $icon = null,
        IconPosition | string | null $iconPosition = null,
        string | Htmlable | null $tooltip = null,
    ): string {
        if (! $iconPosition instanceof IconPosition) {
            $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : IconPosition::Before;
        }

        $badgeAttributes = (new FilamentComponentAttributeBag)
            ->class(['fi-badge', 'fi-size-sm'])
            ->color(BadgeComponent::class, $color ?? 'primary');

        ob_start(); ?>
        <span
            <?php if ($tooltip) { ?>
                x-tooltip="{
                    content: <?= Js::from($tooltip) ?>,
                    theme: $store.theme,
                    allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?>,
                }"
            <?php } ?>
            <?= $badgeAttributes->toHtml() ?>
        >
            <?php if ($icon && $iconPosition === IconPosition::Before) { ?>
                <?= generate_icon_html($icon, size: IconSize::Small)?->toHtml() ?>
            <?php } ?>

            <span class="fi-badge-label-ctn">
                <span class="fi-badge-label">
                    <?= e($badge) ?>
                </span>
            </span>

            <?php if ($icon && $iconPosition === IconPosition::After) { ?>
                <?= generate_icon_html($icon, size: IconSize::Small)?->toHtml() ?>
            <?php } ?>
        </span>
        <?php return ob_get_clean();
    }

    protected function generateDeferredBadgePlaceholderHtml(Js $indexJs): string
    {
        ob_start(); ?>
        <span
            x-show="isLoadingDeferredBadges"
            x-cloak
            class="fi-tabs-item-badge-placeholder"
        >
            <?= generate_loading_indicator_html(size: IconSize::Small)->toHtml() ?>
        </span>

        <template
            x-if="
                ! isLoadingDeferredBadges &&
                    deferredBadges[<?= $indexJs ?>]?.badge != null
            "
        >
            <span
                x-bind:class="
                    'fi-badge fi-size-sm ' +
                        (deferredBadges[<?= $indexJs ?>]?.badgeColorClasses ?? '')
                "
                x-bind:style="deferredBadges[<?= $indexJs ?>]?.badgeColorStyles ?? ''"
                x-init="
                    let tooltip = deferredBadges[<?= $indexJs ?>]?.badgeTooltip
                    if (tooltip) {
                        window.tippy?.($el, {
                            content: tooltip,
                            theme: $store.theme,
                        })
                    }
                "
            >
                <template
                    x-if="
                        deferredBadges[<?= $indexJs ?>]?.badgeIconHtml &&
                            deferredBadges[<?= $indexJs ?>]?.badgeIconPosition !== 'after'
                    "
                >
                    <span
                        x-html="deferredBadges[<?= $indexJs ?>].badgeIconHtml"
                    ></span>
                </template>

                <span class="fi-badge-label-ctn">
                    <span
                        class="fi-badge-label"
                        x-text="deferredBadges[<?= $indexJs ?>]?.badge"
                    ></span>
                </span>

                <template
                    x-if="
                        deferredBadges[<?= $indexJs ?>]?.badgeIconHtml &&
                            deferredBadges[<?= $indexJs ?>]?.badgeIconPosition === 'after'
                    "
                >
                    <span
                        x-html="deferredBadges[<?= $indexJs ?>].badgeIconHtml"
                    ></span>
                </template>
            </span>
        </template>
        <?php return ob_get_clean();
    }

    /**
     * @return array<string, array{badge: ?string, badgeColorClasses: string, badgeColorStyles: string, badgeIconHtml: string | null, badgeIconPosition: string | null, badgeTooltip: string | null}>
     */
    #[ExposedLivewireMethod]
    #[Renderless]
    public function getDeferredTabBadges(): array
    {
        $badges = [];

        foreach ($this->getChildSchema()->getComponents(withOriginalKeys: true) as $tabKey => $tab) {
            if (! $tab instanceof Tab) {
                continue;
            }

            if (! $tab->isBadgeDeferred()) {
                continue;
            }

            $badge = $tab->getBadge();
            $badgeColor = $tab->getBadgeColor($badge);

            $badgeColorClasses = '';
            $badgeColorStyles = '';

            if (is_array($badgeColor)) {
                $badgeColorClasses = 'fi-color';
                $badgeColorStyles = implode('; ', FilamentColor::getComponentCustomStyles(BadgeComponent::class, $badgeColor));
            } elseif (is_string($badgeColor)) {
                $badgeColorClasses = implode(' ', FilamentColor::getComponentClasses(BadgeComponent::class, $badgeColor));
            }

            $badgeIcon = $tab->getBadgeIcon($badge);
            $badgeIconHtml = $badgeIcon
                ? generate_icon_html($badgeIcon, size: IconSize::Small)?->toHtml()
                : null;

            $badgeIconPosition = $tab->getBadgeIconPosition($badge);
            $badgeTooltip = $tab->getBadgeTooltip($badge);

            $badges[strval($tabKey)] = [
                'badge' => $badge,
                'badgeColorClasses' => $badgeColorClasses,
                'badgeColorStyles' => $badgeColorStyles,
                'badgeIconHtml' => $badgeIconHtml,
                'badgeIconPosition' => $badgeIconPosition instanceof IconPosition ? $badgeIconPosition->value : $badgeIconPosition,
                'badgeTooltip' => $badgeTooltip ? strval($badgeTooltip) : null,
            ];
        }

        return $badges;
    }

    public function hasDeferredBadges(): bool
    {
        foreach ($this->getChildSchema()->getComponents() as $tab) {
            if (! $tab instanceof Tab) {
                continue;
            }

            if ($tab->isBadgeDeferred()) {
                return true;
            }
        }

        return false;
    }
}
