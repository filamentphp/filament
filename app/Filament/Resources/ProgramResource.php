<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStatus;
use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use App\Services\WorkflowService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_program')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
                Forms\Components\TextInput::make('sektor')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
                Forms\Components\TextInput::make('lokasi')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
                Forms\Components\TextInput::make('estimasi_biaya')
                    ->required()
                    ->numeric()
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
                // Status is explicitly excluded from the form
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sektor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estimasi_biaya')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ProgramStatus $state): string => match ($state) {
                        ProgramStatus::TERDAFTAR => 'gray',
                        ProgramStatus::DIBAHAS_PU => 'info',
                        ProgramStatus::CATATAN_KL => 'warning',
                        ProgramStatus::KONSOLIDASI_PEMDA => 'primary',
                        ProgramStatus::BERITA_ACARA => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->isAdmin()),
                Tables\Actions\Action::make('advanceStatus')
                    ->label('Advance Status')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Program $record) => auth()->user()->isAdmin() && $record->status !== ProgramStatus::BERITA_ACARA)
                    ->action(function (Program $record) {
                        try {
                            app(WorkflowService::class)->advanceStatus($record);
                            Notification::make()
                                ->title('Status updated successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->title('Failed to update status')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->pages([
                'index' => Pages\ListPrograms::route('/'),
                'create' => Pages\CreateProgram::route('/create'),
                'view' => Pages\ViewProgram::route('/{record}'),
                'edit' => Pages\EditProgram::route('/{record}/edit'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CatatansRelationManager::class,
            RelationManagers\BeritaAcaraRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Strict no-delete
    }
}
