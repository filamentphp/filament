<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\HiddenDepartmentTicket;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Translatable\RecordingTranslatableContentDriver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScopedTicketDepartmentsTable extends TicketDepartmentsReorderableTable
{
    public string $constraint = 'wherePivot';

    public bool $hasHiddenPivot = false;

    public bool $hasPivotFields = true;

    public bool $fetchSelectedRecords = true;

    public ?int $chunkSize = null;

    public bool $isTranslatable = false;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return $this->isTranslatable ? RecordingTranslatableContentDriver::class : null;
    }

    public function table(Table $table): Table
    {
        $configuredRelationship = $table->getRelationship();
        $schema = $this->hasPivotFields ? [
            TextInput::make('price')->numeric(),
            TextInput::make('quantity')->numeric(),
            TextInput::make('pivot_quantity')->numeric()->dehydrated(false),
            TextInput::make('created_at')->dehydrated(false),
            TextInput::make('pivot_created_at')->dehydrated(false),
        ] : [TextInput::make('name')->required()];

        return parent::table($table)
            ->relationship($configuredRelationship ? static fn (): BelongsToMany => clone $configuredRelationship : function (): BelongsToMany {
                $relationship = Ticket::findOrFail($this->ticketKey)->departments()->withPivot(['id', 'sort'])->as('attachment');

                if ($this->hasHiddenPivot) {
                    $relationship->using(HiddenDepartmentTicket::class);
                }

                if ($this->constraint === 'wherePivot') {
                    $relationship->wherePivot('quantity', 7);
                } elseif ($this->constraint === 'withPivotValue') {
                    $relationship->withPivotValue('quantity', 7);
                } elseif ($this->constraint === 'global scope') {
                    $relationship->getQuery()->withGlobalScope('quantity', static fn (Builder $query): Builder => $query->where('department_ticket.quantity', 7));
                }

                return $relationship;
            })
            ->modifyQueryUsing(fn (Builder $query): Builder => $this->constraint === 'modifyQueryUsing' ? $query->where('department_ticket.quantity', 7) : $query)
            ->columns([TextInputColumn::make('price')])
            ->recordActions([
                EditAction::make()->schema($schema),
                ViewAction::make()->schema($schema),
                ReplicateAction::make()->schema($schema),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make()->fetchSelectedRecords($this->fetchSelectedRecords)->chunkSelectedRecords($this->chunkSize),
            ]);
    }
}
