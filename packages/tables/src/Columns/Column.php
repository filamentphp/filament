<?php

namespace Filament\Tables\Columns;

use Filament\Actions\Action;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanAggregateRelatedModels;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\CanSpanColumns;
use Filament\Support\Concerns\CanWrapHeader;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasCellState;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasRelatedRecord;
use Filament\Support\Concerns\HasVerticalAlignment;
use Filament\Support\Concerns\HasWidth;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Concerns\HasTooltip;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Livewire\Component;
use LogicException;

use function Filament\Support\generate_href_html;

class Column extends ViewComponent
{
    use CanAggregateRelatedModels;
    use CanGrow;
    use CanSpanColumns;
    use CanWrapHeader;
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToLayout;
    use Concerns\BelongsToTable;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeSearchable;
    use Concerns\CanBeSortable;
    use Concerns\CanBeSummarized;
    use Concerns\CanBeToggled;
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;
    use Concerns\HasExtraCellAttributes;
    use Concerns\HasExtraHeaderAttributes;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasRecord;
    use Concerns\HasRowLoop;
    use Concerns\InteractsWithTableQuery;
    use HasAlignment;
    use HasCellState;
    use HasExtraAttributes;
    use HasPlaceholder;
    use HasRelatedRecord;
    use HasTooltip;
    use HasVerticalAlignment;
    use HasWidth;

    protected string $evaluationIdentifier = 'column';

    protected string $viewIdentifier = 'column';

    protected ?string $cachedCellAttributeHtml = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $columnClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new LogicException("Column of class [$columnClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($columnClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getTable(): Table
    {
        return $this->table ?? $this->getGroup()?->getTable() ?? $this->getLayout()?->getTable() ?? throw new LogicException("The column [{$this->getName()}] is not mounted to a table.");
    }

    public function hasTable(): bool
    {
        return isset($this->table) || ($this->getGroup()?->hasTable() ?? false) || ($this->getLayout()?->hasTable() ?? false);
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'record' => [$this->getRecord()],
            'rowLoop' => [$this->getRowLoop()],
            'state' => [$this->getState()],
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

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        if (! ($record instanceof Model)) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function renderInLayout(): ?HtmlString
    {
        if ($this->isHidden()) {
            return null;
        }

        $attributes = (new FilamentComponentAttributeBag)
            ->gridColumn(
                $this->getColumnSpan(),
                $this->getColumnStart(),
            )
            ->class([
                'fi-growable' => $this->canGrow(),
                (filled($hiddenFrom = $this->getHiddenFrom()) ? "{$hiddenFrom}:fi-hidden" : ''),
                (filled($visibleFrom = $this->getVisibleFrom()) ? "{$visibleFrom}:fi-visible" : ''),
            ]);

        $this->inline();

        $action = $this->getAction();
        $url = $this->getUrl();
        $isClickDisabled = $this->isClickDisabled();

        $wrapperTag = match (true) {
            $url && (! $isClickDisabled) => 'a',
            $action && (! $isClickDisabled) => 'button',
            default => 'div',
        };

        $attributes = $attributes
            ->merge([
                'type' => ($wrapperTag === 'button') ? 'button' : null,
                'wire:click.prevent.stop' => $wireClickAction = match (true) {
                    ($wrapperTag !== 'button') => null,
                    $action instanceof Action => "mountTableAction('{$action->getName()}', " . Js::from($this->getRecordKey()) . ')',
                    filled($action) => "callTableColumnAction('{$this->getName()}', " . Js::from($this->getRecordKey()) . ')',
                    default => null,
                },
                'wire:loading.attr' => ($wrapperTag === 'button') ? 'disabled' : null,
                'wire:target' => $wireClickAction,
            ])
            ->class([
                'fi-ta-col',
                ((($alignment = $this->getAlignment()) instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
                'fi-ta-col-has-column-url' => ($wrapperTag === 'a') && filled($url),
            ]);

        ob_start(); ?>

        <<?= $wrapperTag ?>
            <?php if ($wrapperTag === 'a') {
                echo generate_href_html($url, $this->shouldOpenUrlInNewTab())->toHtml();
            } ?>
            <?= $attributes->toHtml() ?>
        >
            <?= $this->toHtml() ?>
        </<?= $wrapperTag ?>>

        <?php return new HtmlString(ob_get_clean());
    }

    public function renderInTableCell(?string $recordAction = null, ?string $recordUrl = null, bool $shouldOpenRecordUrlInNewTab = false, bool $isPartiallyRenderable = false): HtmlString
    {
        $table = $this->getTable();
        $livewire = $this->getLivewire();
        $recordKey = $this->getRecordKey();

        $action = $this->getAction();
        $url = $this->getUrl();
        $hasStateBasedUrls = $this->hasStateBasedUrls();
        $isClickDisabled = $this->isClickDisabled() || $table->isReordering();
        $isStackedOnMobile = $table->isStackedOnMobile();

        $wrapperTag = match (true) {
            ($url || ($recordUrl && $action === null)) && (! $hasStateBasedUrls) && (! $isClickDisabled) => 'a',
            ($action || $recordAction) && (! $hasStateBasedUrls) && (! $isClickDisabled) => 'button',
            default => 'div',
        };

        $wireClickAction = null;

        if ($wrapperTag === 'button') {
            if ($action instanceof Action) {
                $wireClickAction = "mountTableAction('{$action->getName()}', " . Js::from($recordKey) . ')';
            } elseif ($action) {
                $wireClickAction = "callTableColumnAction('{$this->getName()}', " . Js::from($recordKey) . ')';
            } elseif ($table->getAction($recordAction)) {
                $wireClickAction = "mountTableAction('{$recordAction}', " . Js::from($recordKey) . ')';
            } else {
                $wireClickAction = "{$recordAction}(" . Js::from($recordKey) . ')';
            }
        }

        ob_start(); ?>

        <td
            wire:key="<?= ($livewire instanceof Component) ? e($livewire->getId()) : '' ?>.table.record.<?= e($recordKey) ?>.column.<?= e($this->getName()) ?>"
            <?php if ($isPartiallyRenderable) { ?>
                wire:partial="<?= e($this->getTableCellPartialName()) ?>"
            <?php } ?>
            <?= $this->getCachedCellAttributeHtml() ?>
        >
            <?= $isStackedOnMobile ? '<div class="fi-ta-cell-label">' . e($this->getLabel()) . '</div><div class="fi-ta-cell-content">' : '' ?>
            <<?= $wrapperTag ?>
                <?php if ($wrapperTag === 'a') { ?>
                    <?= generate_href_html($url ?: $recordUrl, $url ? $this->shouldOpenUrlInNewTab() : $shouldOpenRecordUrlInNewTab, hasNestedClickEventHandler: true)->toHtml() ?>
                    <?php if (blank($url) && filled($recordUrl)) { ?>
                        <?= $table->getExtraRecordLinkAttributeBag($this->getRecord())->toHtml() ?>
                    <?php } ?>
                <?php } elseif ($wrapperTag === 'button') { ?>
                    type="button"
                    wire:click.prevent.stop="<?= e($wireClickAction) ?>"
                    wire:loading.attr="disabled"
                    wire:target="<?= e($wireClickAction) ?>"
                <?php } ?>
                class="<?= Arr::toCssClasses([
                    'fi-ta-col',
                    'fi-ta-col-has-column-url' => ($wrapperTag === 'a') && filled($url),
                ]) ?>"
            >
                <?= $this->toHtml() ?>
            </<?= $wrapperTag ?>>
            <?= $isStackedOnMobile ? '</div>' : '' ?>
        </td>

        <?php return new HtmlString(ob_get_clean());
    }

    public function getTableCellPartialName(): string
    {
        // Strip characters that could break the `wire:partial` attribute selector used to find the cell in the DOM.
        return preg_replace('/[<>"\'`\\\\\x00-\x1F\x7F]/', '', "table.record.{$this->getRecordKey()}.column.{$this->getName()}");
    }

    public function getCellAttributeHtml(): string
    {
        $columnAlignment = $this->getAlignment();
        $columnVerticalAlignment = $this->getVerticalAlignment();

        return $this->getExtraCellAttributeBag()->class([
            'fi-ta-cell',
            'fi-ta-cell-' . str($this->getName())->camel()->kebab(),
            (($columnAlignment instanceof Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
            (($columnVerticalAlignment instanceof VerticalAlignment) ? "fi-vertical-align-{$columnVerticalAlignment->value}" : (is_string($columnVerticalAlignment) ? $columnVerticalAlignment : '')),
            (filled($columnHiddenFrom = $this->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
            (filled($columnVisibleFrom = $this->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
        ])->toHtml();
    }

    public function getCachedCellAttributeHtml(): string
    {
        if ($this->cachedCellAttributeHtml !== null) {
            return $this->cachedCellAttributeHtml;
        }

        if (
            $this->hasDynamicExtraCellAttributes()
            || $this->hasDynamicAlignment()
            || $this->hasDynamicVerticalAlignment()
            || $this->hasDynamicHiddenFrom()
            || $this->hasDynamicVisibleFrom()
        ) {
            return $this->getCellAttributeHtml();
        }

        return $this->cachedCellAttributeHtml = $this->getCellAttributeHtml();
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraViewData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }
}
