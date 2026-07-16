<?php

namespace Filament\Schemas\Components;

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

            foreach ($this->getChildSchema()->getComponents() as $index => $tab) {
                if ($tab->getId() !== $queryStringTab) {
                    continue;
                }

                return $index + 1;
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

        $getTabVisibilityJs = static function (Tab $tab, ?int $index = null, ?string $mode = null) use ($isScrollable): ?string {
            $hiddenJs = $tab->getHiddenJs();
            $visibleJs = $tab->getVisibleJs();

            $baseJs = match ([filled($hiddenJs), filled($visibleJs)]) {
                [true, true] => "(! ({$hiddenJs})) && ({$visibleJs})",
                [true, false] => "! ({$hiddenJs})",
                [false, true] => $visibleJs,
                default => null,
            };

            if ($isScrollable || $index === null || $mode === null) {
                return $baseJs;
            }

            $tabKey = $tab->getKey(isAbsolute: false);

            $dropdownJs = match ($mode) {
                'inline' => "(!withinDropdownMounted || withinDropdownIndex === null || {$index} < withinDropdownIndex)",
                'trigger' => "(withinDropdownMounted && withinDropdownIndex !== null && {$index} >= withinDropdownIndex && '{$tabKey}' === tab)",
                default => null,
            };

            return $baseJs ? "{$baseJs} && {$dropdownJs}" : $dropdownJs;
        };

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
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class([
                'fi-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        if (! $isScrollable) {
            $navAttributes = $navAttributes->merge([
                'x-bind:class' => '{ \'fi-invisible\': ! withinDropdownMounted }',
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

        $visibleTabKeysJson = collect($tabs)
            ->filter(static fn (Tab $tab): bool => $tab->isVisible())
            ->map(static fn (Tab $tab) => $tab->getKey(isAbsolute: false))
            ->values()
            ->toJson();

        $alpineComponentSrc = FilamentAsset::getAlpineComponentSrc('tabs', 'filament/schemas');

        ob_start(); ?>

        <div
            x-data="tabsSchemaComponent({
                activeTab: <?= Js::from($activeTab) ?>,
                isScrollable: <?= Js::from($isScrollable) ?>,
                isTabPersisted: <?= Js::from($this->isTabPersisted()) ?>,
                isTabPersistedInQueryString: <?= Js::from($this->isTabPersistedInQueryString()) ?>,
                livewireId: <?= Js::from($this->getLivewire()->getId()) ?>,
                schemaKey: <?= Js::from($this->getRootContainer()->getKey()) ?>,
                tab: <?php if ($this->isTabPersisted() && filled($id)) { ?>$persist(null).as(<?= Js::from($id) ?>)<?php } else { ?><?= Js::from(null) ?><?php } ?>,
                tabQueryStringKey: <?= Js::from($this->getTabQueryStringKey()) ?>,
            })"
            x-load
            x-load-src="<?= e($alpineComponentSrc) ?>"
            wire:ignore.self
            <?= $outerAttributes->toHtml() ?>
        >
            <input
                type="hidden"
                value="<?= e($visibleTabKeysJson) ?>"
                x-ref="tabsData"
            />

            <nav <?= $navAttributes->toHtml() ?>>
                <?php foreach ($this->getStartRenderHooks() as $startRenderHook) { ?>
                    <?= FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>

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
                    $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'inline');

                    $tabItemAttributes = (new FilamentComponentAttributeBag)
                        ->merge($tabExtraAttributeBag->getAttributes(), escape: false)
                        ->merge([
                            'role' => 'tab',
                            'aria-selected' => 'false',
                            'data-tab-key' => $tabKey,
                            'x-bind:aria-selected' => "tab === '{$tabKey}'",
                            'x-on:click' => "tab = '{$tabKey}'",
                        ], escape: false)
                        ->class([
                            'fi-tabs-item',
                        ]);

                    if ($tabVisibilityJs !== null) {
                        $tabItemAttributes = $tabItemAttributes->merge([
                            'x-cloak' => true,
                            'x-show' => $tabVisibilityJs,
                        ], escape: false);
                    }
                    ?>
                    <button
                        type="button"
                        x-bind:class="{
                            'fi-active': tab === '<?= e($tabKey) ?>',
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

                <?php if (! $isScrollable) { ?>
                    <div x-data="filamentDropdown" class="fi-dropdown">
                        <div
                            x-on:keyup.enter="toggle($event)"
                            x-on:keyup.space="toggle($event)"
                            x-on:mousedown="if ($event.button === 0) toggle($event)"
                            class="fi-dropdown-trigger"
                        >
                            <?php foreach ($tabs as $index => $tab) {
                                $isTabBadgeDeferred = $tab->isBadgeDeferred();
                                $tabBadge = $isTabBadgeDeferred ? null : $tab->getBadge();
                                $tabBadgeColor = $isTabBadgeDeferred ? null : $tab->getBadgeColor($tabBadge);
                                $tabBadgeTooltip = $isTabBadgeDeferred ? null : $tab->getBadgeTooltip($tabBadge);
                                $tabExtraAttributeBag = $tab->getExtraAttributeBag();
                                $tabKey = $tab->getKey(isAbsolute: false);
                                $tabLabel = $tab->getLabel();
                                $tabVisibilityJs = $getTabVisibilityJs($tab, $index, 'trigger');

                                $triggerTabAttributes = (new FilamentComponentAttributeBag)
                                    ->merge($tabExtraAttributeBag->getAttributes(), escape: false)
                                    ->merge([
                                        'role' => 'tab',
                                        'aria-selected' => 'false',
                                        'x-bind:aria-selected' => "tab === '{$tabKey}'",
                                    ], escape: false)
                                    ->class(['fi-tabs-item']);

                                if ($tabVisibilityJs !== null) {
                                    $triggerTabAttributes = $triggerTabAttributes->merge([
                                        'x-cloak' => true,
                                        'x-show' => $tabVisibilityJs,
                                    ], escape: false);
                                }

                                $chevronIconHtml = generate_icon_html(Heroicon::ChevronDown, alias: SchemaIconAlias::COMPONENTS_TABS_DROPDOWN_TRIGGER_BUTTON)?->toHtml();
                                ?>
                                <button
                                    type="button"
                                    x-bind:class="{
                                        'fi-active': tab === '<?= e($tabKey) ?>',
                                    }"
                                    <?= $triggerTabAttributes->toHtml() ?>
                                >
                                    <?= $chevronIconHtml ?>

                                    <span class="fi-tabs-item-label">
                                        <?= e($tabLabel) ?>
                                    </span>

                                    <?php if (filled($tabBadge)) { ?>
                                        <?= $this->generateTabBadgeHtml($tabBadge, $tabBadgeColor, tooltip: $tabBadgeTooltip) ?>
                                    <?php } elseif ($isTabBadgeDeferred) { ?>
                                        <?= $this->generateDeferredBadgePlaceholderHtml(Js::from($index)) ?>
                                    <?php } ?>
                                </button>
                            <?php } ?>

                            <button
                                type="button"
                                role="tab"
                                aria-selected="false"
                                class="fi-tabs-item"
                                x-show="isDropdownButtonVisible"
                            >
                                <span class="fi-tabs-item-label">
                                    <?= generate_icon_html(Heroicon::EllipsisHorizontal, alias: SchemaIconAlias::COMPONENTS_TABS_MORE_TABS_BUTTON)?->toHtml() ?>
                                </span>
                            </button>
                        </div>

                        <div
                            x-cloak
                            x-float.placement.<?= e(__('filament-panels::layout.direction') === 'ltr' ? 'bottom-start' : 'bottom-end') ?>.flip.offset="{ offset: 8 }"
                            x-ref="panel"
                            x-transition:enter-start="fi-opacity-0"
                            x-transition:leave-end="fi-opacity-0"
                            class="fi-dropdown-panel"
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

                                    $dropdownItemAttributes = (new FilamentComponentAttributeBag)
                                        ->merge([
                                            'type' => 'button',
                                            'wire:loading.attr' => 'disabled',
                                            'x-bind:class' => "{ 'fi-selected': tab === '" . e($tabKey) . "' }",
                                            'x-on:click' => "tab = '{$tabKey}'; close(\$event);",
                                            'x-show' => "{$index} >= withinDropdownIndex",
                                        ], escape: false)
                                        ->class(['fi-dropdown-list-item'])
                                        ->color(ItemComponent::class, 'gray');
                                    ?>
                                    <button <?= $dropdownItemAttributes->toHtml() ?>>
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
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php foreach ($this->getEndRenderHooks() as $endRenderHook) { ?>
                    <?= FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
            </nav>

            <?php foreach ($tabs as $tab) {
                $tabVisibilityJs = $getTabVisibilityJs($tab);

                if ($tabVisibilityJs) { ?>
                    <div x-cloak x-show="<?= $tabVisibilityJs ?>">
                        <?= $tab->toHtml() ?>
                    </div>
                <?php } else { ?>
                    <?= $tab->toHtml() ?>
                <?php }
                } ?>
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

        if ($hasDeferredBadges) {
            $outerAttributes = $outerAttributes->merge([
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

        $navAttributes = (new FilamentComponentAttributeBag)
            ->merge([
                'aria-label' => $label,
                'role' => 'tablist',
            ])
            ->class([
                'fi-tabs',
                'fi-contained' => $isContained,
                'fi-vertical' => $isVertical,
            ]);

        ob_start(); ?>

        <div <?= $outerAttributes->toHtml() ?>>
            <nav <?= $navAttributes->toHtml() ?>>
                <?php foreach ($this->getStartRenderHooks() as $startRenderHook) { ?>
                    <?= FilamentView::renderHook($startRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>

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

                    $wireClickValue = filled($tabKey)
                        ? "\$set('{$livewireProperty}', '" . addslashes($tabKey) . "')"
                        : "\$set('{$livewireProperty}', null)";

                    $tabItemAttributes = (new FilamentComponentAttributeBag)
                        ->merge($tabExtraAttributeBag->getAttributes(), escape: false)
                        ->merge([
                            'aria-selected' => $isActive ? 'true' : 'false',
                            'role' => 'tab',
                            'type' => 'button',
                            'wire:click' => $wireClickValue,
                            'wire:loading.attr' => 'disabled',
                        ], escape: false)
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

                <?php foreach ($this->getEndRenderHooks() as $endRenderHook) { ?>
                    <?= FilamentView::renderHook($endRenderHook, scopes: $renderHookScopes)->toHtml() ?>
                <?php } ?>
            </nav>

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
        string | Htmlable | null $icon = null,
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
