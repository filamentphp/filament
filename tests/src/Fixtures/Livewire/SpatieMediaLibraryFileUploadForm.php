<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\MediaPost;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class SpatieMediaLibraryFileUploadForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithFileUploads;

    public $data = [];

    public MediaPost $record;

    public function mount(MediaPost $record): void
    {
        $this->record = $record;
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->collection('avatars'),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
        $this->form->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
