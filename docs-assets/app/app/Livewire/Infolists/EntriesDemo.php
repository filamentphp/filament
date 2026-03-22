<?php

namespace App\Livewire\Infolists;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class EntriesDemo extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public string $component = 'entry';

    public function mount(string $component = 'entry'): void
    {
        $this->component = $component;
    }

    protected function resolveSchemaClass(): string
    {
        return 'App\\Livewire\\Infolists\\Entries\\' . Str::studly($this->component) . 'Schema';
    }

    public function infolist(Schema $infolist): Schema
    {
        $schemaClass = $this->resolveSchemaClass();

        return $infolist
            ->components($schemaClass::schema());
    }

    public function render()
    {
        return view('livewire.infolists.entries');
    }
}
