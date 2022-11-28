<?php

namespace Filament\Actions;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Support\Traits\Conditionable;

abstract class StaticAction extends ViewComponent
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\HasColor;
    use Concerns\HasIcon;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasSize;
    use Conditionable;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'action';

    protected string $viewIdentifier = 'action';

    final public function __construct(?string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use `->extraAttributes()` instead.
     *
     * @param array<mixed> $attributes
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
