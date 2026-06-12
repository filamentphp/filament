<?php

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('sanitizes stored HTML when rendering the disabled state to prevent stored XSS', function () {
    livewire(TestComponentWithDisabledRichEditor::class)
        ->fillForm([
            'content' => '<p>Safe paragraph</p><img src=x onerror="window.__xss = true">',
        ])
        ->assertDontSeeHtml('onerror="window.__xss = true"');
});

class TestComponentWithDisabledRichEditor extends Livewire
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->disabled(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
