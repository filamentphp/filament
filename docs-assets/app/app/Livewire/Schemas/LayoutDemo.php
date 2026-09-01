<?php

namespace App\Livewire\Schemas;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class LayoutDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public string $component = 'section';

    public function mount(string $component = 'section'): void
    {
        $this->component = $component;

        $this->form->fill();
    }

    protected function resolveSchemaClass(): string
    {
        return 'App\\Livewire\\Schemas\\Layout\\' . Str::studly($this->component) . 'Schema';
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
        return view('livewire.schema.layout');
    }
}
