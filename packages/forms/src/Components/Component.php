<?php

namespace Filament\Forms\Components;

use Filament\Forms\Concerns\HasColumns;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as ViewComponent;

class Component extends ViewComponent implements Htmlable
{
    use Concerns\BelongsToContainer;
    use Concerns\BelongsToModel;
    use Concerns\CanBeConcealed;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\EvaluatesClosures;
    use Concerns\HasChildComponents;
    use Concerns\HasExtraAttributes;
    use Concerns\HasFieldWrapper;
    use Concerns\HasInlineLabel;
    use Concerns\HasId;
    use Concerns\HasLabel;
    use Concerns\HasMaxWidth;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use Concerns\HasView;
    use Concerns\ListensToEvents;
    use Configurable;
    use HasColumns;
    use HasStateBindingModifiers;
    use Macroable;
    use Tappable;

    protected function setUp(): void
    {
        $this->configure();
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            'component' => $this,
        ]));
    }
}
