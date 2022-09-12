<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanDisablePlaceholderSelection;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Tables\Columns\Contracts\Editable;

class ToggleColumn extends Column implements Editable
{
    use Concerns\CanBeValidated;
    use HasToggleColors;
    use HasToggleIcons;

    protected string $view = 'tables::columns.toggle-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disableClick();

        $this->rules(['file']);
    }
}
