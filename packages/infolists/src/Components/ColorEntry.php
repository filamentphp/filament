<?php

namespace Filament\Infolists\Components;

use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Enums\Alignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class ColorEntry extends Entry implements HasEmbeddedView
{
    use CanBeCopied;
    use CanWrap;

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-color',
            ]);

        if (blank($state)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                            allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $state = Arr::wrap($state);

        $alignment = $this->getAlignment();

        $attributes = $attributes
            ->class([
                'fi-wrapped' => $this->canWrap(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?php
                $isCopyable = $this->isCopyable($stateItem);

                $copyableStateJs = $isCopyable
                    ? Js::from($this->getCopyableState($stateItem) ?? $stateItem)
                    : null;
                $copyMessageJs = $isCopyable
                    ? Js::from($this->getCopyMessage($stateItem))
                    : null;
                $copyMessageDurationJs = $isCopyable
                    ? Js::from($this->getCopyMessageDuration($stateItem))
                    : null;

                $tooltip = $this->getTooltip($stateItem);

                // The colour value is the swatch's only information, so expose it (or the
                // developer's tooltip text) as an accessible name on the `role="img"` swatch.
                $accessibleLabel = filled($tooltip)
                    ? ($tooltip instanceof Htmlable ? strip_tags($tooltip->toHtml()) : $tooltip)
                    : $stateItem;
                ?>

                <div <?= (new FilamentComponentAttributeBag)
                    ->merge([
                        'role' => 'img',
                        'aria-label' => e($accessibleLabel),
                        'x-on:click' => $isCopyable
                            ? <<<JS
                            window.navigator.clipboard.writeText({$copyableStateJs})
                            \$tooltip({$copyMessageJs}, {
                                theme: \$store.theme,
                                timeout: {$copyMessageDurationJs},
                            })
                            JS
                            : null,
                        'x-tooltip' => filled($tooltip)
                            ? '{
                                content: ' . Js::from($tooltip) . ',
                                theme: $store.theme,
                                allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                            }'
                            : null,
                    ], escape: false)
                    ->class([
                        'fi-in-color-item',
                        'fi-copyable' => $isCopyable,
                    ])
                    ->style([
                        'background-color: ' . e($sanitizedColor = Str::sanitizeCssColor($stateItem)) => filled($sanitizedColor),
                    ])
                    ->toHtml() ?>></div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }

    public function canWrapByDefault(): bool
    {
        return true;
    }
}
