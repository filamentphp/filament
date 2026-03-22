<?php

namespace App\Livewire\Forms;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class FieldsDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public string $component = 'field';

    public function mount(string $component = 'field'): void
    {
        $this->component = $component;

        $this->form->fill();

        $schemaClass = $this->resolveSchemaClass();

        if (method_exists($schemaClass, 'afterMount')) {
            $schemaClass::afterMount($this);
        }
    }

    protected function resolveSchemaClass(): string
    {
        return 'App\\Livewire\\Forms\\Fields\\' . Str::studly($this->component) . 'Schema';
    }

    public function form(Schema $form): Schema
    {
        $schemaClass = $this->resolveSchemaClass();

        return $form
            ->statePath('data')
            ->components($schemaClass::schema());
    }

    public function render()
    {
        return view('livewire.forms.fields');
    }
}
