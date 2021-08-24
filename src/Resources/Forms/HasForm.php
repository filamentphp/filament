<?php

namespace Filament\Resources\Forms;

use Filament\Resources\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

trait HasForm
{
    use \Filament\Forms\HasForm;

    public function getActions()
    {
        return $this->actions();
    }

    public function getForm()
    {
        return $this->form(
            Form::for($this),
        );
    }

    protected function actions()
    {
        return [];
    }

    protected function form(Form $form)
    {
        return $form;
    }

    /**
     * Store the given $file for the given $field in storage.
     */
    protected function storeFile(UploadedFile $file, FileUpload $field): string
    {
        $storeMethod = $field->getVisibility() === 'public' ? 'storePublicly' : 'store';

        if (! $field->storeAsCallback || ! ($field->getLivewire()->record)) {
            return $file->{$storeMethod}($field->getDirectory(), $field->getDiskName());
        }

        $record = $field->getLivewire()->record;
        $input = new ParameterBag($record instanceof Model ? $record->attributesToArray() : $record);

        return $file->{$storeMethod.'As'}(
            $field->getDirectory(),
            call_user_func($field->storeAsCallback, $file, $input),
            $field->getDiskName()
        );
    }
}
