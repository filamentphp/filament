<?php

use Filament\Infolists;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render icon entry with state-based URL', function (): void {
    livewire(IconEntryWithUrl::class)
        ->assertOk()
        ->assertSeeHtml('href="https://example.com/icon-link"');
});

class IconEntryWithUrl extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'icon_with_url' => 'https://example.com/icon-link',
            ])
            ->components([
                Infolists\Components\IconEntry::make('icon_with_url')
                    ->icon(Heroicon::Link)
                    ->url(fn (?string $state): ?string => $state),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}
