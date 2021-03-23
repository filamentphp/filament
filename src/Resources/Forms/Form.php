<?php

namespace Filament\Resources\Forms;

class Form extends \Filament\Forms\Form
{
    protected $hasWrapper = true;

    protected $model;

    public function getModel()
    {
        return $this->model ?? $this->getParent()->getModel();
    }

    public function getSchema()
    {
        $schema = parent::getSchema();

        if (! $this->hasWrapper) {
            return collect($schema)
                ->first()
                ->getSchema();
        }

        return $schema;
    }

    public function hasWrapper()
    {
        return $this->hasWrapper;
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    public function schema($schema)
    {
        if (! is_array($schema)) {
            $schema = [$schema];

            $this->withoutWrapper();
        }

        return parent::schema($schema);
    }

    public function withoutWrapper()
    {
        $this->hasWrapper = false;

        return $this;
    }
}
