<?php

namespace Filament\Tables\Actions;

use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component;

class Action extends Component implements Htmlable
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeMounted;
    use Concerns\CanBeOutlined;
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
    use Concerns\HasTooltip;
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
        $this->view ?? $this->link();

        $this->configure();
    }

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        return $this->evaluate($this->getAction(), [
            'data' => $data,
        ]);
    }

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedTableAction';
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
