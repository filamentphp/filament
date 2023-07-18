<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Tables\Columns\Contracts\Editable;

class ToggleColumn extends Column implements Editable
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasToggleColors;
    use HasToggleIcons;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.toggle-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->rules(['boolean']);
    }
}
