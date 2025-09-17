<?php

use Filament\Infolists;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can evaluate livewire closure dependency by name', function (): void {
    livewire(LivewireInfolists::class)
        ->assertOk()
        ->assertSee('First Entry Label')
        ->assertSee('First Entry State')
        ->assertSee('Second Entry Label')
        ->assertSee('Second Entry State')
        ->assertSee('Third Entry Label')
        ->assertSee('Third Entry State (dynamic)');
});

class LivewireInfolists extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public $data;

    public static function make(): static
    {
        return new static;
    }

    public function mount(): void
    {
        $this->data([
            'first_entry' => 'First Entry State',
            'second_entry' => 'Second Entry State',
        ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state($this->data)
            ->components(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('first_entry')
                        ->label('First Entry Label'),
                ];
            });
    }

    public function infolistWithCustomName(Schema $schema): Schema
    {
        return $schema
            ->state($this->data)
            ->components(function (self $livewire) {
                expect($livewire)->toBe($this);

                return [
                    Infolists\Components\TextEntry::make('second_entry')
                        ->label('Second Entry Label'),
                    Infolists\Components\TextEntry::make('third_entry')
                        ->label('Third Entry Label')
                        ->state(function (self $livewire) {
                            expect($livewire)->toBe($this);

                            return 'Third Entry State (dynamic)';
                        }),
                ];
            });
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render(): string
    {
        return <<<'BLADE'
		{{ $this->infolist }}
		{{ $this->infolistWithCustomName }}
		BLADE;
    }
}
