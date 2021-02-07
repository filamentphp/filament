<?php

namespace Filament\Fields;

use Filament\Traits\FieldConcerns;
use Illuminate\Support\Str;

class Tabs extends Field
{
    use FieldConcerns\CanHaveId;
    use FieldConcerns\CanHaveLabel;

    public static function make($label = null)
    {
        $tabs = (new static())->label($label);

        if ($label) $tabs = $tabs->id((string) Str::of($label)->slug());

        return $tabs;
    }

    public function tabs($tabs)
    {
        $this->fields($tabs);

        return $this;
    }
}
