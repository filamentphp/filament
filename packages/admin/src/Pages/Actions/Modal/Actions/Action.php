<?php

namespace Filament\Pages\Actions\Modal\Actions;

use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component;

class Action extends Component implements Htmlable
{
    use Concerns\CanCancelAction;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Concerns\HasColor;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasView;
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
        $this->button();

        $this->configure();
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            'action' => $this,
        ]));
    }
}
