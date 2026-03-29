<?php

namespace App\Livewire\Forms;

use App\Models\Post;
use App\Tables\TagsTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\ModalTableSelect;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class ModalTableSelectDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?Post $record = null;

    public $data = [];

    public function mount(): void
    {
        $this->record = Post::with('tags')->first();

        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->model($this->record)
            ->statePath('data')
            ->components([
                Group::make()
                    ->id('modalTableSelect')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ModalTableSelect::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->tableConfiguration(TagsTable::class),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.overview');
    }
}
