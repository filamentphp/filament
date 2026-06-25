<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function table(Table $table): Table
    {
        $ticket = Ticket::query()->findOrFail($this->ticketKey);

        return $table
            ->relationship(function () use ($ticket): BelongsToMany {
                $relationship = $ticket->departments()->withPivot('sort');

                if ($this->minimumQuantity !== null) {
                    $relationship->wherePivot('quantity', '>=', $this->minimumQuantity);
                }

                return $relationship;
            })
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->allowDuplicates($this->duplicates)
            ->reorderable('sort', direction: $this->direction)
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
