<?php

namespace Filament\Forms\Components\Concerns;

trait CanAlternateToColumnLayout
{
    protected $columnLayout = false;

    public function columnLayout()
    {
        $this->configure(function () {
            $this->columnLayout = true;
        });

        return $this;
    }

    public function isColumnLayout()
    {
        return $this->columnLayout;
    }
}
