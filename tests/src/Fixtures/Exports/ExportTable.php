<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class ExportTable extends ExportActions implements HasTable
{
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([TextColumn::make('title')->searchable()->sortable()])
            ->filters([Filter::make('published')->query(static fn (Builder $query): Builder => $query->where('is_published', true))])
            ->headerActions([$this->exportAction()])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(ActionPostExporter::class)
                    ->fileDisk('local')
                    ->formats([ExportFormat::Csv]),
            ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->table }}<x-filament-actions::modals /></div>';
    }
}
