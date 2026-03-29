<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard\Step;

class CreatePostWizard extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = PostResource::class;

    protected function getSteps(): array
    {
        return [
            Step::make('Details')
                ->description('Provide the basic post details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255),
                    Select::make('author_id')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
            Step::make('Content')
                ->description('Write the post content')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    MarkdownEditor::make('description')
                        ->columnSpanFull(),
                ]),
            Step::make('Publishing')
                ->description('Set visibility and status')
                ->icon('heroicon-o-eye')
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'reviewing' => 'Reviewing',
                            'published' => 'Published',
                        ])
                        ->required()
                        ->default('draft'),
                    Toggle::make('is_featured')
                        ->label('Featured post'),
                    TextInput::make('rating')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10),
                ]),
        ];
    }
}
