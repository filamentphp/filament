<?php

namespace Filament\Tables\Columns\Summarizers;

use Closure;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Summarizer extends ViewComponent
{
    use Concerns\BelongsToColumn;
    use Concerns\CanFormatState;
    use Concerns\HasLabel;
    use Concerns\InteractsWithTableQuery;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'summarizer';

    protected string $viewIdentifier = 'summarizer';

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.summaries.text';

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
        $query = $this->getQuery()->clone();

        if ($column->queriesRelationships($query->getModel())) {
            $relationship = $column->getRelationship($query->getModel());
            $attribute = $column->getRelationshipAttribute();

            $inverseRelationship = $column->getInverseRelationshipName($query->getModel());

            $baseQuery = $query->toBase();

            $query = $relationship->getQuery()->getModel()->newQuery()
                ->whereHas(
                    $inverseRelationship,
                    function (EloquentBuilder $relatedQuery) use ($baseQuery, $query): EloquentBuilder {
                        $relatedQuery->mergeConstraintsFrom($query);

                        if ($baseQuery->limit !== null) {
                            $relatedQuery->whereKey($this->getTable()->getRecords()->modelKeys());
                        }

                        return $relatedQuery;
                    },
                );
        }

        $query = DB::table($query->toBase(), $query->getModel()->getTable());

        if ($this->hasQueryModification()) {
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
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
