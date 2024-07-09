<?php

namespace Filament\Pages\Concerns;

use Filament\Pages\HeadingIconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

trait HasHeadingIcon {
    public function getHeadingWithIcon(
        ?string $heading = null,
        ?string $icon = null,
        string $iconColor = 'primary',
        HeadingIconPosition $iconPosition = HeadingIconPosition::Start
    ): string | Htmlable
    {
        $color = "--c-600: var(--{$iconColor}-600);";

        $margin = ($iconPosition === HeadingIconPosition::Start)
            ? 'margin-inline-end: .5rem;' : 'margin-inline-start: .5rem;';

        $iconStyle = "{$color} {$margin} width: 1.75rem; height: 1.75rem";

        $iconComponent = filled($icon)
            ? '<x-'. $icon .' style="'. $iconStyle .'" class="inline text-custom-600" />' : null;

        $iconStart = ($iconPosition === HeadingIconPosition::Start)
            ? $iconComponent : null;

        $iconEnd = ($iconPosition === HeadingIconPosition::End)
            ? $iconComponent : null;

        $headingText = $heading ?? $this->heading ?? $this->getTitle();

        return new HtmlString(
            Blade::render('<div class="flex items-center">
                '. $iconStart .' '. $headingText .' '. $iconEnd .'
            </div>')
        );
    }
}
