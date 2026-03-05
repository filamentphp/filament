<?php

namespace Filament\Tests\Fixtures\Resources\Posts\Pages;

use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Illuminate\Support\Arr;

class CreateAnotherPreservingRepeaterPost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\TextInput::make('rating')->numeric()->required(),
                Forms\Components\Select::make('author_id')
                    ->relationship('author', 'name')
                    ->required(),
                Forms\Components\Repeater::make('json_array_of_objects')
                    ->schema([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('email'),
                    ]),
            ]);
    }

    protected function preserveFormDataWhenCreatingAnother(array $data): array
    {
        return Arr::only($data, ['json_array_of_objects']);
    }
}
