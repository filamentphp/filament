<?php

namespace Filament\Support\View;

use Filament\Support\Contracts\LoadingIndicator;
use Illuminate\View\ComponentAttributeBag;

class DefaultLoadingIndicator implements LoadingIndicator
{
    public function toHtml(ComponentAttributeBag $attributes): string
    {
        // The spinner is decorative — the surrounding control (button `aria-label`, loading section
        // `role="status"`) conveys the loading state — so hide it from assistive tech by default. A
        // caller can still override by passing its own `aria-hidden`.
        $attributes = $attributes
            ->merge(['aria-hidden' => 'true'], escape: false)
            ->class(['fi-loading-indicator-default']);

        return "<svg viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\" {$attributes->toHtml()}></svg>";
    }
}
