<?php

namespace Filament\Support\Actions;

use Exception;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Support\Traits\Conditionable;

abstract class BaseAction extends ViewComponent
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

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $actionClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Action of class [$actionClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($actionClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use `->extraAttributes()` instead.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
