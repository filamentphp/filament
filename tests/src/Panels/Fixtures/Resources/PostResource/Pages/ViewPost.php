<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\CreateAction::make('createWithMetadata')
                ->record($this->getRecord())
                ->form(function (Form $form): Form {
                    $form->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('author_id')->required(),
                        Group::make([
                            TextInput::make('seo_title')->required(),
                        ])->relationship('metadata'),
                    ]);

                    $form->getFlatFields();

                    return $form;
                }),
        ];
    }

    public function refreshTitle()
    {
        $this->refreshFormData([
            'title',
        ]);
    }
}
