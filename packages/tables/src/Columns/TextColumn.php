<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasLineClamp;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Contracts\HasIcon as HasIconInterface;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use stdClass;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;

class TextColumn extends Column implements HasEmbeddedView
{
    use CanBeCopied;
    use CanWrap;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasDescription;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use HasFontFamily;
    use HasLineClamp;
    use HasWeight;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    protected TextSize | string | Closure | null $size = null;

    protected bool | Closure $isLimitedListExpandable = false;

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    public function limitList(int | Closure | null $limit = 3): static
    {
        $this->listLimit = $limit;

        return $this;
    }

    public function rowIndex(bool $isFromZero = false): static
    {
        $this->state(static function (HasTable $livewire, stdClass $rowLoop) use ($isFromZero): string {
            $rowIndex = $rowLoop->{$isFromZero ? 'index' : 'iteration'};

            $recordsPerPage = $livewire->getTableRecordsPerPage();

            if (! is_numeric($recordsPerPage)) {
                return (string) $rowIndex;
            }

            return (string) ($rowIndex + ($recordsPerPage * ($livewire->getTablePage() - 1)));
        });

        return $this;
    }

    public function size(TextSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextSize | string
    {
        $size = $this->evaluate($this->size, [
            'state' => $state,
        ]);

        if (blank($size)) {
            return TextSize::Small;
        }

        if (is_string($size)) {
            $size = TextSize::tryFrom($size) ?? $size;
        }

        if ($size === 'base') {
            return TextSize::Medium;
        }

        return $size;
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }

    public function isListWithLineBreaks(): bool
    {
        return $this->evaluate($this->isListWithLineBreaks) || $this->isBulleted();
    }

    public function getListLimit(): ?int
    {
        return $this->evaluate($this->listLimit);
    }

    public function expandableLimitedList(bool | Closure $condition = true): static
    {
        $this->isLimitedListExpandable = $condition;

        return $this;
    }

    public function isLimitedListExpandable(): bool
    {
        return (bool) $this->evaluate($this->isLimitedListExpandable);
    }

    public function hasBulleted(): bool
    {
        return $this->isBulleted !== false;
    }

    public function hasListWithLineBreaks(): bool
    {
        return $this->isListWithLineBreaks !== false;
    }

    public function hasSize(): bool
    {
        return $this->size !== null;
    }

    /**
     * When adding a new property that affects the rendered cell HTML, add
     * a `has*()` predicate to the trait that owns the property and reference
     * it here.
     */
    protected function canRenderOptimized(mixed $state): bool
    {
        if (
            is_array($state) ||
            $state instanceof Collection ||
            $state instanceof Htmlable ||
            $state instanceof HasIconInterface
        ) {
            return false;
        }

        if (blank($state)) {
            return false;
        }

        return ! $this->hasBulleted()
            && ! $this->hasListWithLineBreaks()
            && ! $this->hasIcon()
            && ! $this->hasTooltip()
            && ! $this->hasCopyable()
            && ! $this->hasWeight()
            && ! $this->hasFontFamily()
            && ! $this->hasLineClamp()
            && ! $this->hasSize()
            && ! $this->hasDescription()
            && ! $this->hasWrap()
            && ! $this->hasExtraAttributes();
    }

    protected function toOptimizedHtml(mixed $state): string
    {
        $formattedState = e($this->formatState($state));

        $url = $this->getUrl($state);

        if (filled($url)) {
            $formattedState = '<a ' . generate_href_html($url, $this->shouldOpenUrlInNewTab())->toHtml() . '>' . $formattedState . '</a>';
        }

        $isBadge = $this->isBadge();
        $color = $this->getColor($state);

        if ($isBadge) {
            $badgeColor = filled($color) ? $color : 'primary';

            if (is_array($badgeColor)) {
                $badgeStyle = implode('; ', FilamentColor::getComponentCustomStyles(BadgeComponent::class, $badgeColor));
                $formattedState = '<span class="fi-badge fi-size-sm fi-color" style="' . $badgeStyle . '">' . $formattedState . '</span>';
            } else {
                $badgeColorClasses = implode(' ', FilamentColor::getComponentClasses(BadgeComponent::class, $badgeColor));
                $formattedState = '<span class="fi-badge fi-size-sm ' . $badgeColorClasses . '">' . $formattedState . '</span>';
            }
        }

        $classString = $isBadge
            ? 'fi-ta-text fi-ta-text-item fi-ta-text-has-badges'
            : 'fi-ta-text fi-ta-text-item fi-size-sm';

        $styleString = '';

        if ((! $isBadge) && filled($color)) {
            if (is_array($color)) {
                $classString .= ' fi-color';
                $styleString = ' style="' . implode('; ', FilamentColor::getComponentCustomStyles(ItemComponent::class, $color)) . '"';
            } else {
                $classString .= ' ' . implode(' ', FilamentColor::getComponentClasses(ItemComponent::class, $color));
            }
        }

        if ($this->isInline()) {
            $classString .= ' fi-inline';
        }

        $alignment = $this->getAlignment();

        if ($alignment instanceof Alignment) {
            $classString .= " fi-align-{$alignment->value}";
        } elseif (is_string($alignment) && $alignment !== '') {
            $classString .= ' ' . e($alignment);
        }

        return '<div class="' . $classString . '"' . $styleString . '>' . $formattedState . '</div>';
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($this->canRenderOptimized($state)) {
            return $this->toOptimizedHtml($state);
        }

        $isBadge = $this->isBadge();
        $isListWithLineBreaks = $this->isListWithLineBreaks();
        $isLimitedListExpandable = $this->isLimitedListExpandable();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-text',
                'fi-inline' => $this->isInline(),
            ]);

        $alignment = $this->getAlignment();

        $attributes = $attributes
            ->class([
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        if (blank($state instanceof Htmlable ? $state->toHtml() : $state)) {
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

        $shouldOpenUrlInNewTab = $this->shouldOpenUrlInNewTab();

        $formatState = function (mixed $stateItem, mixed $formattedState = null) use ($shouldOpenUrlInNewTab): string {
            $url = $this->getUrl($stateItem);

            $item = '';

            if (filled($url)) {
                $item .= '<a ' . generate_href_html($url, $shouldOpenUrlInNewTab)->toHtml() . '>';
            }

            $item .= e($formattedState ?? $this->formatState($stateItem));

            if (filled($url)) {
                $item .= '</a>';
            }

            return $item;
        };

        /** @var array<mixed> $state */
        $state = Arr::wrap($state);

        $stateCount = count($state);

        $listLimit = $this->getListLimit() ?? $stateCount;
        $stateOverListLimitCount = 0;

        if ($listLimit && ($stateCount > $listLimit)) {
            $stateOverListLimitCount = $stateCount - $listLimit;

            if (
                (! $isListWithLineBreaks) ||
                (! $isLimitedListExpandable)
            ) {
                $state = array_slice($state, 0, $listLimit);
            }
        }

        $isCollapsedList = false;

        if (($stateCount > 1) && (! $isListWithLineBreaks) && (! $isBadge)) {
            $state = [
                implode(
                    ', ',
                    array_map(
                        fn (mixed $stateItem): string => $formatState($stateItem),
                        $state,
                    ),
                ),
            ];

            $stateCount = 1;
            $formatState = fn (mixed $stateItem, mixed $formattedState = null): string => $stateItem;
            $isCollapsedList = true;
        }

        $attributes = $attributes
            ->class([
                'fi-ta-text-has-badges' => $isBadge,
                'fi-wrapped' => $this->canWrap(),
            ]);

        $lineClamp = $this->getLineClamp();
        $iconPosition = $this->getIconPosition();
        $isBulleted = $this->isBulleted();

        $getStateItem = function (mixed $stateItem, mixed $formattedState = null) use ($iconPosition, $isBadge, $lineClamp): array {
            $color = $this->getColor($stateItem) ?? ($isBadge ? 'primary' : null);
            $iconColor = $this->getIconColor($stateItem);

            $size = $this->getSize($stateItem);

            $iconHtml = generate_icon_html($this->getIcon($stateItem), attributes: (new FilamentComponentAttributeBag)
                ->merge(['aria-hidden' => 'true'], escape: false)
                ->color(IconComponent::class, $iconColor), size: match ($size) {
                    TextSize::Medium => IconSize::Medium,
                    TextSize::Large => IconSize::Large,
                    default => IconSize::Small,
                })?->toHtml();

            $isCopyable = $this->isCopyable($stateItem);

            if ($isCopyable) {
                $copyableStateJs = Js::from($this->getCopyableState($stateItem) ?? $formattedState ?? $this->formatState($stateItem));
                $copyMessageJs = Js::from($this->getCopyMessage($stateItem));
                $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($stateItem));
            }

            $tooltip = $this->getTooltip($stateItem);

            return [
                'attributes' => (new FilamentComponentAttributeBag)
                    ->class([
                        'fi-ta-text-item',
                        (($fontFamily = $this->getFontFamily($stateItem)) instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : (is_string($fontFamily) ? $fontFamily : ''),
                    ])
                    ->when(
                        ! $isBadge,
                        fn (ComponentAttributeBag $attributes) => $attributes
                            ->class([
                                ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                                (($weight = $this->getWeight($stateItem)) instanceof FontWeight) ? "fi-font-{$weight->value}" : (is_string($weight) ? $weight : ''),
                            ])
                            ->when($lineClamp, fn (ComponentAttributeBag $attributes) => $attributes->style([
                                "--line-clamp: {$lineClamp}",
                            ]))
                            ->color(ItemComponent::class, $color)
                    ),
                'contentAttributes' => ($isBadge || $isCopyable || filled($tooltip))
                    ? (new FilamentComponentAttributeBag)
                        ->merge([
                            'x-on:click.prevent.stop' => $isCopyable
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
                            'fi-copyable' => $isCopyable,
                        ])
                        ->when(
                            $isBadge,
                            fn (ComponentAttributeBag $attributes) => $attributes
                                ->class([
                                    'fi-badge' => $isBadge,
                                    ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                                ])
                                ->color(BadgeComponent::class, $color ?? 'primary'),
                        )
                    : null,
                'iconAfterHtml' => ($iconPosition === IconPosition::After) ? $iconHtml : '',
                'iconBeforeHtml' => ($iconPosition === IconPosition::Before) ? $iconHtml : '',
            ];
        };

        $descriptionAbove = $this->getDescriptionAbove();
        $descriptionBelow = $this->getDescriptionBelow();
        $hasDescriptions = filled($descriptionAbove) || filled($descriptionBelow);

        if (
            ($stateCount === 1) &&
            (! $isBulleted) &&
            (! $hasDescriptions) &&
            (! $lineClamp)
        ) {
            $stateItem = Arr::first($state);
            $stateItemFormattedState = $isCollapsedList ? null : $this->formatState($stateItem);
            [
                'attributes' => $stateItemAttributes,
                'contentAttributes' => $stateItemContentAttributes,
                'iconAfterHtml' => $stateItemIconAfterHtml,
                'iconBeforeHtml' => $stateItemIconBeforeHtml,
            ] = $getStateItem($stateItem, $stateItemFormattedState);

            ob_start(); ?>

            <div <?= $attributes
                ->merge($stateItemAttributes->getAttributes(), escape: false)
                ->toHtml() ?>>
                <?php if ($stateItemContentAttributes) { ?>
                    <span <?= $stateItemContentAttributes->toHtml() ?>>
                <?php } ?>

                <?= $stateItemIconBeforeHtml ?>
                <?= $formatState($stateItem, $stateItemFormattedState) ?>
                <?= $stateItemIconAfterHtml ?>

                <?php if ($stateItemContentAttributes) { ?>
                    </span>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $attributes = $attributes
            ->class([
                'fi-bulleted' => $isBulleted,
                'fi-ta-text-has-line-breaks' => $isListWithLineBreaks,
            ]);

        if ($hasDescriptions || $stateOverListLimitCount) {
            $attributes = $attributes
                ->merge([
                    'x-data' => ($stateOverListLimitCount && $isLimitedListExpandable)
                        ? '{ isLimited: true }'
                        : null,
                ], escape: false)
                ->class([
                    'fi-ta-text-has-descriptions' => $hasDescriptions,
                    'fi-ta-text-list-limited' => $stateOverListLimitCount,
                ]);

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($descriptionAbove)) { ?>
                    <p class="fi-ta-text-description">
                        <?= e($descriptionAbove) ?>
                    </p>
                <?php } ?>

                <?php if (($stateCount === 1) && (! $isBulleted)) { ?>
                    <?php
                        $stateItem = Arr::first($state);
                    $stateItemFormattedState = $isCollapsedList ? null : $this->formatState($stateItem);
                    [
                        'attributes' => $stateItemAttributes,
                        'contentAttributes' => $stateItemContentAttributes,
                        'iconAfterHtml' => $stateItemIconAfterHtml,
                        'iconBeforeHtml' => $stateItemIconBeforeHtml,
                    ] = $getStateItem($stateItem, $stateItemFormattedState);
                    ?>

                    <p <?= $stateItemAttributes->toHtml() ?>>
                        <?php if ($stateItemContentAttributes) { ?>
                            <span <?= $stateItemContentAttributes->toHtml() ?>>
                        <?php } ?>

                        <?= $stateItemIconBeforeHtml ?>
                        <?= $formatState($stateItem, $stateItemFormattedState) ?>
                        <?= $stateItemIconAfterHtml ?>

                        <?php if ($stateItemContentAttributes) { ?>
                            </span>
                        <?php } ?>
                    </p>
                <?php } else { ?>
                    <ul>
                        <?php $stateIteration = 1; ?>

                        <?php foreach ($state as $stateItem) { ?>
                            <?php $stateItemFormattedState = $isCollapsedList ? null : $this->formatState($stateItem); ?>
                            <?php [
                                'attributes' => $stateItemAttributes,
                                'contentAttributes' => $stateItemContentAttributes,
                                'iconAfterHtml' => $stateItemIconAfterHtml,
                                'iconBeforeHtml' => $stateItemIconBeforeHtml,
                            ] = $getStateItem($stateItem, $stateItemFormattedState); ?>

                            <li
                                <?php if ($stateIteration > $listLimit) { ?>
                                    x-show="! isLimited"
                                    x-cloak
                                    x-transition
                                <?php } ?>
                                <?= $stateItemAttributes->toHtml() ?>
                            >
                                <?php if ($stateItemContentAttributes) { ?>
                                    <span <?= $stateItemContentAttributes->toHtml() ?>>
                                <?php } ?>

                                <?= $stateItemIconBeforeHtml ?>
                                <?= $formatState($stateItem, $stateItemFormattedState) ?>
                                <?= $stateItemIconAfterHtml ?>

                                <?php if ($stateItemContentAttributes) { ?>
                                    </span>
                                <?php } ?>
                            </li>

                            <?php $stateIteration++ ?>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php if ($stateOverListLimitCount) { ?>
                    <div class="fi-ta-text-list-limited-message">
                        <?php
                            // These stay `<div role="button">` — not a real `<button>`, and deliberately without
                            // `tabindex`. When the column has a record URL or action, the table wraps the whole cell
                            // content in an `<a>` / `<button>` (see the record-content wrapper in the tables view), and
                            // a `<button>` — or any element with `tabindex` — is interactive content that is invalid
                            // nested inside a link/button. `role="button"` + `aria-expanded` expose the control's
                            // purpose and state to assistive tech without introducing that invalid nesting.
                    ?>
                        <?php if ($isLimitedListExpandable) { ?>
                            <div
                                role="button"
                                x-bind:aria-expanded="(! isLimited).toString()"
                                x-on:click.prevent.stop="isLimited = false"
                                x-show="isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-tables::table.columns.text.actions.expand_list', $stateOverListLimitCount) ?>
                            </div>

                            <div
                                role="button"
                                x-bind:aria-expanded="(! isLimited).toString()"
                                x-on:click.prevent.stop="isLimited = true"
                                x-cloak
                                x-show="! isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-tables::table.columns.text.actions.collapse_list', $stateOverListLimitCount) ?>
                            </div>
                        <?php } else { ?>
                            <?= trans_choice('filament-tables::table.columns.text.more_list_items', $stateOverListLimitCount) ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (filled($descriptionBelow)) { ?>
                    <p class="fi-ta-text-description">
                        <?= e($descriptionBelow) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?php $stateItemFormattedState = $isCollapsedList ? null : $this->formatState($stateItem); ?>
                <?php [
                    'attributes' => $stateItemAttributes,
                    'contentAttributes' => $stateItemContentAttributes,
                    'iconAfterHtml' => $stateItemIconAfterHtml,
                    'iconBeforeHtml' => $stateItemIconBeforeHtml,
                ] = $getStateItem($stateItem, $stateItemFormattedState); ?>

                <li <?= $stateItemAttributes->toHtml() ?>>
                    <?php if ($stateItemContentAttributes) { ?>
                        <span <?= $stateItemContentAttributes->toHtml() ?>>
                    <?php } ?>

                    <?= $stateItemIconBeforeHtml ?>
                    <?= $formatState($stateItem, $stateItemFormattedState) ?>
                    <?= $stateItemIconAfterHtml ?>

                    <?php if ($stateItemContentAttributes) { ?>
                        </span>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>

        <?php return ob_get_clean();
    }
}
