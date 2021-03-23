<?php

namespace Filament\Resources\Forms\Components\Concerns;

use Filament\Resources\Forms\Form;

trait CanServeResourceSubform
{
    public function getSubform()
    {
        return Form::extend($this)->schema($this->getSchema());
    }
}
