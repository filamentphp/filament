<?php

namespace Filament\Tables\Columns;

class View extends Column
{
    public $viewData = [];

    public function data($data)
    {
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    protected function getViewData()
    {
        return $this->viewData;
    }
}
