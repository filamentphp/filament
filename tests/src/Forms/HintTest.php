<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('has a form with the default name \'form\'', function (): void {
    livewire(TestComponentWithFormWithTestFieldHint::class)
        ->assertFormExists();
});

class TestFieldWithChildComponentSchema extends Field
{
    protected string $view = 'forms.test-component-with-form';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->afterStateHydrated(function (self $component, mixed $state): void {
                if (! $state) {
                    $state = ['partA' => 'defaultA', 'partB' => 'defaultB'];
                }

                if (is_string($state)) {
                    [$partA, $partB] = explode('-', $state, 2);

                    $state = [
                        'partA' => $partA,
                        'partB' => $partB,
                    ];
                }

                $component->state($state);
            })
            ->mutateDehydratedStateUsing(function (array $state): ?string {
                if (! $state['partA'] || ! $state['partB']) {
                    return null;
                }

                return $state['partA'] . '-' . $state['partB'];
            })
            ->schema([
                TextInput::make('partA'),
                TextInput::make('partB'),
            ])
            ->hint(function (array $state) {
                // Must be an array as the `afterStateHydrated()` ensures it is always an array, whilst it is filled as string.
                return "{$state['partA']} / {$state['partB']}";
            });
    }
}

class TestComponentWithFormWithTestFieldHint extends Livewire
{
    public function mount(): void
    {
        $this->form->fill([
            'schema' => 'a-b',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TestFieldWithChildComponentSchema::make('schema')
                    ->hintIcon(fn (array $state) => 'heroicon-o-information-circle'),
            ])
            ->statePath('data');
    }
}
