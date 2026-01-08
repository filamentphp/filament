<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithCodeEditor::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithCodeEditor::class)
        ->fillForm(['code' => '<?php echo "Hello";'])
        ->assertSchemaStateSet(['code' => '<?php echo "Hello";']);
});

it('can render with PHP language', function (): void {
    livewire(TestComponentWithPhpCodeEditor::class)
        ->assertSuccessful();
});

it('can render with JavaScript language', function (): void {
    livewire(TestComponentWithJsCodeEditor::class)
        ->assertSuccessful();
});

class TestComponentWithCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithPhpCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')->language(Language::Php),
            ])
            ->statePath('data');
    }
}

class TestComponentWithJsCodeEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')->language(Language::JavaScript),
            ])
            ->statePath('data');
    }
}
