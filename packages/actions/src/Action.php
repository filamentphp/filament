<?php

namespace Filament\Actions;

use BackedEnum;
use Closure;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Actions\Enums\ActionStatus;
use Filament\Schemas\Components\Contracts\HasExtraItemActions;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasBadgeTooltip;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Enums\IconSize;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\DropdownComponent\ItemComponent;
use Filament\Support\View\Components\DropdownComponent\ItemComponent\IconComponent;
use Filament\Support\View\Components\LinkComponent;
use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateButtonHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownItemHtml;
use Filament\Support\View\Concerns\CanGenerateIconButtonHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Component;
use Livewire\Drawer\Utils;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

class Action extends ViewComponent implements Arrayable
{
    use CanGenerateBadgeHtml;
    use CanGenerateButtonHtml;
    use CanGenerateDropdownItemHtml;
    use CanGenerateIconButtonHtml;
    use CanGenerateLinkHtml;
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToSchemaComponent;
    use Concerns\BelongsToTable;
    use Concerns\CanBeAuthorized;
    use Concerns\CanBeBooted;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeMounted;
    use Concerns\CanBeOutlined;
    use Concerns\CanBeRateLimited;
    use Concerns\CanBeSorted;
    use Concerns\CanCallParentAction;
    use Concerns\CanClose;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\CanDispatchEvent;
    use Concerns\CanFetchSelectedRecords;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanOpenUrl;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\CanSubmitForm;
    use Concerns\CanUseDatabaseTransactions;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasData;
    use Concerns\HasExtraModalOverlayAttributes;
    use Concerns\HasExtraModalWindowAttributes;
    use Concerns\HasGroupedIcon;
    use Concerns\HasInfolist;
    use Concerns\HasKeyBindings;
    use Concerns\HasLabel;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasMountableArguments;
    use Concerns\HasName;
    use Concerns\HasParentActions;
    use Concerns\HasSchema;
    use Concerns\HasSize;
    use Concerns\HasTableIcon;
    use Concerns\HasWizard;
    use Concerns\InteractsWithRecord;
    use Concerns\InteractsWithSelectedRecords;
    use HasBadge;
    use HasBadgeTooltip;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon;
    use HasIconPosition;
    use HasIconSize;
    use HasTooltip;

    protected bool | Closure $isBulk = false;

    public const BADGE_VIEW = 'filament::components.badge';

    public const BUTTON_VIEW = 'filament::components.button.index';

    public const GROUPED_VIEW = 'filament::components.dropdown.list.item';

    public const ICON_BUTTON_VIEW = 'filament::components.icon-button';

    public const LINK_VIEW = 'filament::components.link';

    protected string $evaluationIdentifier = 'action';

    protected string $viewIdentifier = 'action';

    protected ?string $livewireTarget = null;

    protected string | Closure | null $alpineClickHandler = null;

    protected bool | Closure $shouldMarkAsRead = false;

    protected bool | Closure $shouldMarkAsUnread = false;

    protected ?int $nestingIndex = null;

    protected ?ActionStatus $status = null;

    protected ?Action $parentAction = null;

    final public function __construct(?string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::BUTTON_VIEW);
    }

    public function markAsRead(bool | Closure $condition = true): static
    {
        $this->shouldMarkAsRead = $condition;

        return $this;
    }

    public function markAsUnread(bool | Closure $condition = true): static
    {
        $this->shouldMarkAsUnread = $condition;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $icon = $this->getIcon();

        if ($icon instanceof ScalableIcon) {
            $icon = $icon->getIconForSize($this->getIconSize() ?? IconSize::Medium);
        } elseif ($icon instanceof BackedEnum) {
            $icon = $icon->value;
        }

        return [
            'name' => $this->getName(),
            'alpineClickHandler' => $this->getCustomAlpineClickHandler(),
            'color' => $this->getColor(),
            'event' => $this->getEvent(),
            'eventData' => $this->getEventData(),
            'dispatchDirection' => $this->getDispatchDirection(),
            'dispatchToComponent' => $this->getDispatchToComponent(),
            'extraAttributes' => $this->getExtraAttributes(),
            'icon' => $icon,
            'iconPosition' => $this->getIconPosition(),
            'iconSize' => $this->getIconSize(),
            'isOutlined' => $this->isOutlined(),
            'isDisabled' => $this->isDisabled(),
            'label' => $this->getLabel(),
            'shouldClose' => $this->shouldClose(),
            'shouldMarkAsRead' => $this->shouldMarkAsRead(),
            'shouldMarkAsUnread' => $this->shouldMarkAsUnread(),
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'shouldPostToUrl' => $this->shouldPostToUrl(),
            'size' => $this->getSize(),
            'tooltip' => $this->getTooltip(),
            'url' => $this->getUrl(),
            'view' => $this->getView(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make($data['name']);

        $view = match ($data['view'] ?? null) {
            'filament-actions::button-action' => static::BUTTON_VIEW,
            'filament-actions::grouped-action' => static::GROUPED_VIEW,
            'filament-actions::icon-button-action' => static::ICON_BUTTON_VIEW,
            'filament-actions::link-action' => static::LINK_VIEW,
            default => $data['view'] ?? null,
        };

        if (filled($view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
        }

        if (filled($data['alpineClickHandler'] ?? null)) {
            $static->alpineClickHandler($data['alpineClickHandler']);
        }

        $static->close($data['shouldClose'] ?? false);
        $static->color($data['color'] ?? null);
        $static->disabled($data['isDisabled'] ?? false);

        match ($data['dispatchDirection'] ?? null) {
            'self' => $static->dispatchSelf($data['event'] ?? null, $data['eventData'] ?? []),
            'to' => $static->dispatchTo($data['dispatchToComponent'] ?? null, $data['event'] ?? null, $data['eventData'] ?? []),
            default => $static->dispatch($data['event'] ?? null, $data['eventData'] ?? [])
        };

        $static->extraAttributes($data['extraAttributes'] ?? []);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->markAsRead($data['shouldMarkAsRead'] ?? false);
        $static->markAsUnread($data['shouldMarkAsUnread'] ?? false);
        $static->outlined($data['isOutlined'] ?? false);
        $static->postToUrl($data['shouldPostToUrl'] ?? false);
        $static->url($data['url'] ?? null, $data['shouldOpenUrlInNewTab'] ?? false);
        $static->tooltip($data['tooltip'] ?? null);

        return $static;
    }

    public function isBadge(): bool
    {
        return $this->getView() === static::BADGE_VIEW;
    }

    public function badge(string | Closure | null $badge = null): static
    {
        if (func_num_args() === 0) {
            /** @phpstan-ignore-next-line */
            return $this->view(static::BADGE_VIEW);
        }

        $this->badge = $badge;

        return $this;
    }

    public function button(): static
    {
        return $this->view(static::BUTTON_VIEW);
    }

    public function isButton(): bool
    {
        return $this->getView() === static::BUTTON_VIEW;
    }

    public function grouped(): static
    {
        return $this->view(static::GROUPED_VIEW);
    }

    public function iconButton(): static
    {
        return $this->view(static::ICON_BUTTON_VIEW);
    }

    public function isIconButton(): bool
    {
        return $this->getView() === static::ICON_BUTTON_VIEW;
    }

    public function link(): static
    {
        return $this->view(static::LINK_VIEW);
    }

    public function isLink(): bool
    {
        return $this->getView() === static::LINK_VIEW;
    }

    public function alpineClickHandler(string | Closure | null $handler): static
    {
        // Security: This JavaScript expression is evaluated on the client.
        // Never pass user input — only developer-defined expressions.

        $this->alpineClickHandler = $handler;
        $this->livewireClickHandlerEnabled(blank($handler));

        return $this;
    }

    public function actionJs(string | Closure | null $action): static
    {
        // Security: This JavaScript expression is evaluated on the client.
        // Never pass user input — only developer-defined expressions.

        $this->alpineClickHandler($action);

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        if ($event = $this->getLivewireEventClickHandler()) {
            return $event;
        }

        if (filled($handler = $this->getParentActionCallLivewireClickHandler())) {
            $handler .= '(';
            $handler .= Js::from($this->getArguments());
            $handler .= ')';

            return $handler;
        }

        if ($this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->getJsClickHandler();
    }

    protected function getLivewireKey(): ?string
    {
        if (! ($this->getRecord(withDefault: false) && $this->getTable())) {
            return null;
        }

        $livewire = $this->getLivewire();

        if (! ($livewire instanceof Component)) {
            return null;
        }

        $key = md5(serialize($this->getContext()));

        return "{$livewire->getId()}.actions.{$this->getName()}.{$key}";
    }

    public function getLivewireEventClickHandler(): ?string
    {
        $event = $this->getEvent();

        if (blank($event)) {
            return null;
        }

        $arguments = '';

        if ($component = $this->getDispatchToComponent()) {
            $arguments .= Js::from($component)->toHtml();
            $arguments .= ', ';
        }

        $arguments .= Js::from($event)->toHtml();

        if ($this->getEventData()) {
            $arguments .= ', ';
            $arguments .= Js::from($this->getEventData())->toHtml();
        }

        return match ($this->getDispatchDirection()) {
            'self' => "\$dispatchSelf($arguments)",
            'to' => "\$dispatchTo($arguments)",
            default => "\$dispatch($arguments)"
        };
    }

    public function getAlpineClickHandler(): ?string
    {
        if (filled($handler = $this->getCustomAlpineClickHandler())) {
            return $handler;
        }

        if ($this->shouldClose()) {
            return (filled($this->getUrl()) && (! $this->shouldOpenUrlInNewTab())) ? 'close(true)' : 'close()';
        }

        if ($this->shouldMarkAsRead()) {
            return 'markAsRead()';
        }

        if ($this->shouldMarkAsUnread()) {
            return 'markAsUnread()';
        }

        if (! $this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->getJsClickHandler();
    }

    public function getCustomAlpineClickHandler(): ?string
    {
        return $this->evaluate($this->alpineClickHandler);
    }

    public function livewireTarget(?string $target): static
    {
        $this->livewireTarget = $target;

        return $this;
    }

    public function getLivewireTarget(): ?string
    {
        if (filled($this->livewireTarget)) {
            return $this->livewireTarget;
        }

        if (! $this->canAccessSelectedRecords()) {
            return $this->canSubmitForm() ? $this->getFormToSubmit() : null;
        }

        return $this->getJsClickHandler();
    }

    /**
     * @deprecated Use `extraAttributes()` instead.
     *
     * @param  array<mixed>  $attributes
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedAction';
    }

    /**
     * @deprecated Use `getJsClickHandler()` instead.
     */
    protected function getJavaScriptClickHandler(): ?string
    {
        return $this->getJsClickHandler();
    }

    protected function getJsClickHandler(): ?string
    {
        if ($this->shouldClose()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getInvokedArguments() ?? [])) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
        }

        $contextParameter = '';

        if (count($context = $this->getContext())) {
            $contextParameter .= ', ';
            $contextParameter .= Js::from($context);

            if ($argumentsParameter === '') {
                $argumentsParameter = ', {}';
            }
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter}{$contextParameter})";
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        $context = [];

        $table = $this->getTable();

        $record = $this->getRecord();

        if ($record && (
            (! $table)
            || (! $record instanceof Model)
            || blank($table->getModel())
            || is_a($record::class, $table->getModel(), true)
        ) && filled($recordKey = $this->resolveRecordKey($record))) {
            $context['recordKey'] = $recordKey;
        }

        if ($this->getParentAction()) {
            return $context;
        }

        if ($table) {
            $context['table'] = true;
        }

        if ($table && $this->isBulk()) {
            $context['bulk'] = true;
        }

        if (filled($schemaComponentKey = ($this->getSchemaContainer() ?? $this->getSchemaComponent())?->getKey())) {
            $context['schemaComponent'] = $schemaComponentKey;
        }

        return $context;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'arguments' => [$this->getArguments()],
            'data' => [$this->getData()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'mountedActions' => [$this->getLivewire()->getMountedActions()],
            'record' => [$this->getRecord()],
            'selectedRecords', 'records' => [$this->getIndividuallyAuthorizedSelectedRecords()],
            'selectedRecordsQuery', 'recordsQuery' => [$this->getSelectedRecordsQuery()],
            'schema' => [$this->getSchemaContainer()],
            'schemaComponent', 'component' => [$this->getSchemaComponent()],
            'schemaOperation', 'context', 'operation' => [$this->getSchemaContainer()?->getOperation() ?? $this->getSchemaComponent()?->getContainer()->getOperation()],
            'schemaGet', 'get' => [$this->getSchemaComponent()->makeGetUtility()->skipComponentsChildContainersWhileSearching(false)],
            'schemaSet', 'set' => [$this->getSchemaComponent()->makeSetUtility()->skipComponentsChildContainersWhileSearching(false)],
            'schemaComponentState', 'state' => [$this->getSchemaComponentState()],
            'schemaState' => [$this->getSchemaState()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = is_a($parameterType, Model::class, allow_string: true) ? $this->getRecord() : null;

        return match ($parameterType) {
            Builder::class => [$this->getSelectedRecordsQuery()],
            EloquentCollection::class, Collection::class => [$this->getIndividuallyAuthorizedSelectedRecords()],
            Model::class, ($record instanceof Model) ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function getSchemaComponentState(): mixed
    {
        $schemaContainer = $this->getSchemaContainer();

        while ($schemaContainer) {
            $parentComponent = $schemaContainer->getParentComponent();

            if (! $parentComponent) {
                break;
            }

            if ($parentComponent->hasStatePath()) {
                return $parentComponent->getState();
            }

            $schemaContainer = $parentComponent->getContainer();
        }

        return $this->getSchemaComponent()?->getState();
    }

    public function getSchemaState(): mixed
    {
        $schemaComponent = $this->getSchemaComponent();
        $arguments = $this->getArguments();

        if (
            $schemaComponent instanceof HasExtraItemActions &&
            filled($itemKey = $arguments['item'] ?? null)
        ) {
            return $schemaComponent->getItemState($itemKey);
        }

        $schemaContainer = $this->getSchemaContainer();

        while ($schemaContainer) {
            if (filled($schemaContainer->getStatePath(isAbsolute: false))) {
                return $schemaContainer->getStateSnapshot();
            }

            $parentComponent = $schemaContainer->getParentComponent();

            if (! $parentComponent) {
                return $schemaContainer->getStateSnapshot();
            }

            $schemaContainer = $parentComponent->getContainer();
        }

        return null;
    }

    public function shouldClearRecordAfter(): bool
    {
        if (! ($this->record instanceof Model)) {
            return false;
        }

        return ! $this->record->exists;
    }

    public function clearRecordAfter(): void
    {
        if (! $this->shouldClearRecordAfter()) {
            return;
        }

        $this->record(null);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(array $parameters = []): mixed
    {
        try {
            return $this->evaluate($this->getActionFunction(), $parameters);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }

            $this->clearVisibilityCache();
        }
    }

    public function cancel(bool $shouldRollBackDatabaseTransaction = false): void
    {
        throw (new Cancel)->rollBackDatabaseTransaction($shouldRollBackDatabaseTransaction);
    }

    public function halt(bool $shouldRollBackDatabaseTransaction = false): void
    {
        throw (new Halt)->rollBackDatabaseTransaction($shouldRollBackDatabaseTransaction);
    }

    /**
     * @deprecated Use `halt()` instead.
     */
    public function hold(): void
    {
        $this->halt();
    }

    public function success(): void
    {
        $this->status = ActionStatus::Success;
    }

    public function failure(): void
    {
        $this->status = ActionStatus::Failure;
    }

    public function getStatus(): ActionStatus
    {
        if ($this->status) {
            return $this->status;
        }

        if (! $this->canAccessSelectedRecords()) {
            return ActionStatus::Success;
        }

        if ($this->successfulSelectedRecordsCount === $this->totalSelectedRecordsCount) {
            return ActionStatus::Success;
        }

        return ActionStatus::Failure;
    }

    public function bulk(bool | Closure $condition = true): static
    {
        $this->isBulk = $condition;

        return $this;
    }

    public function isBulk(): bool
    {
        return (bool) $this->evaluate($this->isBulk);
    }

    /**
     * @param  view-string  $view
     */
    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament::');
    }

    public function shouldMarkAsRead(): bool
    {
        return (bool) $this->evaluate($this->shouldMarkAsRead);
    }

    public function shouldMarkAsUnread(): bool
    {
        return (bool) $this->evaluate($this->shouldMarkAsUnread);
    }

    public function nestingIndex(?int $index): static
    {
        $this->nestingIndex = $index;

        return $this;
    }

    public function getNestingIndex(): ?int
    {
        return $this->nestingIndex;
    }

    public function renderModal(): View
    {
        return view('filament-actions::action-modal', [
            'action' => $this,
        ]);
    }

    public function toModalHtmlable(): Htmlable
    {
        return new HtmlString(Utils::insertAttributesIntoHtmlRoot($this->renderModal()->render(), [
            'wire:partial' => "action-modals.{$this->getNestingIndex()}",
        ]));
    }

    public function toHtml(): string
    {
        if (($this instanceof HasEmbeddedView) && (! $this->hasView())) {
            return $this->toEmbeddedHtml();
        }

        if ($this->canRenderOptimizedLink()) {
            return $this->toOptimizedLinkHtml();
        }

        if ($this->canRenderOptimizedGrouped()) {
            return $this->toOptimizedGroupedHtml();
        }

        return match ($this->getView()) {
            static::BADGE_VIEW => $this->toBadgeHtml(),
            static::BUTTON_VIEW => $this->toButtonHtml(),
            static::GROUPED_VIEW => $this->toGroupedHtml(),
            static::ICON_BUTTON_VIEW => $this->toIconButtonHtml(),
            static::LINK_VIEW => $this->toLinkHtml(),
            default => $this->render()->render(),
        };
    }

    protected function canRenderOptimizedLink(): bool
    {
        return ($this->getView() === static::LINK_VIEW)
            && $this->hasSmallSizeDefault()
            && ! $this->hasBadge()
            && ! $this->hasTooltip()
            && ! $this->hasIconPosition()
            && ! $this->hasIconSize()
            && ! $this->hasKeyBindings()
            && ! $this->hasLabeledFromBreakpoint()
            && ! $this->hasOutlined()
            && ! $this->hasLabelHidden()
            && ! $this->hasDisabled()
            && ! $this->hasShouldOpenUrlInNewTab()
            && ! $this->hasShouldPostToUrl()
            && ! $this->hasClose()
            && ! $this->hasCanAccessSelectedRecords()
            && ! $this->hasCustomModalPresence()
            && ! $this->hasAuthorization()
            && ! $this->hasExtraAttributes()
            && ! $this->hasMarkAsRead()
            && ! $this->hasMarkAsUnread()
            && ! $this->hasAlpineClickHandler()
            && ! $this->hasCustomLivewireClickHandler()
            && ! $this->hasLivewireTarget()
            && ! $this->hasCanSubmitForm()
            && ! $this->hasFormId()
            && ! $this->hasTableIcon()
            && ! $this->hasGroupedIcon();
    }

    public function hasMarkAsRead(): bool
    {
        return $this->shouldMarkAsRead !== false;
    }

    public function hasMarkAsUnread(): bool
    {
        return $this->shouldMarkAsUnread !== false;
    }

    public function hasAlpineClickHandler(): bool
    {
        return $this->alpineClickHandler !== null;
    }

    public function hasCustomLivewireClickHandler(): bool
    {
        return $this->isLivewireClickHandlerEnabled !== null;
    }

    public function hasLivewireTarget(): bool
    {
        return $this->livewireTarget !== null;
    }

    protected function toOptimizedLinkHtml(): string
    {
        $color = $this->getColor() ?? 'primary';

        if (is_array($color)) {
            $classString = 'fi-ac-link-action fi-link fi-size-sm fi-color';
            $styleString = ' style="' . implode('; ', FilamentColor::getComponentCustomStyles(LinkComponent::class, $color)) . '"';
        } else {
            $colorClasses = implode(' ', FilamentColor::getComponentClasses(LinkComponent::class, $color));
            $classString = "fi-ac-link-action fi-link fi-size-sm {$colorClasses}";
            $styleString = '';
        }

        $wireKey = $this->getLivewireKey();
        $wireKeyAttribute = $wireKey === null ? '' : ' wire:key="' . e($wireKey) . '"';

        $url = $this->getUrl();
        $icon = $this->getIcon();
        $label = e($this->getLabel());

        if (filled($url)) {
            $iconHtml = $icon ? generate_icon_html($icon, size: IconSize::Small)?->toHtml() : '';
            $hrefHtml = generate_href_html($url)->toHtml();

            return "<a {$hrefHtml}{$wireKeyAttribute} class=\"{$classString}\"{$styleString}>{$iconHtml}{$label}</a>";
        }

        $handler = $this->getLivewireClickHandler();

        if (blank($handler)) {
            return "<span{$wireKeyAttribute} class=\"{$classString}\"{$styleString}>{$label}</span>";
        }

        $loadingDelay = config('filament.livewire_loading_delay', 'default');

        $iconHtml = $icon ? generate_icon_html(
            $icon,
            attributes: (new FilamentComponentAttributeBag([
                'wire:loading.remove.delay.' . $loadingDelay => true,
                'wire:target' => $handler,
            ])),
            size: IconSize::Small,
        )?->toHtml() : '';

        $loadingHtml = generate_loading_indicator_html(
            (new FilamentComponentAttributeBag([
                'wire:loading.delay.' . $loadingDelay => '',
                'wire:target' => $handler,
            ])),
            size: IconSize::Small,
        )->toHtml();

        // Match `ComponentAttributeBag::__toString()` attribute escaping (only `"` → `\"`).
        $handler = str_replace('"', '\\"', $handler);

        return "<button type=\"button\" wire:loading.attr=\"disabled\" wire:click=\"{$handler}\"{$wireKeyAttribute} class=\"{$classString}\"{$styleString}>{$iconHtml}{$loadingHtml}{$label}</button>";
    }

    protected function canRenderOptimizedGrouped(): bool
    {
        return ($this->getView() === static::GROUPED_VIEW)
            && ! $this->hasBadge()
            && ! $this->hasTooltip()
            && ! $this->hasIconSize()
            && ! $this->hasKeyBindings()
            && ! $this->hasDisabled()
            && ! $this->hasShouldOpenUrlInNewTab()
            && ! $this->hasShouldPostToUrl()
            && ! $this->hasClose()
            && ! $this->hasCanAccessSelectedRecords()
            && ! $this->hasCustomModalPresence()
            && ! $this->hasAuthorization()
            && ! $this->hasExtraAttributes()
            && ! $this->hasMarkAsRead()
            && ! $this->hasMarkAsUnread()
            && ! $this->hasAlpineClickHandler()
            && ! $this->hasCustomLivewireClickHandler()
            && ! $this->hasLivewireTarget()
            && ! $this->hasCanSubmitForm();
    }

    protected function toOptimizedGroupedHtml(): string
    {
        $color = $this->getColor() ?? 'gray';

        if (is_array($color)) {
            $classString = 'fi-dropdown-list-item fi-ac-grouped-action fi-color';
            $styleString = ' style="' . implode('; ', FilamentColor::getComponentCustomStyles(ItemComponent::class, $color)) . '"';
        } else {
            $colorClasses = implode(' ', FilamentColor::getComponentClasses(ItemComponent::class, $color));
            $classString = "fi-dropdown-list-item fi-ac-grouped-action {$colorClasses}";
            $styleString = '';
        }

        $wireKey = $this->getLivewireKey();
        $wireKeyAttribute = $wireKey === null ? '' : ' wire:key="' . e($wireKey) . '"';

        $url = $this->getUrl();
        $icon = $this->getIcon(default: $this->getGroupedIcon());
        $label = e($this->getLabel());

        if (filled($url)) {
            $iconHtml = $icon ? generate_icon_html(
                $icon,
                attributes: (new FilamentComponentAttributeBag)->color(IconComponent::class, $color),
            )?->toHtml() : '';
            $hrefHtml = generate_href_html($url)->toHtml();

            return "<a {$hrefHtml}{$wireKeyAttribute} class=\"{$classString}\"{$styleString}>{$iconHtml}<span class=\"fi-dropdown-list-item-label\">{$label}</span></a>";
        }

        $handler = $this->getLivewireClickHandler();

        if (blank($handler)) {
            return "<button type=\"button\"{$wireKeyAttribute} class=\"{$classString}\"{$styleString}><span class=\"fi-dropdown-list-item-label\">{$label}</span></button>";
        }

        $loadingDelay = config('filament.livewire_loading_delay', 'default');

        $iconHtml = $icon ? generate_icon_html(
            $icon,
            attributes: (new FilamentComponentAttributeBag([
                'wire:loading.remove.delay.' . $loadingDelay => true,
                'wire:target' => $handler,
            ]))->color(IconComponent::class, $color),
        )?->toHtml() : '';

        $loadingHtml = generate_loading_indicator_html(
            (new FilamentComponentAttributeBag([
                'wire:loading.delay.' . $loadingDelay => '',
                'wire:target' => $handler,
            ])),
        )->toHtml();

        // Match `ComponentAttributeBag::__toString()` attribute escaping (only `"` → `\"`).
        $handlerEscaped = str_replace('"', '\\"', $handler);

        return "<button type=\"button\" wire:loading.attr=\"disabled\" wire:click=\"{$handlerEscaped}\"{$wireKeyAttribute} class=\"{$classString}\"{$styleString}>{$iconHtml}{$loadingHtml}<span class=\"fi-dropdown-list-item-label\">{$label}</span></button>";
    }

    protected function toBadgeHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return $this->generateBadgeHtml(
            attributes: (new FilamentComponentAttributeBag([
                'action' => $shouldPostToUrl ? e($url) : null,
                'method' => $shouldPostToUrl ? 'post' : null,
                'wire:click' => $this->getLivewireClickHandler(),
                'wire:key' => $this->getLivewireKey(),
                'wire:target' => $this->getLivewireTarget(),
                'x-on:click' => $this->getAlpineClickHandler(),
            ]))
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-badge-action']),
            color: $this->getColor(),
            form: $this->getFormToSubmit(),
            formId: $this->getFormId(),
            href: ($isDisabled || $shouldPostToUrl) ? null : $url,
            icon: $this->getIcon(default: $this->getTable() ? $this->getTableIcon() : null),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            isDisabled: $isDisabled,
            keyBindings: $this->getKeyBindings(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
            target: ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
            tooltip: $this->getTooltip(),
            type: $this->canSubmitForm() ? 'submit' : 'button',
        );
    }

    protected function toButtonHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return $this->generateButtonHtml(
            attributes: (new FilamentComponentAttributeBag([
                'action' => $shouldPostToUrl ? e($url) : null,
                'method' => $shouldPostToUrl ? 'post' : null,
                'wire:click' => $this->getLivewireClickHandler(),
                'wire:key' => $this->getLivewireKey(),
                'wire:target' => $this->getLivewireTarget(),
                'x-on:click' => $this->getAlpineClickHandler(),
            ]))
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-btn-action']),
            badge: $badge = $this->getBadge(),
            badgeColor: $this->getBadgeColor($badge),
            color: $this->getColor(),
            form: $this->getFormToSubmit(),
            formId: $this->getFormId(),
            href: ($isDisabled || $shouldPostToUrl) ? null : $url,
            icon: $this->getIcon(default: $this->getTable() ? $this->getTableIcon() : null),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            isDisabled: $isDisabled,
            isLabelSrOnly: $this->isLabelHidden(),
            isOutlined: $this->isOutlined(),
            keyBindings: $this->getKeyBindings(),
            label: $this->getLabel(),
            labeledFromBreakpoint: $this->getLabeledFromBreakpoint(),
            size: $this->getSize(),
            tag: $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
            target: ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
            tooltip: $this->getTooltip(),
            type: $this->canSubmitForm() ? 'submit' : 'button',
        );
    }

    protected function toGroupedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return $this->generateDropdownItemHtml(
            attributes: (new FilamentComponentAttributeBag([
                'action' => $shouldPostToUrl ? e($url) : null,
                'method' => $shouldPostToUrl ? 'post' : null,
                'wire:click' => $this->getLivewireClickHandler(),
                'wire:key' => $this->getLivewireKey(),
                'wire:target' => $this->getLivewireTarget(),
                'x-on:click' => $this->getAlpineClickHandler(),
            ]))
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-grouped-action']),
            badge: $badge = $this->getBadge(),
            badgeColor: $this->getBadgeColor($badge),
            badgeTooltip: $this->getBadgeTooltip($badge),
            color: $this->getColor(),
            href: ($isDisabled || $shouldPostToUrl) ? null : $url,
            icon: $this->getIcon(default: $this->getGroupedIcon()),
            iconSize: $this->getIconSize(),
            isDisabled: $isDisabled,
            keyBindings: $this->getKeyBindings(),
            label: $this->getLabel(),
            tag: $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
            target: ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
            tooltip: $this->getTooltip(),
            type: $this->canSubmitForm() ? 'submit' : 'button',
        );
    }

    protected function toIconButtonHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return $this->generateIconButtonHtml(
            attributes: (new FilamentComponentAttributeBag([
                'action' => $shouldPostToUrl ? e($url) : null,
                'method' => $shouldPostToUrl ? 'post' : null,
                'wire:click' => $this->getLivewireClickHandler(),
                'wire:key' => $this->getLivewireKey(),
                'wire:target' => $this->getLivewireTarget(),
                'x-on:click' => $this->getAlpineClickHandler(),
            ]))
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-icon-btn-action']),
            badge: $badge = $this->getBadge(),
            badgeColor: $this->getBadgeColor($badge),
            color: $this->getColor(),
            form: $this->getFormToSubmit(),
            formId: $this->getFormId(),
            href: ($isDisabled || $shouldPostToUrl) ? null : $url,
            icon: $this->getIcon(default: $this->getTable() ? $this->getTableIcon() : null),
            iconSize: $this->getIconSize(),
            isDisabled: $isDisabled,
            keyBindings: $this->getKeyBindings(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
            target: ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
            tooltip: $this->getTooltip(),
            type: $this->canSubmitForm() ? 'submit' : 'button',
        );
    }

    protected function toLinkHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return $this->generateLinkHtml(
            attributes: (new FilamentComponentAttributeBag([
                'action' => $shouldPostToUrl ? e($url) : null,
                'method' => $shouldPostToUrl ? 'post' : null,
                'wire:click' => $this->getLivewireClickHandler(),
                'wire:key' => $this->getLivewireKey(),
                'wire:target' => $this->getLivewireTarget(),
                'x-on:click' => $this->getAlpineClickHandler(),
            ]))
                ->merge($this->getExtraAttributes(), escape: false)
                ->class(['fi-ac-link-action']),
            badge: $badge = $this->getBadge(),
            badgeColor: $this->getBadgeColor($badge),
            color: $this->getColor(),
            form: $this->getFormToSubmit(),
            formId: $this->getFormId(),
            href: ($isDisabled || $shouldPostToUrl) ? null : $url,
            icon: $this->getIcon(default: $this->getTable() ? $this->getTableIcon() : null),
            iconPosition: $this->getIconPosition(),
            iconSize: $this->getIconSize(),
            isDisabled: $isDisabled,
            isLabelSrOnly: $this->isLabelHidden(),
            keyBindings: $this->getKeyBindings(),
            label: $this->getLabel(),
            size: $this->getSize(),
            tag: $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
            target: ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
            tooltip: $this->getTooltip(),
            type: $this->canSubmitForm() ? 'submit' : 'button',
        );
    }

    public function getClone(): static
    {
        return clone $this;
    }

    public function parentAction(?Action $action): static
    {
        $this->parentAction = $action;

        return $this;
    }

    public function getParentAction(): ?Action
    {
        return $this->parentAction;
    }
}
