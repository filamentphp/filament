<?php

namespace Filament\Tables\Columns\Summarizers;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;

class Summarizer extends ViewComponent implements HasEmbeddedView
{
    use Concerns\BelongsToColumn;
    use Concerns\CanBeHidden;
    use Concerns\CanFormatState;
    use Concerns\HasLabel;
    use Concerns\InteractsWithTableQuery;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'summarizer';

    protected string $viewIdentifier = 'summarizer';

    protected ?string $id = null;

    /**
     * @var array<string, mixed>
     */
    protected array $selectedState = [];

    protected ?Closure $using = null;

    final public function __construct(?string $id = null)
    {
        $this->id($id);
    }

    public static function make(?string $id = null): static
    {
        $static = app(static::class, ['id' => $id]);
        $static->configure();

        return $static;
    }

    public function id(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function using(?Closure $using): static
    {
        $this->using = $using;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function selectedState(array $state): static
    {
        $this->selectedState = $state;

        return $this;
    }

    public function getState(): mixed
    {
        if (filled($state = $this->getSelectedState())) {
            return $state;
        }

        $column = $this->getColumn();
        $attribute = $column->getName();
        $query = $this->getQuery()?->clone();

        $hasRelationship = $query && $column->hasRelationship($query->getModel());

        if ($this->hasQueryModificationCallback() && $hasRelationship) {
            $baseQueryForModification = $query->getQuery();
            $this->evaluate($this->modifyQueryUsing, [
                'attribute' => $attribute,
                'query' => $baseQueryForModification,
            ]);
        }

        if ($hasRelationship) {
            $relationship = $column->getRelationship($query->getModel());
            $attribute = $column->getFullAttributeName($query->getModel());

            $relatedModel = clone $relationship->getRelated();
            $relatedTable = $relatedModel->getTable();
            $relatedModel->setTable($relationship->getRelationCountHash());

            $baseQuery = $query->toBase();

            if (($baseQuery->limit === null) && ($baseQuery->offset === null) && ($baseQuery->unionLimit === null) && ($baseQuery->unionOffset === null)) {
                $baseQuery->reorder();
            }

            // Keep the table's filters, scopes and limits inside the derived query.
            // Eloquent builds every forward relationship constraint in `whereHas()`.
            $parentQuery = $query->getModel()->newModelQuery()
                ->whereIn(
                    $query->getModel()->getQualifiedKeyName(),
                    $query->getConnection()->table($baseQuery, $query->getModel()->getTable())
                        ->select($query->getModel()->getQualifiedKeyName()),
                )
                ->whereHas(
                    $column->getRelationshipName($query->getModel()),
                    fn (EloquentBuilder $relatedQuery) => $relatedQuery->whereColumn(
                        $relatedQuery->getModel()->getQualifiedKeyName(),
                        $relatedModel->getQualifiedKeyName(),
                    ),
                );

            $query = $relationship->getRelated()->newModelQuery()
                ->from("{$relatedTable} as {$relatedModel->getTable()}")
                ->whereExists($parentQuery->selectRaw('1')->toBase());
        } elseif ($query) {
            // https://github.com/filamentphp/filament/issues/12501
            // Handle pivot columns in `BelongsToMany` context.
            // This handles two cases:
            // 1. Columns defined as `pivot.quantity` (direct pivot access)
            // 2. Columns defined as `quantity` in a `RelationManager` (implicit pivot column)

            $pivotAttribute = str($attribute)->startsWith('pivot.')
                ? (string) str($attribute)->after('pivot.')->prepend('pivot_')
                : 'pivot_' . $attribute;

            $isPivotAttributeSelected = collect($query->getQuery()->getColumns())
                ->contains(fn (string $column): bool => str($column)->endsWith(" as {$pivotAttribute}"));

            if ($isPivotAttributeSelected) {
                $attribute = $pivotAttribute;
            }

            // Remove the join table's wildcard to prevent duplicate column
            // errors (e.g., both tables have `id`) when the query is used
            // as a subquery in MySQL. This applies to all columns in a
            // `BelongsToMany` context, not just pivot columns.
            $hasPivotColumns = collect($query->getQuery()->getColumns())
                ->contains(fn (string $column): bool => str($column)->contains(' as pivot_'));

            if ($hasPivotColumns && ($joinTable = ($query->getQuery()->joins[0]->table ?? null))) {
                $query->getQuery()->columns = array_filter(
                    $query->getQuery()->columns,
                    fn (mixed $column): bool => ! is_string($column) || $column !== "{$joinTable}.*",
                );
            }
        }

        $asName = (string) str($query?->getModel()->getTable())->afterLast('.');
        $baseQuery = $query?->toBase();

        if (($baseQuery?->getGrammar() instanceof SqlServerGrammar) && ($baseQuery->limit === null) && ($baseQuery->offset === null) && ($baseQuery->unionLimit === null) && ($baseQuery->unionOffset === null)) {
            $baseQuery->reorder();
        }

        $query = $query?->getModel()->resolveConnection($query->getModel()->getConnectionName())
            ->table($baseQuery, $asName);

        if ($this->hasQueryModificationCallback() && ! $hasRelationship) {
            $query = $this->evaluate($this->modifyQueryUsing, [
                'attribute' => $attribute,
                'query' => $query,
            ]) ?? $query;
        }

        if ($this->using !== null) {
            return $this->evaluate($this->using, [
                'attribute' => $attribute,
                'query' => $query,
            ]);
        }

        return $this->summarize($query, $attribute);
    }

    public function getSelectedState(): mixed
    {
        return null;
    }

    public function summarize(Builder $query, string $attribute): mixed
    {
        return null;
    }

    /**
     * @return array<string, string>
     */
    public function getSelectStatements(string $column): array
    {
        return [];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'table' => [$this->getTable()],
            'query' => [$this->getQuery()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    public function toEmbeddedHtml(): string
    {
        $label = $this->getLabel();
        $isLabelHidden = $this->isLabelHidden();

        $attributes = $this->getExtraAttributeBag()
            ->class(['fi-ta-text-summary']);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if (filled($label)) { ?>
                <span class="fi-ta-text-summary-label<?= $isLabelHidden ? ' fi-sr-only' : '' ?>">
                    <?= e($label) ?>
                </span>
            <?php } ?>

            <span>
                <?= e($this->formatState($this->getState())) ?>
            </span>
        </div>

        <?php return ob_get_clean();
    }
}
