<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Article;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SpatieTagsInputForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Article $record;

    public ?string $tagType = null;

    public bool $useNullType = false;

    public function mount(Article $record): void
    {
        $this->record = $record;
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        $component = SpatieTagsInput::make('tags');

        if ($this->useNullType) {
            $component->type(null);
        } elseif ($this->tagType !== null) {
            $component->type($this->tagType);
        }

        return $form
            ->schema([$component])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
        $this->form->saveRelationships();
    }

    public function saveOnly(): void
    {
        $this->record->load('tags');
        $this->form->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
