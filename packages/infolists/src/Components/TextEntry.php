<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Infolists\View\Components\TextEntryComponent\ItemComponent;
use Filament\Infolists\View\Components\TextEntryComponent\ItemComponent\IconComponent;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;

class TextEntry extends Entry implements HasAffixActions, HasEmbeddedView
{
    use CanBeCopied;
    use CanWrap;
    use Concerns\CanFormatState;
    use Concerns\HasAffixes;
    use Concerns\HasColor;
    use Concerns\HasIcon;
    use Concerns\HasIconColor;
    use HasFontFamily;
    use HasLineClamp;
    use HasWeight;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isProse = false;

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

    public function prose(bool | Closure $condition = true): static
    {
        $this->isProse = $condition;

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

    public function isProse(): bool
    {
        return (bool) $this->evaluate($this->isProse);
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
                'fi-in-text',
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
                'fi-in-text-has-badges' => $isBadge,
                'fi-wrapped' => $this->canWrap(),
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        $lineClamp = $this->getLineClamp();
        $iconPosition = $this->getIconPosition();
        $isBulleted = $this->isBulleted();
        $isProse = $this->isProse();
        $isMarkdown = $this->isMarkdown();

        $getStateItem = function (mixed $stateItem) use ($iconPosition, $isBadge, $isMarkdown, $isProse, $lineClamp): array {
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

            $tooltip = $this->getTooltip($stateItem);

            return [
                'attributes' => (new ComponentAttributeBag)
                    ->class([
                        'fi-in-text-item',
                        'fi-prose' => $isProse || $isMarkdown,
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
                    ? (new ComponentAttributeBag)
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
                                    'fi-badge',
                                    ($size instanceof TextSize) ? "fi-size-{$size->value}" : $size,
                                ])
                                ->color(BadgeComponent::class, $color ?? 'primary')
                        )
                    : null,
                'iconAfterHtml' => ($iconPosition === IconPosition::After) ? $iconHtml : '',
                'iconBeforeHtml' => ($iconPosition === IconPosition::Before) ? $iconHtml : '',
            ];
        };

        $prefixActions = array_filter(
            $this->getPrefixActions(),
            fn (Action $prefixAction): bool => $prefixAction->isVisible(),
        );

        $suffixActions = array_filter(
            $this->getSuffixActions(),
            fn (Action $suffixAction): bool => $suffixAction->isVisible(),
        );

        if (
            ($stateCount === 1) &&
            (! $isBulleted) &&
            empty($prefixActions) &&
            empty($suffixActions)
        ) {
            $stateItem = Arr::first($state);
            [
                'attributes' => $stateItemAttributes,
                'contentAttributes' => $stateItemContentAttributes,
                'iconAfterHtml' => $stateItemIconAfterHtml,
                'iconBeforeHtml' => $stateItemIconBeforeHtml,
            ] = $getStateItem($stateItem);

            ob_start(); ?>

            <div <?= $attributes
                ->merge($stateItemAttributes->getAttributes(), escape: false)
                ->toHtml() ?>>
                <?php if ($stateItemContentAttributes) { ?>
                <span <?= $stateItemContentAttributes->toHtml() ?>>
                <?php } ?>

                <?= $stateItemIconBeforeHtml ?>
                <?= $formatState($stateItem) ?>
                <?= $stateItemIconAfterHtml ?>

                <?php if ($stateItemContentAttributes) { ?>
                    </span>
            <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $attributes = $attributes
            ->class([
                'fi-bulleted' => $isBulleted,
                'fi-in-text-has-line-breaks' => $isListWithLineBreaks,
            ]);

        if ($stateOverListLimitCount || $prefixActions || $suffixActions) {
            $attributes = $attributes
                ->merge([
                    'x-data' => $isLimitedListExpandable
                        ? '{ isLimited: true }'
                        : null,
                ], escape: false)
                ->class([
                    'fi-in-text-affixed' => $prefixActions || $suffixActions,
                    'fi-in-text-list-limited' => $stateOverListLimitCount,
                ]);

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if ($prefixActions) { ?>
                    <div class="fi-in-text-affix">
                        <?php foreach ($prefixActions as $prefixAction) { ?>
                            <?= $prefixAction->toHtml() ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($prefixActions || $suffixActions) { ?>
                    <div class="fi-in-text-affixed-content">
                <?php } ?>

                <ul>
                    <?php $stateIteration = 1; ?>

                    <?php foreach ($state as $stateItem) { ?>
                        <?php [
                            'attributes' => $stateItemAttributes,
                            'contentAttributes' => $stateItemContentAttributes,
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
                            <?php if ($stateItemContentAttributes) { ?>
                            <span <?= $stateItemContentAttributes->toHtml() ?>>
                            <?php } ?>

                            <?= $stateItemIconBeforeHtml ?>
                            <?= $formatState($stateItem) ?>
                            <?= $stateItemIconAfterHtml ?>

                            <?php if ($stateItemContentAttributes) { ?>
                                </span>
                        <?php } ?>
                        </li>

                        <?php $stateIteration++ ?>
                    <?php } ?>
                </ul>

                <?php if ($stateOverListLimitCount) { ?>
                    <div class="fi-in-text-list-limited-message">
                        <?php if ($isLimitedListExpandable) { ?>
                            <div
                                role="button"
                                x-on:click.prevent.stop="isLimited = false"
                                x-show="isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-infolists::components.entries.text.actions.expand_list', $stateOverListLimitCount) ?>
                            </div>

                            <div
                                role="button"
                                x-on:click.prevent.stop="isLimited = true"
                                x-cloak
                                x-show="! isLimited"
                                class="fi-link fi-size-xs"
                            >
                                <?= trans_choice('filament-infolists::components.entries.text.actions.collapse_list', $stateOverListLimitCount) ?>
                            </div>
                        <?php } else { ?>
                            <?= trans_choice('filament-infolists::components.entries.text.more_list_items', $stateOverListLimitCount) ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($prefixActions || $suffixActions) { ?>
                    </div>
                <?php } ?>

                <?php if ($suffixActions) { ?>
                    <div class="fi-in-text-affix">
                        <?php foreach ($suffixActions as $suffixAction) { ?>
                            <?= $suffixAction->toHtml() ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        ob_start(); ?>

        <ul <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItem) { ?>
                <?php [
                    'attributes' => $stateItemAttributes,
                    'contentAttributes' => $stateItemContentAttributes,
                    'iconAfterHtml' => $stateItemIconAfterHtml,
                    'iconBeforeHtml' => $stateItemIconBeforeHtml,
                ] = $getStateItem($stateItem); ?>

                <li <?= $stateItemAttributes->toHtml() ?>>
                    <?php if ($stateItemContentAttributes) { ?>
                    <span <?= $stateItemContentAttributes->toHtml() ?>>
                    <?php } ?>

                    <?= $stateItemIconBeforeHtml ?>
                    <?= $formatState($stateItem) ?>
                    <?= $stateItemIconAfterHtml ?>

                    <?php if ($stateItemContentAttributes) { ?>
                        </span>
                <?php } ?>
                </li>
            <?php } ?>
        </ul>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }

    public function canWrapByDefault(): bool
    {
        return true;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function avg(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->state(function (TextEntry $entry, ?Model $record) use ($relationship, $column): int | float | null {
            if (blank($record)) {
                return null;
            }

            $record->loadAvg(
                $entry->evaluate($relationship),
                $entry->evaluate($column),
            );

            return $record->getAttributeValue($entry->getName());
        });

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationships
     */
    public function counts(string | array | Closure | null $relationships): static
    {
        $this->state(function (TextEntry $entry, ?Model $record) use ($relationships): int | float | null {
            if (blank($record)) {
                return null;
            }

            $record->loadCount(
                $entry->evaluate($relationships),
            );

            return $record->getAttributeValue($entry->getName());
        });

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function max(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->state(function (TextEntry $entry, ?Model $record) use ($relationship, $column): int | float | null {
            if (blank($record)) {
                return null;
            }

            $record->loadMax(
                $entry->evaluate($relationship),
                $entry->evaluate($column),
            );

            return $record->getAttributeValue($entry->getName());
        });

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function min(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->state(function (TextEntry $entry, ?Model $record) use ($relationship, $column): int | float | null {
            if (blank($record)) {
                return null;
            }

            $record->loadMin(
                $entry->evaluate($relationship),
                $entry->evaluate($column),
            );

            return $record->getAttributeValue($entry->getName());
        });

        return $this;
    }

    /**
     * @param  string | array<int | string, string | Closure> | Closure | null  $relationship
     */
    public function sum(string | array | Closure | null $relationship, string | Closure | null $column): static
    {
        $this->state(function (TextEntry $entry, ?Model $record) use ($relationship, $column): int | float | null {
            if (blank($record)) {
                return null;
            }

            $record->loadSum(
                $entry->evaluate($relationship),
                $entry->evaluate($column),
            );

            return $record->getAttributeValue($entry->getName());
        });

        return $this;
    }
}
