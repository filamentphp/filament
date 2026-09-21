<?php

namespace Filament\Widgets\Concerns;

use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;

trait HasJsRenderer /** @phpstan-ignore trait.unused */
{
    abstract public function getRenderer(): string | RawJs | null;

    /** @return array<string, mixed> */
    public function getRendererConfiguration(): array
    {
        return [];
    }

    public function render(): View
    {
        return view('filament-widgets::js-widget', $this->getViewData());
    }
}
