<?php

namespace App\Filament\Audit\Resources;

use App\Filament\Audit\Resources\AuditStatusHistoryResource\Pages;
use App\Models\ProgramStatusHistory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditStatusHistoryResource extends Resource
{
    protected static ?string $model = ProgramStatusHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Status History';

    public static function canViewAny(): bool
    {
        // Task 1: BPK Only for "State Transition" access?
        // Task 1 says: BPK -> "Akses RIWAYAT STATUS". ITJEN -> Not explicitly mentioned in Task 1 table for History, but Task 5 says "Menu: ... Status History".
        // Task 5 implies Audit Panel has Status History.
        // Task 2 Matrix: "View Audit Log" is limited for ITJEN, full for BPK.
        // Let's allow BPK fully. ITJEN? Maybe read-only.
        // Task 1 "BPK ... Akses RIWAYAT STATUS". ITJEN list does not mention it.
        // I will allow BPK only based on Task 1 distinction.
        return auth()->user()->isBpk();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.nama_program'),
                Tables\Columns\TextColumn::make('from_status')->badge(),
                Tables\Columns\TextColumn::make('to_status')->badge(),
                Tables\Columns\TextColumn::make('changedBy.name')->label('Changed By'),
                Tables\Columns\TextColumn::make('changed_by_role'),
                Tables\Columns\TextColumn::make('changed_at')->dateTime(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditStatusHistories::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
}
