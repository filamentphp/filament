<?php

namespace Filament\Tables\Columns;

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

class ColorColumn extends Column implements HasEmbeddedView
{
    use CanBeCopied;
    use CanWrap;

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $alignment = $this->getAlignment();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-color',
                'fi-inline' => $this->isInline(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
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
                    <p class="fi-ta-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $state = Arr::wrap($state);

        $attributes = $attributes
            ->class([
                'fi-wrapped' => $this->canWrap(),
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

                $sanitizedColor = Str::sanitizeCssColor($stateItem);
                ?>

                <div <?= (new FilamentComponentAttributeBag)
                    ->merge([
                        // The swatch conveys its value purely through `background-color`, so expose the color as a
                        // named `role="img"` for screen readers. Only the sanitized colour is used, so an invalid
                        // value is never announced. The copyable swatch is an interactive control that needs
                        // separate treatment (an accessible name and keyboard operability), so it is not named here.
                        'aria-label' => ($isCopyable || blank($sanitizedColor)) ? null : e($sanitizedColor),
                        'role' => ($isCopyable || blank($sanitizedColor)) ? null : 'img',
                        'x-on:click.prevent.stop' => $isCopyable
                            ? <<<JS
                            window.navigator.clipboard.writeText({$copyableStateJs})
                            \$tooltip({$copyMessageJs}, {
                                theme: \$store.theme,
                                timeout: {$copyMessageDurationJs},
                            })
                            JS
                            : null,
                        'x-tooltip' => filled($tooltip = $this->getTooltip($stateItem))
                            ? '{
                                content: ' . Js::from($tooltip) . ',
                                theme: $store.theme,
                                allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                            }'
                            : null,
                    ], escape: false)
                    ->class([
                        'fi-ta-color-item',
                        'fi-copyable' => $isCopyable,
                    ])
                    ->style([
                        'background-color: ' . e($sanitizedColor) => filled($sanitizedColor),
                    ])
                    ->toHtml() ?>></div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
