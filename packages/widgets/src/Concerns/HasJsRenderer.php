<?php

namespace Filament\Widgets\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\RawJs;
use Illuminate\Contracts\View\View;

trait HasJsRenderer /** @phpstan-ignore trait.unused */
{
    #[Unexposed]
    abstract public function getRenderer(): string | RawJs | null;

    /** @return array<string, mixed> */
    #[Unexposed]
    public function getRendererConfiguration(): array
    {
        return [];
    }

    #[Unexposed]
    public function render(): View
    {
        return view($this->view ?? 'filament-widgets::js-widget', $this->getViewData());
    }
}
