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

    public function getState(): mixed
    {
        $column = $this->getColumn();
        $attribute = $column->getName();
        $query = $this->getQuery()->clone();

        if ($column->queriesRelationships($query->getModel())) {
            $relationship = $column->getRelationship($query->getModel());
            $attribute = $column->getRelationshipAttribute();

            $inverseRelationship = $column->getInverseRelationshipName ?? (string) str(class_basename($relationship->getParent()::class))
                ->plural()
                ->camel();

            $query = $relationship->getQuery()->getModel()->newQuery()
                ->whereHas(
                    $inverseRelationship,
                    fn (EloquentBuilder $relatedQuery): EloquentBuilder => $this->hasPaginatedQuery() ?
                        $relatedQuery->whereKey($this->getTable()->getRecords()->modelKeys()) :
                        $query,
                );
        }

        $query = DB::table($query->getQuery());

        if ($this->hasQueryModificationCallback()) {
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

    public function summarize(Builder $query, string $attribute): mixed
    {
        return null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    protected function hasPaginatedQuery(): bool
    {
        return $this->getQuery()->getQuery()->limit !== null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
            'table' => $this->getTable(),
        ]);
    }
}
