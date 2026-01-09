<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithImageEntry::class)
        ->assertSuccessful()
        ->assertSeeHtml('src="https://example.com/image.jpg"');
});

it('can render with circular style', function (): void {
    livewire(TestComponentWithCircularImageEntry::class)
        ->assertSuccessful()
        ->assertSeeHtml('src="https://example.com/avatar.jpg"');
});

class TestComponentWithImageEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'image' => 'https://example.com/image.jpg',
            ])
            ->components([
                ImageEntry::make('image'),
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

class TestComponentWithCircularImageEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'avatar' => 'https://example.com/avatar.jpg',
            ])
            ->components([
                ImageEntry::make('avatar')
                    ->circular(),
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
