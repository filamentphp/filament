<?php

namespace Filament\Tables\BulkActions;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class BulkAction
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeRun;
    use Concerns\HasFormSchema;
    use Concerns\HasIcon;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Conditionable;
    use Macroable;
    use Tappable;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = new static($name);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
    }
}
