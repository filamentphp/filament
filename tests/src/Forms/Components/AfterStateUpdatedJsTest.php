<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('renders afterStateUpdatedJs with correct $wire.$watch syntax', function (): void {
    $component = livewire(AfterStateUpdatedJsTestComponent::class);

    $html = $component->html();

    expect($html)->toContain('$wire.$watch');
    expect($html)->not->toContain('$wire.watch(');
});

it('includes afterStateUpdatedJs in component x-init', function (): void {
    $component = livewire(AfterStateUpdatedJsTestComponent::class);

    $html = $component->html();

    expect($html)->toContain('x-init');
    expect($html)->toContain('$set');
    expect($html)->toContain('volume_weight');
});

class AfterStateUpdatedJsTestComponent extends Component implements \Filament\Actions\Contracts\HasActions, \Filament\Schemas\Contracts\HasSchemas
{
    use \Filament\Actions\Concerns\InteractsWithActions;
    use \Filament\Schemas\Concerns\InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('length')
                    ->numeric()
                    ->default(0)
                    ->afterStateUpdatedJs(<<<'JS'
                        const length = parseFloat($state ?? 0);
                        const breadth = parseFloat($get('breadth') ?? 0);
                        const height = parseFloat($get('height') ?? 0);
                        const weight = (length * breadth * height) / 5000;
                        $set('volume_weight', Math.round(weight * 100) / 100);
                    JS),

                TextInput::make('breadth')
                    ->numeric()
                    ->default(0)
                    ->afterStateUpdatedJs(<<<'JS'
                        const length = parseFloat($get('length') ?? 0);
                        const breadth = parseFloat($state ?? 0);
                        const height = parseFloat($get('height') ?? 0);
                        const weight = (length * breadth * height) / 5000;
                        $set('volume_weight', Math.round(weight * 100) / 100);
                    JS),

                TextInput::make('height')
                    ->numeric()
                    ->default(0)
                    ->afterStateUpdatedJs(<<<'JS'
                        const length = parseFloat($get('length') ?? 0);
                        const breadth = parseFloat($get('breadth') ?? 0);
                        const height = parseFloat($state ?? 0);
                        const weight = (length * breadth * height) / 5000;
                        $set('volume_weight', Math.round(weight * 100) / 100);
                    JS),

                TextInput::make('volume_weight')
                    ->readOnly()
                    ->default(0),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
