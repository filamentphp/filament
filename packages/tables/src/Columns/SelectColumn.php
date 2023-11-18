<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanSelectPlaceholder;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Tables\Columns\Contracts\Editable;
use Illuminate\Validation\Rule;

class SelectColumn extends Column implements Editable
{
    use CanDisableOptions;
    use CanSelectPlaceholder;
    use Concerns\CanBeValidated {
        getRules as baseGetRules;
    }
    use Concerns\CanUpdateState;
    use HasExtraInputAttributes;
    use HasOptions;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.select-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->placeholder(__('filament-forms::components.select.placeholder'));
    }

    /**
     * @return array<array-key>
     */
    public function getRules(): array
    {
        return [
            ...$this->baseGetRules(),
            Rule::in(array_keys($this->getOptions())),
        ];
    }
}
