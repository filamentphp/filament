<?php

namespace Filament\Forms\Components\TableSelect\Livewire;

use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Services\RelationshipJoiner;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithoutUrlPagination;

class TableSelectLivewireComponent extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;
    use WithoutUrlPagination;

    #[Locked]
    public bool $isDisabled = false;

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
            throw new Exception("Table configuration class [{$tableConfiguration}] does not exist.");
        }

        if (! method_exists($tableConfiguration, 'configure')) {
            throw new Exception("Table configuration class [{$tableConfiguration}] does not have a [configure(Table \$table): Table] method.");
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
            $table->query(function (): Builder {
                $relationship = Relation::noConstraints(fn (): Relation => ($this->record ??= app($this->model))->{$this->relationshipName}());

                $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                if (! ($relationship instanceof BelongsToMany)) {
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
