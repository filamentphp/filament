<?php

namespace Filament\Support\Components;

use Exception;
use Filament\Support\Concerns\Configurable;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as BaseComponent;

abstract class ViewComponent extends BaseComponent implements Htmlable
{
    use Configurable;
    use EvaluatesClosures;
    use Macroable;
    use Tappable;

    protected string $view;

    protected string $viewIdentifier;

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        if (! isset($this->view)) {
            throw new Exception('Class [' . static::class . '] extends [' . ViewComponent::class . '] but does not have a [$view] property defined.');
        }

        return $this->view;
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view(
            $this->getView(),
            array_merge(
                $this->data(),
                isset($this->viewIdentifier) ? [$this->viewIdentifier => $this] : [],
            ),
        );
    }
}
