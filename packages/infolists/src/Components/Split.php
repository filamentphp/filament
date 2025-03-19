<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Concerns\HasFromBreakpoint;
use Filament\Support\Concerns\HasGap;
use Filament\Support\Concerns\HasHorizontalAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;

class Split extends Component
{
    use HasFromBreakpoint;
    use HasGap;
    use HasVerticalAlignment;
    use HasHorizontalAlignment;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.split';

    /**
     * @param  array<Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component> | Closure  $schema
     */
    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }
}
