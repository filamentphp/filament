<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithCodeEntry::class)
        ->assertSuccessful()
        ->assertSeeText('echo "Hello World"');
});

it('can render with grammar highlighting', function (): void {
    livewire(TestComponentWithPhpCodeEntry::class)
        ->assertSuccessful();
});

class TestComponentWithCodeEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'code' => 'echo "Hello World"',
            ])
            ->components([
                CodeEntry::make('code'),
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

class TestComponentWithPhpCodeEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'php_code' => '<?php echo "Hello"; ?>',
            ])
            ->components([
                CodeEntry::make('php_code')
                    ->grammar(\Phiki\Grammar\Grammar::Php),
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
