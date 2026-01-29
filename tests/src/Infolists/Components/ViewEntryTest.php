<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithViewEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Custom:')
        ->assertSeeText('Custom Content');
});

class TestComponentWithViewEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'custom' => 'Custom Content',
            ])
            ->components([
                ViewEntry::make('custom')
                    ->view('filament.infolists.entries.custom-entry'),
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
