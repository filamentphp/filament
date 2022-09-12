<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Tables\Columns\Contracts\Editable;

class CheckboxColumn extends Column implements Editable
{
    use Concerns\CanBeValidated;
    use HasExtraInputAttributes;

    protected string $view = 'tables::columns.checkbox-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disableClick();

        $this->rules(['boolean']);
    }
}
