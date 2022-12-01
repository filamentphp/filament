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
            // if the column being summarized is a dotted relationship, we need to invert the main query so it
            // selects records from the last relationship in the chain, based on the ID's of the main table
            // rows currently being summarized.  This involves building an inverse relationship chain, and nesting
            // a whereHas with it that constrains the inverse query to just the main table ID's.

            $record = $query->getModel();
            $relationship = $column->getLastRelationship($record);
            $attribute = $column->getRelationshipAttribute();

            if ($column->getInverseRelationshipName()) {
                // if an inverse chain was provided, use it
                $inverseRelationship = $column->getInverseRelationshipName();
                // ... and reset $record to the last in the column's chain, hence first in inverse chain
                $record = $column->getLastRelationshipRecord($record);
            } else {
                // if no inverse chain give, attempt to derive it with a call to getInverseRelationships,
                // which will also reset $record to the first relationship in the inverse chain
                $inverseRelationship = $column->getInverseRelationships(
                    $column->getName(),
                    $record
                );
            }

            if ($inverseRelationship) {
                // if we got a valid inverse, nest the relevant query to get the related records from the end of the
                // chain.
                //
                // At this point ...
                // $relationship is the last relationship in the column's chain, and hence first in the inverse chain.
                // $record is the record from the last column relationship chain, and hence first in the inverse chain.
                // $query is the original column query which we will use to get all the id's of the queried records.

                $parentQuery = $query;

                $query = $column->getNestedQuery(
                    $relationship->getQuery()->getModel()->newQuery(),
                    $inverseRelationship,
                    $record,
                    fn (EloquentBuilder $query) => $this->hasPaginatedQuery() ?
                        $query->whereKey($this->getTable()->getRecords()->modelKeys()) :
                        $query->whereIn(
                            $relationship->getModel()->getKeyName(),
                            $parentQuery->select($relationship->getModel()->getKeyName())
                        )
                );
            }
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
