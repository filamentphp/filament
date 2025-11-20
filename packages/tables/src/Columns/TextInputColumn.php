<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasInputMode;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class TextInputColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasExtraInputAttributes;
    use HasInputMode;
    use HasStep;

    protected string | RawJs | Closure | null $mask = null;

    protected string | Closure | null $type = null;

    protected string | Htmlable | Closure | null $suffixLabel = null;

    protected string | Htmlable | Closure | null $prefixLabel = null;

    protected string | BackedEnum | Htmlable | Closure | null $prefixIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $prefixIconColor = null;

    protected string | BackedEnum | Htmlable | Closure | null $suffixIcon = null;

    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $suffixIconColor = null;

    protected bool | Closure $isPrefixInline = false;

    protected bool | Closure $isSuffixInline = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type) ?? 'text';
    }

    public function mask(string | RawJs | Closure | null $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask(): string | RawJs | null
    {
        return $this->evaluate($this->mask);
    }

    public function prefix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->prefixLabel = $label;
        $this->inlinePrefix($isInline);

        return $this;
    }

    public function suffix(string | Htmlable | Closure | null $label, bool | Closure $isInline = false): static
    {
        $this->suffixLabel = $label;
        $this->inlineSuffix($isInline);

        return $this;
    }

    public function inlinePrefix(bool | Closure $isInline = true): static
    {
        $this->isPrefixInline = $isInline;

        return $this;
    }

    public function inlineSuffix(bool | Closure $isInline = true): static
    {
        $this->isSuffixInline = $isInline;

        return $this;
    }

    public function prefixIcon(string | BackedEnum | Htmlable | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->prefixIcon = $icon;
        $this->inlinePrefix($isInline);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function prefixIconColor(string | array | Closure | null $color = null): static
    {
        $this->prefixIconColor = $color;

        return $this;
    }

    public function suffixIcon(string | BackedEnum | Htmlable | Closure | null $icon, bool | Closure $isInline = false): static
    {
        $this->suffixIcon = $icon;
        $this->inlineSuffix($isInline);

        return $this;
    }

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function suffixIconColor(string | array | Closure | null $color = null): static
    {
        $this->suffixIconColor = $color;

        return $this;
    }

    public function getPrefixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->prefixLabel);
    }

    public function getSuffixLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->suffixLabel);
    }

    public function getPrefixIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->prefixIcon);
    }

    public function getSuffixIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->suffixIcon);
    }

    /**
     * @return string | array<string> | null
     */
    public function getPrefixIconColor(): string | array | null
    {
        return $this->evaluate($this->prefixIconColor);
    }

    /**
     * @return string | array<string> | null
     */
    public function getSuffixIconColor(): string | array | null
    {
        return $this->evaluate($this->suffixIconColor);
    }

    public function isPrefixInline(): bool
    {
        return (bool) $this->evaluate($this->isPrefixInline);
    }

    public function isSuffixInline(): bool
    {
        return (bool) $this->evaluate($this->isSuffixInline);
    }

    public function toEmbeddedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $state = $this->getState();
        $mask = $this->getMask();

        $alignment = $this->getAlignment() ?? Alignment::Start;

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $type = filled($mask) ? 'text' : $this->getType();

        $prefixIcon = $this->getPrefixIcon();
        $prefixIconColor = $this->getPrefixIconColor();
        $prefixLabel = $this->getPrefixLabel();
        $suffixIcon = $this->getSuffixIcon();
        $suffixIconColor = $this->getSuffixIconColor();
        $suffixLabel = $this->getSuffixLabel();
        $isPrefixInline = $this->isPrefixInline();
        $isSuffixInline = $this->isSuffixInline();

        $hasPrefix = $prefixIcon || filled($prefixLabel);
        $hasSuffix = $suffixIcon || filled($suffixLabel);

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'x-load' => true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/text-input', 'filament/tables'),
                'x-data' => 'textInputTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-text-input',
                'fi-inline' => $this->isInline(),
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'disabled' => $isDisabled,
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
                'x-bind:disabled' => $isDisabled ? null : 'isLoading',
                'inputmode' => $this->getInputMode(),
                'placeholder' => $this->getPlaceholder(),
                'step' => $this->getStep(),
                'type' => $type,
                'x-mask' . ($mask instanceof RawJs ? ':dynamic' : '') => filled($mask) ? $mask : null,
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                        allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-input',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        ob_start(); ?>

        <div
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= str($state)->replace('"', '\\"') ?>" x-ref="serverState" />

            <div
                x-bind:class="{
                    'fi-disabled': isLoading || <?= Js::from($isDisabled) ?>,
                    'fi-invalid': error !== undefined,
                }"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                x-on:click.prevent.stop
                class="fi-input-wrp"
            >
                <?php if ($hasPrefix) { ?>
                    <div
                        class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content <?= $isPrefixInline ? 'fi-inline' : '' ?> <?= filled($prefixLabel) ? 'fi-input-wrp-prefix-has-label' : '' ?>"
                    >
                        <?= generate_icon_html($prefixIcon, null, (new ComponentAttributeBag)
                            ->color(IconComponent::class, $prefixIconColor))?->toHtml() ?>

                        <?php if (filled($prefixLabel)) { ?>
                            <span class="fi-input-wrp-label">
                                <?= e($prefixLabel) ?>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="fi-input-wrp-content-ctn">
                    <input
                        x-model.lazy="state"
                        <?= $inputAttributes->toHtml() ?>
                    />
                </div>

                <?php if ($hasSuffix) { ?>
                    <div
                        class="fi-input-wrp-suffix <?= $isSuffixInline ? 'fi-inline' : '' ?> <?= filled($suffixLabel) ? 'fi-input-wrp-suffix-has-label' : '' ?>"
                    >
                        <?php if (filled($suffixLabel)) { ?>
                            <span class="fi-input-wrp-label">
                                <?= e($suffixLabel) ?>
                            </span>
                        <?php } ?>

                        <?= generate_icon_html($suffixIcon, null, (new ComponentAttributeBag)
                            ->color(IconComponent::class, $suffixIconColor))?->toHtml() ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
