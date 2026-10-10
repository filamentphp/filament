<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Support\HtmlString;

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
                    ->label('Title')
                    ->url('/posts')
                    ->extraEntryWrapperAttributes(['data-testid' => 'linked-entry']),
                TextEntry::make('content')
                    ->label('Content'),
                TextEntry::make('enum_label')
                    ->state(NavigationGroupEnum::Users)
                    ->prose()
                    ->extraAttributes(['data-testid' => 'enum-label']),
                TextEntry::make('trusted_label')
                    ->state(new class implements HasLabel
                    {
                        public function getLabel(): HtmlString
                        {
                            return new HtmlString('<strong>Alpha beta</strong>');
                        }
                    })
                    ->limit(100)
                    ->words(100)
                    ->prefix('Label: ')
                    ->suffix(' (label)')
                    ->extraAttributes(['data-testid' => 'trusted-label']),
                TextEntry::make('rating')
                    ->label('Rating')
                    ->badge(),
                TextEntry::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),
                CodeEntry::make('code')
                    ->state(['enabled' => true, 'retries' => 3])
                    ->copyable(static fn (string $state): bool => str_contains($state, 'enabled'))
                    ->extraAttributes(['data-testid' => 'copyable-code']),
                CodeEntry::make('custom_copy_code')
                    ->state(['enabled' => true, 'retries' => 3])
                    ->copyable()
                    ->copyableState(static fn (string $state): string => "Copy: {$state}")
                    ->extraAttributes(['data-testid' => 'custom-copy-code', 'x-data' => '{}']),
                CodeEntry::make('id_scoped_code')
                    ->state(['enabled' => true, 'retries' => 3])
                    ->copyable()
                    ->extraAttributes(['data-testid' => 'id-scoped-code', 'x-id' => "['custom-description']"]),
                IconEntry::make('is_published')
                    ->label('Published')
                    ->size('lg'),
                IconEntry::make('title')
                    ->label('Custom icon')
                    ->icon(new HtmlString('<svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" fill="currentColor" /></svg>')),
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
