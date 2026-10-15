<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\View\Components\Columns\IconColumnComponent\IconComponent;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Stringable;

use function Filament\Support\generate_href_html;
use function Filament\Support\generate_icon_html;

class IconColumn extends Column implements HasEmbeddedView
{
    use CanWrap;
    use Concerns\HasColor {
        getColor as getBaseColor;
    }
    use Concerns\HasIcon {
        getIcon as getBaseIcon;
    }

    protected bool | Closure | null $isBoolean = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $falseColor = null;

    protected string | BackedEnum | Htmlable | Closure | false | null $falseIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $trueColor = null;

    protected string | BackedEnum | Htmlable | Closure | false | null $trueIcon = null;

    protected bool | Closure $isListWithLineBreaks = false;

    protected IconSize | string | Closure | null $size = null;

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function false(string | BackedEnum | Htmlable | Closure | false | null $icon = null, string | array | Closure | null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function falseColor(string | array | Closure | null $color): static
    {
        $this->boolean();
        $this->falseColor = $color;

        return $this;
    }

    public function falseIcon(string | BackedEnum | Htmlable | Closure | false | null $icon): static
    {
        $this->boolean();
        $this->falseIcon = $icon;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function true(string | BackedEnum | Htmlable | Closure | false | null $icon = null, string | array | Closure | null $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function trueColor(string | array | Closure | null $color): static
    {
        $this->boolean();
        $this->trueColor = $color;

        return $this;
    }

    public function trueIcon(string | BackedEnum | Htmlable | Closure | false | null $icon): static
    {
        $this->boolean();
        $this->trueIcon = $icon;

        return $this;
    }

    /**
     * @deprecated Use `icons()` instead.
     *
     * @param  array<mixed> | Arrayable | Closure  $options
     */
    public function options(array | Arrayable | Closure $options): static
    {
        $this->icons($options);

        return $this;
    }

    public function size(IconSize | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(mixed $state, ?Model $relatedRecord = null): IconSize | string | null
    {
        $size = $this->evaluate($this->size, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);

        if (blank($size)) {
            return null;
        }

        if ($size === 'base') {
            return null;
        }

        if (is_string($size)) {
            $size = IconSize::tryFrom($size) ?? $size;
        }

        return $size;
    }

    public function getIcon(mixed $state, ?Model $relatedRecord = null): string | BackedEnum | Htmlable | null
    {
        if (filled($icon = $this->getBaseIcon($state, $relatedRecord))) {
            return $icon;
        }

        if (! $this->isBoolean($state, $relatedRecord)) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueIcon($state, $relatedRecord) : $this->getFalseIcon($state, $relatedRecord);
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getColor(mixed $state, ?Model $relatedRecord = null): string | array | null
    {
        if (filled($color = $this->getBaseColor($state, $relatedRecord))) {
            return $color;
        }

        if (! $this->isBoolean($state, $relatedRecord)) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueColor($state, $relatedRecord) : $this->getFalseColor($state, $relatedRecord);
    }

    /**
     * @return string | array<string>
     */
    public function getFalseColor(mixed $state = null, ?Model $relatedRecord = null): string | array
    {
        return $this->evaluate(
            $this->falseColor,
            $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0),
        ) ?? 'danger';
    }

    public function getFalseIcon(mixed $state = null, ?Model $relatedRecord = null): string | BackedEnum | Htmlable | null
    {
        $icon = $this->evaluate(
            $this->falseIcon,
            $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0),
        );

        if ($icon === false) {
            return null;
        }

        return $icon
            ?? FilamentIcon::resolve(TablesIconAlias::COLUMNS_ICON_COLUMN_FALSE)
            ?? Heroicon::OutlinedXCircle;
    }

    /**
     * @return string | array<string>
     */
    public function getTrueColor(mixed $state = null, ?Model $relatedRecord = null): string | array
    {
        return $this->evaluate(
            $this->trueColor,
            $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0),
        ) ?? 'success';
    }

    public function getTrueIcon(mixed $state = null, ?Model $relatedRecord = null): string | BackedEnum | Htmlable | null
    {
        $icon = $this->evaluate(
            $this->trueIcon,
            $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0),
        );

        if ($icon === false) {
            return null;
        }

        return $icon
            ?? FilamentIcon::resolve(TablesIconAlias::COLUMNS_ICON_COLUMN_TRUE)
            ?? Heroicon::OutlinedCheckCircle;
    }

    public function isBoolean(mixed $state = null, ?Model $relatedRecord = null): bool
    {
        if (blank($this->isBoolean)) {
            $record = $this->getRecord();

            $this->isBoolean = ($record instanceof Model) && $this->getRecord()->hasCast($this->getName(), ['bool', 'boolean']);
        }

        return (bool) $this->evaluate(
            $this->isBoolean,
            $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0),
        );
    }

    public function isListWithLineBreaks(): bool
    {
        return (bool) $this->evaluate($this->isListWithLineBreaks);
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $alignment = $this->getAlignment();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-ta-icon',
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
        $relatedRecords = $this->getRelatedRecordsForState($state);

        $attributes = $attributes
            ->class([
                'fi-ta-icon-has-line-breaks' => $this->isListWithLineBreaks(),
                'fi-wrapped' => $this->canWrap(),
            ]);

        $shouldOpenUrlInNewTab = $this->shouldOpenUrlInNewTab();

        $formatState = function (mixed $stateItem, ?Model $relatedRecord) use ($shouldOpenUrlInNewTab): string {
            $icon = $this->getIcon($stateItem, $relatedRecord);

            if (blank($icon)) {
                return '';
            }

            $color = $this->getColor($stateItem, $relatedRecord);
            $size = $this->getSize($stateItem, $relatedRecord);

            $item = generate_icon_html($icon, attributes: (new FilamentComponentAttributeBag)
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getTooltip($stateItem, $relatedRecord))
                        ? '{
                            content: ' . Js::from($tooltip) . ',
                            theme: $store.theme,
                            allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                        }'
                        : null,
                ], escape: false)
                ->color(IconComponent::class, $color), size: $size ?? IconSize::Large)
                ->toHtml();

            // The icon is hidden from assistive technology, so a text alternative is
            // rendered alongside it. It also gives any `<a>` / `<button>` wrapping the
            // cell's content an accessible name.
            $stateItemTextAlternative = match (true) {
                filled($tooltip) => $tooltip,
                $this->isBoolean($stateItem, $relatedRecord) => __('filament-tables::table.columns.icon.boolean.' . ($stateItem ? 'true' : 'false')),
                $stateItem instanceof LabelInterface => $stateItem->getLabel(),
                $stateItem instanceof BackedEnum => $stateItem->value,
                $stateItem instanceof Htmlable => strip_tags($stateItem->toHtml()),
                is_scalar($stateItem) || $stateItem instanceof Stringable => $stateItem,
                default => null,
            };

            if (filled($stateItemTextAlternative)) {
                $item .= '<span class="fi-sr-only">' . e(trim(strip_tags((string) $stateItemTextAlternative))) . '</span>';
            }

            if (filled($url = $this->getUrl($stateItem, $relatedRecord))) {
                $item = '<a ' . generate_href_html($url, $shouldOpenUrlInNewTab)->toHtml() . '>' . $item . '</a>';
            }

            return $item;
        };

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($state as $stateItemIndex => $stateItem) { ?>
                <?= $formatState($stateItem, $relatedRecords[$stateItemIndex] ?? null) ?>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
