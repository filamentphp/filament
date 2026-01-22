<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Enums\ProgramStatus;
use App\Services\BeritaAcaraService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class BeritaAcaraRelationManager extends RelationManager
{
    protected static string $relationship = 'beritaAcara';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('keputusan')
                    ->options([
                        'LANJUT' => 'LANJUT',
                        'TANGGUH' => 'TANGGUH',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('ringkasan_kesepakatan')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\FileUpload::make('file_pdf')
                    ->required()
                    ->acceptedFileTypes(['application/pdf'])
                    ->storeFiles(false), // We handle storage in Service
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('keputusan'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('dibuatOleh.name')->label('Dibuat Oleh'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function () {
                        /** @var \App\Models\Program $program */
                        $program = $this->getOwnerRecord();
                        // Only visible if Admin AND status is KONSOLIDASI_PEMDA
                        return auth()->user()->isAdmin() &&
                               $program->status === ProgramStatus::KONSOLIDASI_PEMDA;
                    })
                    ->using(function (array $data, string $model): Model {
                        /** @var \App\Models\Program $program */
                        $program = $this->getOwnerRecord();

                        /** @var UploadedFile $file */
                        $file = $data['file_pdf'];

                        // Clean up data for service (remove file object from array if needed, though service takes it as arg)
                        // Service signature: finalizeProgram(Program $program, UploadedFile $file, array $data)

                        return app(BeritaAcaraService::class)->finalizeProgram($program, $file, $data);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // No Edit or Delete
            ]);
    }
}
