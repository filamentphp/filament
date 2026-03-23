<?php

namespace App\Livewire;

use App\Filament\Exports\PostExporter;
use App\Filament\Imports\PostImporter;
use App\Models\Post;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class ActionsCrudDemo extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::withTrashed()->with('author'))
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('author.name'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->model(Post::class)
                    ->form(static::getPostForm()),
                ImportAction::make()
                    ->importer(PostImporter::class),
                ExportAction::make()
                    ->exporter(PostExporter::class),
            ])
            ->actions([
                ViewAction::make()
                    ->form(static::getPostForm()),
                EditAction::make()
                    ->form(static::getPostForm()),
                ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->form([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                    ]),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ]);
    }

    protected static function getPostForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->required()
                ->maxLength(255),
            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'reviewing' => 'Reviewing',
                    'published' => 'Published',
                ])
                ->required(),
            Toggle::make('is_featured'),
            Textarea::make('description')
                ->rows(3),
        ];
    }

    public function render()
    {
        return view('livewire.actions-crud-demo');
    }
}
