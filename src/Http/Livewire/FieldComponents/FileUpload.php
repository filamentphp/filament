<?php

namespace Filament\Http\Livewire\FieldComponents;

use Filament\Traits\WithNotifications;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;
    use WithNotifications;

    public $fieldName;

    public $file;

    public $files = [];

    public $multiple = false;

    public function delete()
    {
        if ($this->multiple) {

            return;
        }

        $this->emitUp('deleteFile', $this->fieldName);

        $this->reset('file');

        $this->notify(__('filament::avatar.delete', ['name' => $this->user->name]));
    }

    public function mount($fieldName, $multiple)
    {
        $this->fieldName = $fieldName;

        $this->multiple = $multiple;
    }

    public function sort()
    {

    }
}
