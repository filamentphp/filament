<?php

namespace Filament\Resources\Forms\Components\Concerns;

use Filament\Resources\Forms\Form;

trait CanServeResourceSubform
{
    public function getSubform()
    {
        return Form::for($this->getLivewire())
            ->model($this->getForm()->getModel())
            ->schema($this->getSchema());
    }
}
