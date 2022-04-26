<?php

namespace Filament\Tables\Filters;

use Filament\Support\Concerns\Configurable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class Filter
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\EvaluatesClosures;
    use Concerns\HasDefaultState;
    use Concerns\HasFormSchema;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;
    use Configurable;
    use Macroable;
    use Tappable;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        $this->configure();
    }
}
