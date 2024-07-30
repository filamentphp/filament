<?php

namespace Filament\Tests\Panels\Fixtures\Resources\PostResource\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;

class CreatePostUsingWizard extends CreateRecord
{
    use HasWizard;

    protected static string $resource = PostResource::class;

    public function getSteps(): array
    {
        return [
            Step::make('Step 1')
                ->schema([
                    TextInput::make('title')->required(),
                ]),
            Step::make('Step 2')
                ->schema([
                    TextInput::make('content')->required(),
                ]),
        ];
    }
}
