<?php

namespace Filament\Traits\FieldConcerns;

trait CanHaveSizeConstraints
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
