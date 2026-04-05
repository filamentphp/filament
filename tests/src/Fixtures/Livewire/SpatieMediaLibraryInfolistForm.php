<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\MediaPost;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SpatieMediaLibraryInfolistForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public MediaPost $record;

    public ?string $collection = 'avatars';

    public bool $allCollections = false;

    public function mount(MediaPost $record): void
    {
        $this->record = $record;
        $this->infolist->fill([]);
    }

    public function infolist(Schema $infolist): Schema
    {
        $entry = SpatieMediaLibraryImageEntry::make('media');

        if ($this->allCollections) {
            $entry->allCollections();
        } else {
            $entry->collection($this->collection);
        }

        return $infolist
            ->schema([$entry])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}
