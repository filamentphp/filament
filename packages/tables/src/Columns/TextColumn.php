<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasLineClamp;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\View\Components\BadgeComponent;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent\IconComponent;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use stdClass;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;

class TextColumn extends Column implements HasEmbeddedView
{
    use CanWrap;
    use Concerns\CanBeCopied;
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

    public function toEmbeddedHtml(): string
    {
        $isBadge = $this->isBadge();
        $isListWithLineBreaks = $this->isListWithLineBreaks();
        $isLimitedListExpandable = $this->isLimitedListExpandable();

        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-text',
                'fi-inline' => $this->isInline(),
            ]);

        if (blank($state)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder !== null)) { ?>
                    <p class="fi-ta-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return ob_get_clean();
        }

        $shouldOpenUrlInNewTab = $this->shouldOpenUrlInNewTab();

        $formatState = function (mixed $stateItem) use ($shouldOpenUrlInNewTab): string {
            $url = $this->getUrl($stateItem);

            $item = '';

            if (filled($url)) {
                $item .= '<a ' . generate_href_html($url, $shouldOpenUrlInNewTab)->toHtml() . '>';
            }

            $item .= e($this->formatState($stateItem));

            if (filled($url)) {
                $item .= '</a>';
            }

            return $item;
        };

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
            $formatState = fn (mixed $stateItem): string => $stateItem;
        }

        $alignment = $this->getAlignment();

        $attributes = $attributes
            ->class([
                'fi-ta-text-has-badges' => $isBadge,
                'fi-wrapped' => $this->canWrap(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        $lineClamp = $this->getLineClamp();
        $iconPosition = $this->getIconPosition();
        $isBulleted = $this->isBulleted();

        $getStateItem = function (mixed $stateItem) use ($iconPosition, $isBadge, $lineClamp): array {
            $color = $this->getColor($stateItem) ?? ($isBadge ? 'primary' : null);
            $iconColor = $this->getIconColor($stateItem);

            $size = $this->getSize($stateItem);

            $iconHtml = generate_icon_html($this->getIcon($stateItem), attributes: (new ComponentAttributeBag)
                ->color(IconComponent::class, $iconColor), size: match ($size) {
                    TextSize::Medium => IconSize::Medium,
                    TextSize::Large => IconSize::Large,
                    default => IconSize::Small,
                })?->toHtml();

            $isCopyable = $this->isCopyable($stateItem);

            if ($isCopyable) {
                $copyableStateJs = Js::from($this->getCopyableState($stateItem) ?? $this->formatState($stateItem));
                $copyMessageJs = Js::from($this->getCopyMessage($stateItem));
                $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($stateItem));
            }

            return [
                'attributes' => (new ComponentAttributeBag)
                    ->merge([
                        'x-on:click' => $isCopyable
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
                            }'
                            : null,
                    ], escape: false)
                    ->class([
                        'fi-ta-text-item',
                        (($fontFamily = $this->getFontFamily($stateItem)) instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : (is_string($fontFamily) ? $fontFamily : ''),
                        'fi-copyable' => $isCopyable,
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
                'badgeAttributes' => $isBadge
                    ? (new ComponentAttributeBag)
                        ->class([
                            'fi-badge',
                            ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                        ])
                        ->color(BadgeComponent::class, $color ?? 'primary')
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
            (! $hasDescriptions)
        ) {
            $stateItem = Arr::first($state);
            [
                'attributes' => $stateItemAttributes,
                'badgeAttributes' => $stateItemBadgeAttributes,
                'iconAfterHtml' => $stateItemIconAfterHtml,
                'iconBeforeHtml' => $stateItemIconBeforeHtml,
            ] = $getStateItem($stateItem);

            ob_start(); ?>

            <div <?= $attributes
                ->merge($stateItemAttributes->getAttributes(), escape: false)
                ->toHtml() ?>>
                <?php if ($isBadge) { ?>
                    <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                <?php } ?>

                <?= $stateItemIconBeforeHtml ?>
                <?= $formatState($stateItem) ?>
                <?= $stateItemIconAfterHtml ?>

                <?php if ($isBadge) { ?>
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
                    [
                        'attributes' => $stateItemAttributes,
                        'badgeAttributes' => $stateItemBadgeAttributes,
                        'iconAfterHtml' => $stateItemIconAfterHtml,
                        'iconBeforeHtml' => $stateItemIconBeforeHtml,
                    ] = $getStateItem($stateItem);
                    ?>

                    <p <?= $stateItemAttributes->toHtml() ?>>
                        <?php if ($isBadge) { ?>
                            <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                        <?php } ?>

                        <?= $stateItemIconBeforeHtml ?>
                        <?= $formatState($stateItem) ?>
                        <?= $stateItemIconAfterHtml ?>

                        <?php if ($isBadge) { ?>
                            </span>
                        <?php } ?>
                    </p>
                <?php } else { ?>
                    <ul>
                        <?php $stateIteration = 1; ?>

                        <?php foreach ($state as $stateItem) { ?>
                            <?php [
                                'attributes' => $stateItemAttributes,
                                'badgeAttributes' => $stateItemBadgeAttributes,
                                'iconAfterHtml' => $stateItemIconAfterHtml,
                                'iconBeforeHtml' => $stateItemIconBeforeHtml,
                            ] = $getStateItem($stateItem); ?>

                            <li
                                <?php if ($stateIteration > $listLimit) { ?>
                                    x-show="! isLimited"
                                    x-cloak
                                    x-transition
                                <?php } ?>
                                <?= $stateItemAttributes->toHtml() ?>
                            >
                                <?php if ($isBadge) { ?>
                                    <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                                <?php } ?>

                                <?= $stateItemIconBeforeHtml ?>
                                <?= $formatState($stateItem) ?>
                                <?= $stateItemIconAfterHtml ?>

                                <?php if ($isBadge) { ?>
                                    </span>
                                <?php } ?>
                            </li>

                            <?php $stateIteration++ ?>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <?php if ($stateOverListLimitCount) { ?>
                    <div class="fi-ta-text-list-limited-message">
                        <?php if ($isLimitedListExpandable) { ?>
                            <div
                                role="button"
                                x-on:click.prevent.stop="isLimited = false"
                                x-show="isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-tables::table.columns.text.actions.expand_list', $stateOverListLimitCount) ?>
                            </div>

                            <div
                                role="button"
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
                <?php [
                    'attributes' => $stateItemAttributes,
                    'badgeAttributes' => $stateItemBadgeAttributes,
                    'iconAfterHtml' => $stateItemIconAfterHtml,
                    'iconBeforeHtml' => $stateItemIconBeforeHtml,
                ] = $getStateItem($stateItem); ?>

                <li <?= $stateItemAttributes->toHtml() ?>>
                    <?php if ($isBadge) { ?>
                        <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                    <?php } ?>

                    <?= $stateItemIconBeforeHtml ?>
                    <?= $formatState($stateItem) ?>
                    <?= $stateItemIconAfterHtml ?>

                    <?php if ($isBadge) { ?>
                        </span>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>

        <?php return ob_get_clean();
    }
}
