<?php

namespace Filament\Pages\Actions;

use Illuminate\Database\Eloquent\Model;

class EditAction extends Action
{
    public static function make(string $name = 'edit'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/edit.single.label'));
    }
}
