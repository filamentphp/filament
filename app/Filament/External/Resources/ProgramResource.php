<?php

namespace App\Filament\External\Resources;

use App\Enums\ProgramStatus;
use App\Filament\External\Resources\ProgramResource\Pages;
use App\Models\Program;
use App\Services\AuditService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAdmin() || $user->isPUPusat()) {
            return $query;
        }

        if ($user->isPemda()) {
            $scope = $user->pemda_scope['lokasi'] ?? null;
            if ($scope) {
                return $query->where('lokasi', 'like', "%{$scope}%");
            }
            return $query->whereRaw('0=1'); // No scope, no data
        }

        if ($user->isKL()) {
            $scope = $user->kl_scope['sektor'] ?? null;
            if ($scope) {
                return $query->where('sektor', $scope);
            }
            return $query->whereRaw('0=1');
        }

        return $query->whereRaw('0=1'); // Default deny
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_program')->disabled(),
                Forms\Components\TextInput::make('sektor')->disabled(),
                Forms\Components\TextInput::make('lokasi')->disabled(),
                Forms\Components\TextInput::make('estimasi_biaya')->disabled()->numeric(),
                Forms\Components\TextInput::make('status')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => $state instanceof ProgramStatus ? $state->value : $state),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_program')->searchable(),
                Tables\Columns\TextColumn::make('sektor'),
                Tables\Columns\TextColumn::make('lokasi'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Program $record) => Pages\ViewProgram::getUrl(['record' => $record])),
                Tables\Actions\Action::make('download_ba')
                    ->label('Download Berita Acara')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn (Program $record) => $record->status === ProgramStatus::BERITA_ACARA && $record->beritaAcara)
                    ->action(function (Program $record) {
                        // Audit Log
                        app(AuditService::class)->log(
                            'berita_acara.downloaded',
                            'Program',
                            $record->id,
                            ['actor_type' => 'EXTERNAL']
                        );

                        return response()->download(storage_path('app/' . $record->beritaAcara->file_pdf));
                    }),
            ])
            ->bulkActions([]); // No bulk actions
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'view' => Pages\ViewProgram::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
