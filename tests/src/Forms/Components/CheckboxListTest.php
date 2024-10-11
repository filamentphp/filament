<?php

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Form;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render labels when enum implements HasLabel', function () {
    expect(Options::class)->toImplement(HasLabel::class);
    livewire(TestComponentWithFixedOptions::class)
        ->assertSeeText('Foo Label')
        ->assertSeeText('Bar Label');
});

it('can render descriptions when enum implements HasDescription', function () {
    expect(Options::class)->toImplement(HasDescription::class);
    livewire(TestComponentWithFixedOptions::class)
        ->assertSeeText('Foo Description')
        ->assertSeeText('Bar Description');
});

it('can render labels and descriptions for dynamic options', function () {
    expect(Options::class)->toImplement(HasLabel::class);
    expect(Options::class)->toImplement(HasDescription::class);
    livewire(TestComponentWithDynamicOptions::class)
        ->assertSeeText('Foo Label')
        ->assertSeeText('Bar Label')
        ->assertSeeText('Foo Description')
        ->assertSeeText('Bar Description');
});

enum Options: string implements HasDescription, HasLabel
{
    case FOO = 'foo';
    case BAR = 'bar';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::FOO => 'Foo Description',
            self::BAR => 'Bar Description',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FOO => 'Foo Label',
            self::BAR => 'Bar Label',
        };
    }
}

class TestComponentWithFixedOptions extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CheckboxList::make('test')->options(Options::class),
            ]);
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}

class TestComponentWithDynamicOptions extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CheckboxList::make('test')->options(function () {
                    return Options::class;
                }),
            ]);
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
