<?php

namespace Filament\Tables\Actions;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class Action
{
    use Concerns\BelongsToTable;
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasRecord;
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
