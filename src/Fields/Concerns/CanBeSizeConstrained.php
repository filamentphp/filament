<?php

namespace Filament\Fields\Concerns;

trait CanBeSizeConstrained
{
    public function maxSize($size)
    {
        $this->addRules([$this->name => ["max:$size"]]);

        return $this;
    }

    public function minSize($size)
    {
        $this->addRules([$this->name => ["min:$size"]]);

        return $this;
    }
}
