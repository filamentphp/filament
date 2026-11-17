<?php

namespace Filament\Support\View\Concerns;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Width;
use Filament\Support\View\ComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\is_slot_empty;

trait CanGenerateDropdownHtml
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    public function generateDropdownHtml(
        string | Htmlable $slot = '',
        ?BaseComponentAttributeBag $attributes = null,
        string | int | float | null $availableHeight = null,
        string | int | float | null $availableWidth = null,
        bool $flip = true,
        string | int | null $maxHeight = null,
        bool $menu = true,
        int | float $offset = 8,
        ?string $placement = null,
        bool $shift = false,
        bool $size = false,
        int $sizePadding = 16,
        bool $teleport = false,
        ?ComponentSlot $trigger = null,
        Width | string | null $width = null,
    ): string {
        $attributes ??= new ComponentAttributeBag;
        $slot = $slot instanceof ComponentSlot ? $slot : new ComponentSlot(e($slot));

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.dropdown.index')) {
            return view('filament::components.dropdown.index', get_defined_vars())->render();
        }

        $sizeConfig = collect(['availableHeight' => $availableHeight, 'availableWidth' => $availableWidth, 'padding' => $sizePadding])->filter()->toJson();
        $width = is_string($width) ? (Width::tryFrom($width) ?? $width) : $width;
        $floatDirective = 'x-float' . ($placement ? ".placement.{$placement}" : '') . ($size ? '.size' : '') . ($flip ? '.flip' : '') . ($shift ? '.shift' : '') . ($teleport ? '.teleport' : '') . ($offset ? '.offset' : '');
        $panelAttributes = (new ComponentAttributeBag)->class([
            'fi-dropdown-panel',
            $width instanceof Width ? "fi-width-{$width->value}" : ($width ?? ''),
            'fi-scrollable' => $maxHeight || $size,
        ]);

        ob_start(); ?>
        <div x-data="filamentDropdown({ isMenu: <?= e(Js::from($menu)) ?> })" <?= $attributes->class(['fi-dropdown']) ?>>
            <div x-on:mousedown="$event.button === 0 && toggle($event)" <?= ($trigger->attributes ?? new ComponentAttributeBag)->class(['fi-dropdown-trigger']) ?>><?= e($trigger) ?></div>
            <?php if (! is_slot_empty($slot)) { ?>
                <div x-cloak <?= e($floatDirective) ?>="{ offset: <?= e($offset) ?>, <?= e($size ? ('size: ' . $sizeConfig) : '') ?> }" x-ref="panel" x-transition:enter-start="fi-opacity-0" x-transition:leave-end="fi-opacity-0"
                    <?php if ($menu) { ?> role="menu" <?php } ?>
                    <?php if ($attributes->has('wire:key')) { ?> wire:ignore.self wire:key="<?= e($attributes->get('wire:key')) ?>.panel" <?php } ?>
                    <?= $panelAttributes ?> style="<?= $maxHeight ? 'max-height: ' . e($maxHeight) . ';' : '' ?>"
                ><?= e($slot) ?></div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }
}
