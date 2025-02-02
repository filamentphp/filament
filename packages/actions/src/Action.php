<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\HasTooltip;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Livewire\Drawer\Utils;

class Action extends ViewComponent implements Arrayable
{
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToSchemaComponent;
    use Concerns\BelongsToTable;
    use Concerns\CanAccessSelectedRecords;
    use Concerns\CanBeAuthorized;
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
    use Concerns\HasExtraModalWindowAttributes;
    use Concerns\HasForm;
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
    use HasBadge;
    use HasColor;
    use HasExtraAttributes;
    use HasIcon;
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
        return [
            'name' => $this->getName(),
            'color' => $this->getColor(),
            'event' => $this->getEvent(),
            'eventData' => $this->getEventData(),
            'dispatchDirection' => $this->getDispatchDirection(),
            'dispatchToComponent' => $this->getDispatchToComponent(),
            'extraAttributes' => $this->getExtraAttributes(),
            'icon' => $this->getIcon(),
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

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
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

    public function badge(string | int | float | Closure | null $badge = null): static
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
        $this->alpineClickHandler = $handler;
        $this->livewireClickHandlerEnabled(blank($handler));

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

        return $this->getJavaScriptClickHandler();
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
        if (filled($handler = $this->evaluate($this->alpineClickHandler))) {
            return $handler;
        }

        if ($this->shouldClose()) {
            return 'close()';
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

        return $this->getJavaScriptClickHandler();
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
            return null;
        }

        return $this->getJavaScriptClickHandler();
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

    protected function getJavaScriptClickHandler(): ?string
    {
        if ($this->shouldClose()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
        }

        $context = [];

        if ($record = $this->getRecord()) {
            $context['recordKey'] = $this->resolveRecordKey($record);
        }

        if (filled($schemaComponentKey = ($this->getSchemaComponentContainer() ?? $this->getSchemaComponent())?->getKey())) {
            $context['schemaComponent'] = $schemaComponentKey;
        }

        $table = $this->getTable();

        if ($table) {
            $context['table'] = true;
        }

        if ($table && $this->isBulk()) {
            $context['bulk'] = true;
        }

        $contextParameter = '';

        if (filled($context)) {
            $contextParameter .= ', ';
            $contextParameter .= Js::from($context);

            if ($argumentsParameter === '') {
                $argumentsParameter = ', {}';
            }
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter}{$contextParameter})";
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'arguments' => [$this->getArguments()],
            'component', 'schemaComponent' => [$this->getSchemaComponent()],
            'context', 'operation' => [$this->getSchemaComponentContainer()?->getOperation() ?? $this->getSchemaComponent()?->getContainer()->getOperation()],
            'data' => [$this->getFormData()],
            'get' => [$this->getSchemaComponent()->makeGetUtility()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel() ?? $this->getSchemaComponentContainer()?->getModel() ?? $this->getSchemaComponent()?->getModel()],
            'mountedActions' => [$this->getLivewire()->getMountedActions()],
            'record' => [$this->getRecord() ?? $this->getSchemaComponentContainer()?->getRecord() ?? $this->getSchemaComponent()?->getRecord()],
            'records', 'selectedRecords' => [$this->getSelectedRecords()],
            'schema' => [$this->getSchemaComponentContainer()],
            'set' => [$this->getSchemaComponent()->makeSetUtility()],
            'state' => [$this->getSchemaComponent()->getState()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord() ?? $this->getSchemaComponentContainer()?->getRecord() ?? $this->getSchemaComponent()?->getRecord();

        return match ($parameterType) {
            EloquentCollection::class, Collection::class => [$this->getSelectedRecords()],
            Model::class, ($record instanceof Model) ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function shouldClearRecordAfter(): bool
    {
        $record = $this->getRecord();

        if (! ($record instanceof Model)) {
            return false;
        }

        return ! $record->exists;
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
        $this->sendSuccessNotification();
        $this->dispatchSuccessRedirect();
    }

    /**
     * @param  array<string>  $messages
     */
    public function failure(int $successCount = 0, int $totalCount = 0, int $missingMessageCount = 0, array $messages = []): void
    {
        $this->sendFailureNotification($successCount, $totalCount, $missingMessageCount, $messages);
        $this->dispatchFailureRedirect();
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

    /**
     * @param  array<string, mixed>  $props
     */
    protected function renderActionBladeComponentView(array $props = []): View
    {
        $isDisabled = $this->isDisabled();
        $url = $this->getUrl();
        $shouldPostToUrl = $this->shouldPostToUrl();

        return view(
            $this->getView(),
            [
                'attributes' => (new ComponentAttributeBag([
                    'action' => $shouldPostToUrl ? $url : null,
                    'method' => $shouldPostToUrl ? 'post' : null,
                    'wire:click' => $this->getLivewireClickHandler(),
                    'x-on:click' => $this->getAlpineClickHandler(),
                ]))
                    ->merge($this->getExtraAttributes(), escape: false)
                    ->class($props['class'] ?? []),
                'color' => $this->getColor(),
                'disabled' => $isDisabled,
                'form' => $this->getFormToSubmit(),
                'formId' => $this->getFormId(),
                'href' => ($isDisabled || $shouldPostToUrl) ? null : $url,
                'icon' => $props['icon'] ?? $this->getIcon(default: $this->getTable() ? $this->getTableIcon() : null),
                'iconSize' => $this->getIconSize(),
                'keyBindings' => $this->getKeyBindings(),
                'labelSrOnly' => $this->isLabelHidden(),
                'tag' => $url ? $shouldPostToUrl ? 'form' : 'a' : 'button',
                'target' => ($url && $this->shouldOpenUrlInNewTab()) ? '_blank' : null,
                'tooltip' => $this->getTooltip(),
                'type' => $this->canSubmitForm() ? 'submit' : 'button',
                ...Arr::except($props, ['class']),
                ...$this->viewData,
            ],
        );
    }

    protected function renderBadge(): View
    {
        return $this->renderActionBladeComponentView([
            'class' => 'fi-ac-badge-action',
            'iconPosition' => $this->getIconPosition(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ]);
    }

    protected function renderButton(): View
    {
        return $this->renderActionBladeComponentView([
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-btn-action',
            'iconPosition' => $this->getIconPosition(),
            'labeledFrom' => $this->getLabeledFromBreakpoint(),
            'outlined' => $this->isOutlined(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ]);
    }

    protected function renderGrouped(): View
    {
        return $this->renderActionBladeComponentView([
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-grouped-action',
            'icon' => $this->getIcon(default: $this->getGroupedIcon()),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ]);
    }

    protected function renderIconButton(): View
    {
        return $this->renderActionBladeComponentView([
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-icon-btn-action',
            'label' => $this->getLabel(),
            'size' => $this->getSize(),
        ]);
    }

    protected function renderLink(): View
    {
        return $this->renderActionBladeComponentView([
            'badge' => $this->getBadge(),
            'badgeColor' => $this->getBadgeColor(),
            'class' => 'fi-ac-link-action',
            'iconPosition' => $this->getIconPosition(),
            'size' => $this->getSize(),
            'slot' => new ComponentSlot(e($this->getLabel())),
        ]);
    }

    public function render(): View
    {
        return match ($this->getView()) {
            static::BADGE_VIEW => $this->renderBadge(),
            static::BUTTON_VIEW => $this->renderButton(),
            static::GROUPED_VIEW => $this->renderGrouped(),
            static::ICON_BUTTON_VIEW => $this->renderIconButton(),
            static::LINK_VIEW => $this->renderLink(),
            default => parent::render(),
        };
    }

    public function getClone(): static
    {
        return clone $this;
    }
}
