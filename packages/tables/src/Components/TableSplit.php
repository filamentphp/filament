<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Component;
use Filament\Support\Concerns\HasFromBreakpoint;

/**
 * Lays table parts out in a row, like a `Split` column layout: every part grows by
 * default, and `grow(false)` shrinks one to its content, so the parts around it take the space.
 */
class TableSplit extends TableStack
{
    use HasFromBreakpoint;

    public function toEmbeddedHtml(): string
    {
        $attributes = $this->getExtraAttributeBag()
            ->merge(['id' => $this->getId()], escape: false)
            ->class([
                'fi-ta-split fi-ta-layout-split',
                filled($fromBreakpoint = $this->getFromBreakpoint()) ? "{$fromBreakpoint}:fi-ta-split" : 'default:fi-ta-split',
            ]);

        $html = '';

        Component::withVisibilityCache(function () use (&$html): void {
            foreach ($this->getChildSchema()->getComponents(withHidden: true) as $component) {
                if (! $component->isVisible()) {
                    continue;
                }

                $componentHtml = $component->toHtml();

                // A part that renders nothing must not leave an empty cell behind, and Livewire's block markers are not content.
                if (blank(trim(preg_replace('/<!--.*?-->/s', '', $componentHtml) ?? ''))) {
                    continue;
                }

                $html .= ($component->canGrow() ? '<div class="fi-growable">' : '<div>') . $componentHtml . '</div>';
            }
        });

        return "<div {$attributes->toHtml()}>{$html}</div>";
    }
}
