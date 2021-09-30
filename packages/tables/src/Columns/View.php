<?php

namespace Filament\Tables\Columns;

class View extends Column
{
    public function data($data = [])
    {
        $this->viewData($data);

        return $this;
    }
}
