<?php

namespace Filament\Resources\Forms;

class Form extends \Filament\Forms\Form
{
    protected $model;

    public function getModel()
    {
        return $this->model;
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }
}
