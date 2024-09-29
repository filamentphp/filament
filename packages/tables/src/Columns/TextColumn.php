<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasLineClamp;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn\Enums\TextColumnSize;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use stdClass;
use function Filament\Support\generate_icon_html;
use function Filament\Support\get_color_css_variables;

class TextColumn extends Column
{
    use Concerns\CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasDescription;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use HasFontFamily;
    use HasLineClamp;
    use HasWeight;

    protected bool | Closure $canWrap = false;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    protected TextColumnSize | string | Closure | null $size = null;

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

    public function wrap(bool | Closure $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function size(TextColumnSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state): TextColumnSize | string
    {
        $size = $this->evaluate($this->size, [
            'state' => $state,
        ]);

        if (blank($size)) {
            return TextColumnSize::Small;
        }

        if (is_string($size)) {
            $size = TextColumnSize::tryFrom($size) ?? $size;
        }

        if ($size === 'base') {
            return TextColumnSize::Medium;
        }

        return $size;
    }

    public function canWrap(): bool
    {
        return (bool) $this->evaluate($this->canWrap);
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

    protected function defaultToHtml(): string
    {
        $isBadge = $this->isBadge();
        $isListWithLineBreaks = $this->isListWithLineBreaks();
        $isLimitedListExpandable = $this->isLimitedListExpandable();

        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $formatState = fn (mixed $stateItem): string => e($this->formatState($stateItem));

        $state = Arr::wrap($state);
        $stateCount = count($state);

        $listLimit = $this->getListLimit() ?? $stateCount;
        $stateOverLimitCount = 0;

        if ($listLimit && ($stateCount > $listLimit)) {
            $stateOverLimitCount = $stateCount - $listLimit;

            if (
                (! $isListWithLineBreaks) ||
                (! $isLimitedListExpandable)
            ) {
                $state = array_slice($state, 0, $listLimit);
            }
        }

        if ((! $isListWithLineBreaks) && (! $isBadge)) {
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
            $formatState = fn (mixed $stateItem): string => e($stateItem);
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-text',
                'fi-inline' => $this->isInline(),
            ]);

        if (empty($state)) {
            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder !== null)): ?>
                    <p class="fi-ta-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php return ob_get_clean();
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

        $getStateItem = function (mixed $stateItem) use ($iconPosition, $isBadge, $isBulleted, $lineClamp): array {
            $color = $this->getColor($stateItem) ?? ($isBadge ? 'primary' : null);
            $iconColor = $this->getIconColor($stateItem);

            $iconHtml = generate_icon_html($this->getIcon($stateItem), attributes: (new ComponentAttributeBag())
                ->class([
                    match ($iconColor) {
                        null, 'gray' => null,
                        default => 'fi-color-custom',
                    } => filled($iconColor),
                    is_string($iconColor) ? "fi-color-{$iconColor}" : null,
                ])
                ->style([
                    ...($isBadge
                        ? [
                            get_color_css_variables(
                                $color,
                                shades: [500],
                                alias: 'badge.icon',
                            ) => $color !== 'gray',
                        ]
                        : []
                    ),
                    ...(((! $isBadge) && $iconColor)
                        ? [
                            get_color_css_variables(
                                $iconColor,
                                shades: [400, 500],
                                alias: 'tables::columns.text-column.item.icon',
                            ) => ! in_array($iconColor, [null, 'gray']),
                        ]
                        : []
                    ),
                ]))?->toHtml();

            $isCopyable = $this->isCopyable($stateItem);

            if ($isCopyable) {
                $copyableStateJs = Js::from($this->getCopyableState($stateItem) ?? $this->formatState($stateItem));
                $copyMessageJs = Js::from($this->getCopyMessage($stateItem));
                $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($stateItem));
            }

            return [
                'attributes' => (new ComponentAttributeBag())
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
                    ], escape: false)
                    ->class([
                        'fi-ta-text-item',
                        ...((! $isBadge) ? [
                            match ($color) {
                                null, 'gray' => null,
                                default => 'fi-color-custom',
                            },
                            is_string($color) ? "fi-color-{$color}" : null,
                            (($size = $this->getSize($stateItem)) instanceof TextColumnSize) ? "fi-size-{$size->value}" : $size,
                            (($weight = $this->getWeight($stateItem)) instanceof FontWeight) ? "fi-font-{$weight->value}" : (is_string($weight) ? $weight : ''),
                        ] : []),
                        (($fontFamily = $this->getFontFamily($stateItem)) instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : (is_string($fontFamily) ? $fontFamily : ''),
                        'fi-copyable' => $isCopyable,
                    ])
                    ->style([
                        ...((! $isBadge) ? [
                            get_color_css_variables(
                                $color,
                                shades: [400, 600],
                                alias: 'tables::columns.text-column.item',
                            ) => ! in_array($color, [null, 'gray']),
                            "--line-clamp: {$lineClamp}" => $lineClamp && (! $isBadge),
                        ] : []),
                    ]),
                'badgeAttributes' => $isBadge
                    ? (new ComponentAttributeBag())
                        ->class([
                            'fi-badge',
                            match ($color ?? 'primary') {
                                'gray' => '',
                                default => 'fi-color-custom',
                            },
                            is_string($color) ? "fi-color-{$color}" : null,
                            (($size = $this->getSize($stateItem)) instanceof TextColumnSize) ? "fi-size-{$size->value}" : $size,
                        ])
                        ->style([
                            get_color_css_variables(
                                $color,
                                shades: [
                                    50,
                                    400,
                                    600,
                                ],
                                alias: 'badge',
                            ) => $color !== 'gray',
                        ])
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
                ->merge($stateItemAttributes->getAttributes())
                ->toHtml() ?>>
                <?php if ($isBadge): ?>
                    <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                <?php endif; ?>

                <?= $stateItemIconBeforeHtml ?>
                <?= $formatState($stateItem) ?>
                <?= $stateItemIconAfterHtml ?>

                <?php if ($isBadge): ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php return ob_get_clean();
        }

        $attributes = $attributes
            ->class([
                'fi-bulleted' => $isBulleted,
                'fi-ta-text-has-line-breaks' => $isListWithLineBreaks,
            ]);

        if ($hasDescriptions || $stateOverLimitCount) {
            $attributes = $attributes
                ->merge([
                    'x-data' => ($stateOverLimitCount && $isLimitedListExpandable)
                        ? '{ isLimited: true }'
                        : null,
                ])
                ->class([
                    'fi-ta-text-has-descriptions' => $hasDescriptions,
                    'fi-ta-text-list-limited' => $stateOverLimitCount,
                ]);

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($descriptionAbove)): ?>
                    <p class="fi-ta-text-description">
                        <?= e($descriptionAbove) ?>
                    </p>
                <?php endif; ?>

                <?php if (($stateCount === 1) && (! $isBulleted)): ?>
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
                        <?php if ($isBadge): ?>
                            <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                        <?php endif; ?>

                        <?= $stateItemIconBeforeHtml ?>
                        <?= $formatState($stateItem) ?>
                        <?= $stateItemIconAfterHtml ?>

                        <?php if ($isBadge): ?>
                            </span>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <ul>
                        <?php $stateIteration = 1; ?>

                        <?php foreach ($state as $stateItem): ?>
                            <?php [
                                'attributes' => $stateItemAttributes,
                                'badgeAttributes' => $stateItemBadgeAttributes,
                                'iconAfterHtml' => $stateItemIconAfterHtml,
                                'iconBeforeHtml' => $stateItemIconBeforeHtml,
                            ] = $getStateItem($stateItem); ?>

                            <li
                                <?php if ($stateIteration > $listLimit): ?>
                                    x-show="! isLimited"
                                    x-cloak
                                    x-transition
                                <?php endif; ?>
                                <?= $stateItemAttributes->toHtml() ?>
                            >
                                <?php if ($isBadge): ?>
                                    <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                                <?php endif; ?>

                                <?= $stateItemIconBeforeHtml ?>
                                <?= $formatState($stateItem) ?>
                                <?= $stateItemIconAfterHtml ?>

                                <?php if ($isBadge): ?>
                                    </span>
                                <?php endif; ?>
                            </li>

                            <?php $stateIteration++ ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($stateOverLimitCount): ?>
                    <p class="fi-ta-text-list-limited-message">
                        <?php if ($isLimitedListExpandable): ?>
                            <button
                                type="button"
                                x-on:click.prevent="isLimited = false"
                                x-show="isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-tables::table.columns.text.actions.expand_list', $stateOverLimitCount) ?>
                            </button>

                            <button
                                type="button"
                                x-on:click.prevent="isLimited = true"
                                x-cloak
                                x-show="! isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-tables::table.columns.text.actions.collapse_list', $stateOverLimitCount) ?>
                            </button>
                        <?php else: ?>
                            <?= trans_choice('filament-tables::table.columns.text.more_list_items', $stateOverLimitCount) ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if (filled($descriptionBelow)): ?>
                    <p class="fi-ta-text-description">
                        <?= e($descriptionBelow) ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php return ob_get_clean();
        }

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem): ?>
                <?php [
                    'attributes' => $stateItemAttributes,
                    'badgeAttributes' => $stateItemBadgeAttributes,
                    'iconAfterHtml' => $stateItemIconAfterHtml,
                    'iconBeforeHtml' => $stateItemIconBeforeHtml,
                ] = $getStateItem($stateItem); ?>

                <li <?= $stateItemAttributes->toHtml() ?>>
                    <?php if ($isBadge): ?>
                        <span <?= $stateItemBadgeAttributes->toHtml() ?>>
                    <?php endif; ?>

                    <?= $stateItemIconBeforeHtml ?>
                    <?= $formatState($stateItem) ?>
                    <?= $stateItemIconAfterHtml ?>

                    <?php if ($isBadge): ?>
                        </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php return ob_get_clean();
    }

    public function toHtml(): string
    {
        if (! $this->hasView()) {
            return $this->defaultToHtml();
        }

        return parent::toHtml();
    }
}
