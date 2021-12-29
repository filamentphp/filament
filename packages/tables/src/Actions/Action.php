<?php

namespace Filament\Tables\Actions;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component;

class Action extends Component implements Htmlable
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeMounted;
    use Concerns\CanOpenModal;
    use Concerns\CanOpenUrl;
    use Concerns\CanRequireConfirmation;
    use Concerns\EvaluatesClosures;
    use Concerns\HasAction;
    use Concerns\HasColor;
    use Concerns\HasFormSchema;
    use Concerns\HasIcon;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasRecord;
    use Concerns\HasView;
    use Conditionable;
    use Macroable;
    use Tappable;

    protected bool | Closure $isHidden = false;

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

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        $action = $this->getAction();

        if (! $action) {
            return;
        }

        return app()->call($action, [
            'data' => $data,
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ]);
    }

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        if (! $this->isHidden instanceof Closure) {
            return $this->isHidden;
        }

        if (! $this->getRecord()) {
            return false;
        }

        return app()->call($this->isHidden, [
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ]);
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
