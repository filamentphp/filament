<?php

namespace Filament\Support\Actions\Modal\Actions;

use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\HasColor;
use Filament\Support\Actions\Concerns\HasIcon;
use Filament\Support\Actions\Concerns\HasLabel;
use Filament\Support\Actions\Concerns\HasName;
use Filament\Support\Actions\Concerns\HasView;
use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component;

abstract class Action extends Component implements Htmlable
{
    use CanBeOutlined;
    use Concerns\CanCancelAction;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Conditionable;
    use Configurable;
    use HasColor;
    use HasLabel;
    use HasIcon;
    use HasName;
    use HasView;
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
