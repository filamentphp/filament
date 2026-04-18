<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\Post;

class InfolistEntriesBrowserTest extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?int $navigationSort = 21;

    protected static bool $shouldRegisterNavigation = false;

    public ?Post $record = null;

    public function mount(): void
    {
        $this->record = Post::factory()->create();
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->components([
                TextEntry::make('title')
                    ->label('Title'),
                TextEntry::make('content')
                    ->label('Content'),
                TextEntry::make('rating')
                    ->label('Rating')
                    ->badge(),
                TextEntry::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),
                IconEntry::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedSchema::make('infolist'),
            ]);
    }
}
