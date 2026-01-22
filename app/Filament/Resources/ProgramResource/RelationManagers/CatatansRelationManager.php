<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Services\CatatanService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CatatansRelationManager extends RelationManager
{
    protected static string $relationship = 'catatans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sumber')
                    ->options([
                        'PU' => 'PU',
                        'BALAI' => 'BALAI',
                        'KL' => 'KL',
                        'PEMDA' => 'PEMDA',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('isi_catatan')
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('tahap'),
                Tables\Columns\TextColumn::make('sumber'),
                Tables\Columns\TextColumn::make('isi_catatan')
                    ->limit(50),
                Tables\Columns\TextColumn::make('dicatatOleh.name')
                    ->label('Dicatat Oleh'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()->isAdmin())
                    ->using(function (array $data, string $model): Model {
                        $program = $this->getOwnerRecord();
                        // Inject tahap from program status logic
                        // The service expects 'tahap' in data to match program status
                        // Or we can let the user select it?
                        // Prompt says: "tahap HARUS SAMA dengan status program saat ini... Jika mismatch -> THROW EXCEPTION"
                        // So we should auto-fill it or pass it.
                        // Let's auto-fill it to ensure success.

                        /** @var \App\Models\Program $program */
                        $data['tahap'] = $program->status->value;

                        return app(CatatanService::class)->addCatatan($program, $data);
                    }),
            ])
            ->actions([
                // No Edit or Delete allowed
            ]);
    }
}
