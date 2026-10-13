<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Livewire\Component;

class TicketDepartmentsReorderableTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public int $ticketKey;

    public bool $duplicates = false;

    public string $direction = 'asc';

    public ?int $minimumQuantity = null;

    public bool $isPaginated = false;

    public function table(Table $table): Table
    {
        $ticket = Ticket::query()->findOrFail($this->ticketKey);
        $configuredRelationship = $table->getRelationship();

        return $table
            ->relationship($configuredRelationship ? static fn () => $configuredRelationship : function () use ($ticket): BelongsToMany {
                $relationship = $ticket->departments()->withPivot(['id', 'sort']);

                if ($this->minimumQuantity !== null) {
                    $relationship->wherePivot('quantity', '>=', $this->minimumQuantity);
                }

                return $relationship;
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(isIndividual: true),
            ])
            ->allowDuplicates($this->duplicates)
            ->reorderable('sort', direction: $this->direction)
            ->paginated($this->isPaginated)
            ->toolbarActions([
                BulkAction::make('customBulk')
                    ->action(function (Collection | LazyCollection $records): void {
                        $this->dispatch('customBulk-called', quantities: $records->pluck('quantity')->all());
                    }),
                BulkAction::make('queryBulk')
                    ->fetchSelectedRecords(false)
                    ->action(function (BulkAction $action): void {
                        $this->dispatch('customBulk-called', quantities: $action->getSelectedRecordsQuery()->pluck('department_ticket.quantity')->all());
                    }),
                DeleteBulkAction::make(),
                DeleteBulkAction::make('queryBulkDelete')->fetchSelectedRecords(false),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
