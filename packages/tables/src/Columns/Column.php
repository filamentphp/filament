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
use Filament\Support\Concerns\HasVerticalAlignment;
use Filament\Support\Concerns\HasWidth;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Concerns\HasTooltip;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;
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
    use Concerns\HasRowLoopObject;
    use Concerns\InteractsWithTableQuery;
    use HasAlignment;
    use HasCellState;
    use HasExtraAttributes;
    use HasPlaceholder;
    use HasTooltip;
    use HasVerticalAlignment;
    use HasWidth;

    protected string $evaluationIdentifier = 'column';

    protected string $viewIdentifier = 'column';

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
        $record = $this->getRecord();

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

        $attributes = (new ComponentAttributeBag)
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
                    $action instanceof Action => "mountTableAction('{$action->getName()}', '{$this->getRecordKey()}')",
                    filled($action) => "callTableColumnAction('{$this->getName()}', '{$this->getRecordKey()}')",
                    default => null,
                },
                'wire:loading.attr' => ($wrapperTag === 'button') ? 'disabled' : null,
                'wire:target' => $wireClickAction,
            ], escape: false)
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
