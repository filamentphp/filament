<?php

namespace Filament\Forms\Components\TableSelect\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Services\RelationshipJoiner;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use LogicException;

class TableSelectLivewireComponent extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;
    use WithoutUrlPagination;

    #[Locked]
    public bool $isDisabled = false;

    #[Locked]
    public bool $shouldIgnoreRelatedRecords = false;

    #[Locked]
    public ?int $maxSelectableRecords = null;

    #[Locked]
    public ?string $model = null;

    #[Locked]
    public ?Model $record = null;

    #[Locked]
    public ?string $relationshipName = null;

    #[Locked]
    public string $tableConfiguration;

    /**
     * @var array<mixed>
     */
    #[Locked]
    public array $tableArguments = [];

    /**
     * @var string | array<string> | null
     */
    #[Modelable]
    public string | array | null $state = null;

    public function table(Table $table): Table
    {
        $tableConfiguration = base64_decode($this->tableConfiguration);

        if (! class_exists($tableConfiguration)) {
            throw new LogicException("Table configuration class [{$tableConfiguration}] does not exist.");
        }

        if (! method_exists($tableConfiguration, 'configure')) {
            throw new LogicException("Table configuration class [{$tableConfiguration}] does not have a [configure(Table \$table): Table] method.");
        }

        $tableConfiguration::configure($table);

        $table
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateActions([])
            ->selectable()
            ->trackDeselectedRecords(false)
            ->currentSelectionLivewireProperty('state')
            ->maxSelectableRecords(is_array($this->state) ? $this->maxSelectableRecords : 1)
            ->deselectAllRecordsWhenFiltered(false)
            ->disabledSelection($this->isDisabled)
            ->arguments($this->getTableArguments());

        if (filled($this->relationshipName)) {
            $table->query(function (): EloquentBuilder {
                $relationship = Relation::noConstraints(fn (): Relation => ($this->record ??= app($this->model))->{$this->relationshipName}());

                $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                if (! ($relationship instanceof BelongsToMany)) {
                    return $relationshipQuery;
                }

                if ($this->shouldIgnoreRelatedRecords) {
                    $relationshipQuery->whereNotExists(function (Builder $query) use ($relationship): void {
                        $query
                            ->select($relationship->getConnection()->raw(1))
                            ->from($relationship->getTable())
                            ->whereColumn(
                                $relationship->getQualifiedRelatedPivotKeyName(),
                                $relationship->getQualifiedRelatedKeyName(),
                            )
                            ->where(
                                $relationship->getQualifiedForeignPivotKeyName(),
                                $this->record->getAttribute($relationship->getParentKeyName()),
                            );

                        if ($relationship instanceof MorphToMany) {
                            $query->where(
                                $relationship->qualifyPivotColumn($relationship->getMorphType()),
                                $relationship->getMorphClass(),
                            );
                        }
                    });

                    return $relationshipQuery;
                }

                $relationshipBaseQuery = $relationshipQuery->getQuery();

                if (blank($relationshipBaseQuery->joins ?? [])) {
                    return $relationshipQuery;
                }

                array_shift($relationshipBaseQuery->joins);

                return $relationshipQuery;
            });
        }

        return $table;
    }

    /**
     * @return array<mixed>
     */
    public function getTableArguments(): array
    {
        return $this->tableArguments;
    }

    public function render(): string
    {
        return '{{ $this->table }}';
    }
}
