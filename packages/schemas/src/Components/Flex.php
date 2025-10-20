<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Support\Concerns\HasFromBreakpoint;
use Filament\Support\Concerns\HasVerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;

class Flex extends Component
{
    use EntanglesStateWithSingularRelationship;
    use HasFromBreakpoint;
    use HasVerticalAlignment;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.flex';

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $schema
     */
    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }
}
