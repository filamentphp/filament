<?php

namespace Filament\Support\Actions;

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

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
