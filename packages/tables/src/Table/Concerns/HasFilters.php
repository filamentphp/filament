<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait HasFilters
{
    /**
     * @var array<string, BaseFilter>
     */
    protected array $filters = [];

    protected ?Closure $filtersFormSchema = null;

    /**
     * @var int | array<string, int | null> | Closure | null
     */
    protected int | array | Closure | null $filtersFormColumns = null;

    protected string | Closure | null $filtersFormMaxHeight = null;

    protected Width | string | Closure | null $filtersFormWidth = null;

    protected FiltersLayout | Closure | null $filtersLayout = null;

    protected ?Closure $modifyFiltersTriggerActionUsing = null;

    protected bool | Closure | null $persistsFiltersInSession = false;

    protected bool | Closure $shouldDeselectAllRecordsWhenFiltered = true;

    protected bool | Closure $hasDeferredFilters = true;

    protected ?Closure $modifyFiltersApplyActionUsing = null;

    protected ?Closure $modifyFiltersResetActionUsing = null;

    protected ?Closure $modifyFiltersRemoveAllActionUsing = null;

    protected FiltersResetActionPosition | Closure | null $filtersResetActionPosition = null;

    public function deferFilters(bool | Closure $condition = true): static
    {
        $this->hasDeferredFilters = $condition;

        return $this;
    }

    public function hasDeferredFilters(): bool
    {
        return (bool) $this->evaluate($this->hasDeferredFilters);
    }

    public function filtersApplyAction(?Closure $callback): static
    {
        $this->modifyFiltersApplyActionUsing = $callback;

        return $this;
    }

    public function filtersResetAction(?Closure $callback): static
    {
        $this->modifyFiltersResetActionUsing = $callback;

        return $this;
    }

    public function filtersRemoveAllAction(?Closure $callback): static
    {
        $this->modifyFiltersRemoveAllActionUsing = $callback;

        return $this;
    }

    public function deselectAllRecordsWhenFiltered(bool | Closure $condition = true): static
    {
        $this->shouldDeselectAllRecordsWhenFiltered = $condition;

        return $this;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public function filters(array $filters, FiltersLayout | string | Closure | null $layout = null): static
    {
        $this->filters = [];
        $this->pushFilters($filters);

        if ($layout) {
            $this->filtersLayout(is_string($layout) ? (FiltersLayout::tryFrom($layout) ?? $layout) : $layout);
        }

        return $this;
    }

    /**
     * @param  array<BaseFilter>  $filters
     */
    public function pushFilters(array $filters): static
    {
        foreach ($filters as $filter) {
            $filter->table($this);

            $this->filters[$filter->getName()] = $filter;
        }

        return $this;
    }

    /**
     * @param  int | array<string, int | null> | Closure | null  $columns
     */
    public function filtersFormColumns(int | array | Closure | null $columns): static
    {
        $this->filtersFormColumns = $columns;

        return $this;
    }

    public function filtersFormMaxHeight(string | Closure | null $height): static
    {
        $this->filtersFormMaxHeight = $height;

        return $this;
    }

    public function filtersFormWidth(Width | string | Closure | null $width): static
    {
        $this->filtersFormWidth = $width;

        return $this;
    }

    public function filtersResetActionPosition(FiltersResetActionPosition | Closure | null $position): static
    {
        $this->filtersResetActionPosition = $position;

        return $this;
    }

    public function getFiltersResetActionPosition(): FiltersResetActionPosition
    {
        return $this->evaluate($this->filtersResetActionPosition) ?? FiltersResetActionPosition::Header;
    }

    public function filtersLayout(FiltersLayout | Closure | null $filtersLayout): static
    {
        $this->filtersLayout = $filtersLayout;

        return $this;
    }

    public function filtersTriggerAction(?Closure $callback): static
    {
        $this->modifyFiltersTriggerActionUsing = $callback;

        return $this;
    }

    public function persistFiltersInSession(bool | Closure $condition = true): static
    {
        $this->persistsFiltersInSession = $condition;

        return $this;
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getFilters(bool $withHidden = false): array
    {
        if ($withHidden) {
            return $this->filters;
        }

        return array_filter(
            $this->filters,
            fn (BaseFilter $filter): bool => $filter->isVisible(),
        );
    }

    public function getFilter(string $name, bool $withHidden = false): ?BaseFilter
    {
        return $this->getFilters($withHidden)[$name] ?? null;
    }

    public function getFiltersForm(): Schema
    {
        return $this->getLivewire()->getTableFiltersForm();
    }

    /**
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     *
     * @internal
     */
    public function withAppliedFiltersFormState(Closure $callback): mixed
    {
        if (! $this->hasDeferredFilters()) {
            return $callback();
        }

        $this->setFiltersFormStatePath('tableFilters');

        try {
            return $callback();
        } finally {
            $this->setFiltersFormStatePath('tableDeferredFilters');
        }
    }

    protected function setFiltersFormStatePath(string $statePath): void
    {
        $filtersForm = $this->getFiltersForm()->statePath($statePath);

        $filtersForm->flushCachedAbsoluteStatePaths();
        $filtersForm->clearCachedChildSchemas();
    }

    public function filtersFormSchema(?Closure $schema): static
    {
        $this->filtersFormSchema = $schema;

        return $this;
    }

    /**
     * @return array<Component | Action | ActionGroup | string | Htmlable>
     */
    public function getFiltersFormSchema(): array
    {
        $filters = [];

        foreach ($this->getFilters() as $filterName => $filter) {
            $filters[$filterName] = Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filterName)
                ->key($filterName)
                ->columnSpan($filter->getColumnSpan())
                ->columnStart($filter->getColumnStart())
                ->columns($filter->getColumns());
        }

        return $this->evaluate($this->filtersFormSchema, ['filters' => $filters]) ?? array_values($filters);
    }

    public function getFiltersTriggerAction(): Action
    {
        $action = Action::make('openFilters')
            ->label(__('filament-tables::table.actions.filter.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::ACTIONS_FILTER) ?? Heroicon::Funnel)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->modalSubmitAction(false)
            ->extraModalFooterActions([
                $this->getFiltersApplyAction()
                    ->close(),
                $this->getFiltersResetAction(),
            ])
            ->modalCancelActionLabel(__('filament::components/modal.actions.close.label'))
            ->table($this)
            ->authorize(true);

        if ($this->modifyFiltersTriggerActionUsing) {
            $action = $this->evaluate($this->modifyFiltersTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        $action->extraAttributes(['class' => 'fi-force-enabled'], merge: true);

        return $action;
    }

    public function getFiltersApplyAction(): Action
    {
        $action = Action::make('applyFilters')
            ->label(__('filament-tables::table.filters.actions.apply.label'))
            ->action('applyTableFilters')
            ->table($this)
            ->visible($this->hasDeferredFilters())
            ->authorize(true)
            ->button();

        if ($this->modifyFiltersApplyActionUsing) {
            $action = $this->evaluate($this->modifyFiltersApplyActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        $action->extraAttributes(['data-table-filter-action' => 'apply'], merge: true);

        return $action;
    }

    public function getFiltersResetAction(): Action
    {
        $action = Action::make('resetFilters')
            ->label(__('filament-tables::table.filters.actions.reset.label'))
            ->color('danger')
            ->action('resetTableFiltersForm')
            ->table($this)
            ->authorize(true);

        if ($this->modifyFiltersResetActionUsing) {
            $action = $this->evaluate($this->modifyFiltersResetActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        $action->extraAttributes(['data-table-filter-action' => 'reset'], merge: true);

        return $action;
    }

    public function getFiltersRemoveAllAction(): Action
    {
        $action = Action::make('removeAllFilters')
            ->label(__('filament-tables::table.filters.actions.remove_all.label'))
            ->tooltip(__('filament-tables::table.filters.actions.remove_all.tooltip'))
            ->action('removeTableFilters')
            ->livewireTarget('removeTableFilters,removeTableFilter')
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::FILTERS_REMOVE_ALL_BUTTON) ?? Heroicon::XMark)
            ->color('gray')
            ->defaultSize(Size::Small)
            ->table($this)
            ->authorize(true);

        if ($this->modifyFiltersRemoveAllActionUsing) {
            $action = $this->evaluate($this->modifyFiltersRemoveAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getFiltersFormColumns(): int | array
    {
        return $this->evaluate($this->filtersFormColumns) ?? match ($this->getFiltersLayout()) {
            FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible, FiltersLayout::BelowContent => [
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ],
            default => 1,
        };
    }

    public function getFiltersFormMaxHeight(): ?string
    {
        return $this->evaluate($this->filtersFormMaxHeight);
    }

    public function getFiltersFormWidth(): Width | string | null
    {
        return $this->evaluate($this->filtersFormWidth) ?? match ($this->getFiltersFormColumns()) {
            2 => Width::TwoExtraLarge,
            3 => Width::FourExtraLarge,
            4 => Width::SixExtraLarge,
            default => null,
        };
    }

    public function getFiltersLayout(): FiltersLayout
    {
        return $this->evaluate($this->filtersLayout) ?? FiltersLayout::Dropdown;
    }

    public function isFilterable(): bool
    {
        return (bool) count($this->getFilters());
    }

    public function persistsFiltersInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsFiltersInSession);
    }

    public function shouldDeselectAllRecordsWhenFiltered(): bool
    {
        return (bool) $this->evaluate($this->shouldDeselectAllRecordsWhenFiltered);
    }

    public function getActiveFiltersCount(): int
    {
        return array_reduce(
            $this->getFilters(),
            fn (int $carry, BaseFilter $filter): int => $carry + $filter->getActiveCount(),
            0,
        );
    }

    public function isFiltered(): bool
    {
        return $this->getActiveFiltersCount() > 0;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderFiltersInLayout(FiltersLayout ...$layouts): bool
    {
        return $this->isFilterable() && in_array($this->getFiltersLayout(), $layouts);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderCollapsibleFilters(): bool
    {
        return $this->hasRenderFiltersInLayout(FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderFiltersTrigger(): bool
    {
        return $this->isFilterable() && in_array($this->getFiltersLayout(), [
            FiltersLayout::Dropdown,
            FiltersLayout::Modal,
            FiltersLayout::BeforeContent,
            FiltersLayout::BeforeContentCollapsible,
            FiltersLayout::AfterContent,
            FiltersLayout::AfterContentCollapsible,
        ]);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderFiltersFormWidth(): Width | string | null
    {
        $width = $this->getFiltersFormWidth();

        return is_string($width) ? (Width::tryFrom($width) ?? $width) : $width;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersBeforeContentHtml(): string
    {
        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible)) { ?>
            <div
                wire:ignore.self
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-before-content-ctn',
                    'lg:fi-open' => ! $this->hasRenderCollapsibleFilters(),
                    (($filtersFormWidth = $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : $filtersFormWidth,
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-before-content']),
                ); ?>
            </div>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersAboveContentHtml(): string
    {
        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible)) { ?>
            <div
                <?php if ($this->hasRenderCollapsibleFilters()) { ?>
                    x-bind:class="{ 'fi-open': areFiltersOpen }"
                <?php } ?>
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-above-content-ctn',
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['x-cloak' => true, 'x-show' => BladeCompiler::sanitizeComponentAttribute($this->hasRenderCollapsibleFilters() ? 'areFiltersOpen' : null)]),
                ); ?>

                <?php if ($this->hasRenderCollapsibleFilters()) { ?>
                    <span
                        x-on:click="areFiltersOpen = ! areFiltersOpen"
                        class="fi-ta-filters-trigger-action-ctn"
                    >
                        <?= e($this->getFiltersTriggerAction()->badge((string) $this->getActiveFiltersCount())); ?>
                    </span>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersTriggerHtml(): string
    {
        $filtersTriggerAction = $this->hasRenderFiltersTrigger() ? $this->getFiltersTriggerAction() : null;

        ob_start(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::Dropdown, FiltersLayout::Modal)) { ?>
            <?php if (($this->getFiltersLayout() === FiltersLayout::Modal) || $filtersTriggerAction->isModalSlideOver()) {
                $filtersTriggerActionModalAlignment = $filtersTriggerAction->getModalAlignment();
                $filtersTriggerActionIsModalAutofocused = $filtersTriggerAction->isModalAutofocused();
                $filtersTriggerActionIsModalClickThrough = $filtersTriggerAction->isModalClickThrough();
                $filtersTriggerActionHasModalCloseButton = $filtersTriggerAction->hasModalCloseButton();
                $filtersTriggerActionIsModalClosedByClickingAway = $filtersTriggerAction->isModalClosedByClickingAway();
                $filtersTriggerActionIsModalClosedByEscaping = $filtersTriggerAction->isModalClosedByEscaping();
                $filtersTriggerActionModalDescription = $filtersTriggerAction->getModalDescription();
                $filtersTriggerActionModalEyebrow = $filtersTriggerAction->getModalEyebrow();
                $filtersTriggerActionExtraModalWindowAttributeBag = $filtersTriggerAction->getExtraModalWindowAttributeBag();
                $filtersTriggerActionExtraModalOverlayAttributeBag = $filtersTriggerAction->getExtraModalOverlayAttributeBag();
                $filtersTriggerActionVisibleModalFooterActions = $filtersTriggerAction->getVisibleModalFooterActions();
                $filtersTriggerActionModalFooterActionsAlignment = $filtersTriggerAction->getModalFooterActionsAlignment();
                $filtersTriggerActionModalHeading = $filtersTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading');
                $filtersTriggerActionModalIcon = $filtersTriggerAction->getModalIcon();
                $filtersTriggerActionModalIconColor = $filtersTriggerAction->getModalIconColor();
                $filtersTriggerActionIsModalSlideOver = $filtersTriggerAction->isModalSlideOver();
                $filtersTriggerActionModalSlideOverPosition = $filtersTriggerAction->getModalSlideOverPosition();
                $filtersTriggerActionIsModalFooterSticky = $filtersTriggerAction->isModalFooterSticky();
                $filtersTriggerActionIsModalHeaderSticky = $filtersTriggerAction->isModalHeaderSticky();

                $modalArguments = [
                    'alignment' => $filtersTriggerActionModalAlignment,
                    'autofocus' => $filtersTriggerActionIsModalAutofocused,
                    'clickThrough' => $filtersTriggerActionIsModalClickThrough,
                    'closeButton' => $filtersTriggerActionHasModalCloseButton,
                    'closeByClickingAway' => $filtersTriggerActionIsModalClosedByClickingAway,
                    'closeByEscaping' => $filtersTriggerActionIsModalClosedByEscaping,
                    'description' => $filtersTriggerActionModalDescription,
                    'eyebrow' => $filtersTriggerActionModalEyebrow,
                    'extraModalWindowAttributeBag' => $filtersTriggerActionExtraModalWindowAttributeBag,
                    'extraModalOverlayAttributeBag' => $filtersTriggerActionExtraModalOverlayAttributeBag,
                    'footerActions' => $filtersTriggerActionVisibleModalFooterActions,
                    'footerActionsAlignment' => $filtersTriggerActionModalFooterActionsAlignment,
                    'heading' => $filtersTriggerActionModalHeading,
                    'icon' => $filtersTriggerActionModalIcon,
                    'iconColor' => $filtersTriggerActionModalIconColor,
                    'slideOver' => $filtersTriggerActionIsModalSlideOver,
                    'slideOverPosition' => $filtersTriggerActionModalSlideOverPosition,
                    'stickyFooter' => $filtersTriggerActionIsModalFooterSticky,
                    'stickyHeader' => $filtersTriggerActionIsModalHeaderSticky,
                    'width' => $this->getRenderFiltersFormWidth(),
                    'attributes' => new ComponentAttributeBag(['wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.filters'), 'class' => 'fi-ta-filters-modal']),
                ];
                $this->setRenderKey($modalArguments['attributes']->get('wire:key'));
                ob_start(); ?>
                    <?php $modalTrigger = new ComponentSlot(trim(e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())))); ?>

                    <?= e($filtersTriggerAction->getModalContent()); ?>

                    <?= e($this->getFiltersForm()); ?>

                    <?= e($filtersTriggerAction->getModalContentFooter()); ?>

                <?= $this->generateModalHtml(...$modalArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $modalTrigger, livewireId: $this->getLivewire()->getId()); ?>
            <?php } else { ?>
                <?php $dropdownArguments = [
                    'menu' => false,
                    'maxHeight' => $this->getFiltersFormMaxHeight(),
                    'placement' => 'bottom-end',
                    'shift' => true,
                    'flip' => false,
                    'width' => $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall,
                    'attributes' => new ComponentAttributeBag(['wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.filters'), 'class' => 'fi-ta-filters-dropdown']),
                ];
                $this->setRenderKey($dropdownArguments['attributes']->get('wire:key'));
                ob_start(); ?>
                    <?php $dropdownTrigger = new ComponentSlot(trim(e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())))); ?>

                    <?= $this->generateFiltersHtml(
                        applyAction: $this->getFiltersApplyAction(),
                        form: $this->getFiltersForm(),
                        headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                        resetAction: $this->getFiltersResetAction(),
                        resetActionPosition: $this->getFiltersResetActionPosition(),
                        attributes: new ComponentAttributeBag(['x-data' => 'filamentTableFilters', 'x-on:click' => 'handleActionClick($event)', 'tabindex' => '-1']),
                    ); ?>
                <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>
            <?php } ?>
        <?php } elseif ($this->hasRenderFiltersInLayout(FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible)) { ?>
            <span
                x-ref="filtersTriggerActionContainer"
                x-on:click="toggleFiltersDropdown"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-trigger-action-ctn',
                    'lg:fi-hidden' => ! $this->hasRenderCollapsibleFilters(),
                ]); ?>"
            >
                <?= e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())); ?>
            </span>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersBelowContentHtml(): string
    {
        if (! $this->hasRenderFiltersInLayout(FiltersLayout::BelowContent)) {
            return '';
        }

        return $this->generateFiltersHtml(
            applyAction: $this->getFiltersApplyAction(),
            form: $this->getFiltersForm(),
            headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
            resetAction: $this->getFiltersResetAction(),
            resetActionPosition: $this->getFiltersResetActionPosition(),
            attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-below-content']),
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersAfterContentHtml(): string
    {
        ob_start(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible)) { ?>
            <div
                wire:ignore.self
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-after-content-ctn',
                    'lg:fi-open' => ! $this->hasRenderCollapsibleFilters(),
                    (($filtersFormWidth = $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : $filtersFormWidth,
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-after-content']),
                ); ?>
            </div>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersHtml(
        Action $applyAction,
        Schema $form,
        string $headingTag = 'h3',
        ?Action $resetAction = null,
        ?FiltersResetActionPosition $resetActionPosition = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.filters')) {
            return view('filament-tables::components.filters', [ // @phpstan-ignore argument.type (Published overrides only exist in consuming applications.)
                'applyAction' => $applyAction,
                'form' => $form,
                'headingTag' => $headingTag,
                'resetAction' => $resetAction,
                'resetActionPosition' => $resetActionPosition,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $resetActionPosition ??= FiltersResetActionPosition::Header;
        $isApplyActionVisible = $applyAction->isVisible();
        $isResetActionVisible = $resetAction?->isVisible() ?? false;

        $html = '<div ' . $attributes->class(['fi-ta-filters'])->toHtml() . '><div class="fi-ta-filters-header">';
        $html .= '<' . e($headingTag) . ' class="fi-ta-filters-heading">' . e(__('filament-tables::table.filters.heading')) . '</' . e($headingTag) . '>';

        if (($resetActionPosition === FiltersResetActionPosition::Header) && $isResetActionVisible) {
            $html .= '<div>' . e($resetAction->defaultView($resetAction::LINK_VIEW)) . '</div>';
        }

        $html .= '</div>' . e($form);

        if ($isApplyActionVisible || (($resetActionPosition === FiltersResetActionPosition::Footer) && $isResetActionVisible)) {
            $html .= '<div class="fi-ta-filters-actions-ctn">';
            $html .= $this->generateMorphMarkerHtml();

            if ($isApplyActionVisible) {
                $html .= e($applyAction);
            }

            $html .= $this->generateMorphMarkerHtml(isEnd: true);

            if (($resetActionPosition === FiltersResetActionPosition::Footer) && $isResetActionVisible) {
                $html .= e($resetAction);
            }

            $html .= '</div>';
        }

        return $html . '</div>';
    }
}
