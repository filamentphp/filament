<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Contracts\CanHaveNumericState;
use Filament\Schemas\Components\Concerns\CanStripCharactersFromState;
use Filament\Schemas\Components\Concerns\CanTrimState;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\NumberStateCast;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\RawJs;
use LogicException;

class TextInput extends Field implements CanHaveNumericState, Contracts\CanBeLengthConstrained, Contracts\HasAffixes, HasEmbeddedView
{
    use CanStripCharactersFromState;
    use CanTrimState;
    use Concerns\CanBeAutocapitalized;
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeLengthConstrained;
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasDatalistOptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasInputMode;
    use Concerns\HasPlaceholder;
    use Concerns\HasStep;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.text-input';

    protected string | RawJs | Closure | null $mask = null;

    protected bool | Closure $isEmail = false;

    protected bool | Closure $isNumeric = false;

    protected bool | Closure $isPassword = false;

    protected bool | Closure $isRevealable = false;

    protected bool | Closure $isCopyable = false;

    protected bool | Closure $isTel = false;

    protected bool | Closure $isUrl = false;

    /**
     * @var scalar | Closure | null
     */
    protected $maxValue = null;

    /**
     * @var scalar | Closure | null
     */
    protected $minValue = null;

    protected string | Closure | null $telRegex = null;

    protected string | Closure | null $type = null;

    public function currentPassword(bool | Closure $condition = true, ?string $guard = null): static
    {
        if (filled($guard)) {
            $this->rule("current_password:{$guard}", $condition);
        } else {
            $this->rule('current_password', $condition);
        }

        return $this;
    }

    public function email(bool | Closure $condition = true): static
    {
        $this->isEmail = $condition;

        $this->rule('email', $condition);

        return $this;
    }

    public function integer(bool | Closure $condition = true): static
    {
        $this->numeric($condition);
        $this->inputMode(static fn (): ?string => $condition ? 'numeric' : null);
        $this->step(static fn (): ?int => $condition ? 1 : null);
        $this->rule('integer', $condition);

        return $this;
    }

    public function mask(string | RawJs | Closure | null $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * @param  scalar | Closure | null  $value
     */
    public function maxValue($value): static
    {
        $this->maxValue = $value;

        $this->rule(static function (TextInput $component): string {
            $value = $component->getMaxValue();

            return "max:{$value}";
        }, static fn (TextInput $component): bool => filled($component->getMaxValue()));

        return $this;
    }

    /**
     * @param  scalar | Closure | null  $value
     */
    public function minValue($value): static
    {
        $this->minValue = $value;

        $this->rule(static function (TextInput $component): string {
            $value = $component->getMinValue();

            return "min:{$value}";
        }, static fn (TextInput $component): bool => filled($component->getMinValue()));

        return $this;
    }

    public function numeric(bool | Closure $condition = true): static
    {
        $this->isNumeric = $condition;

        $this->inputMode(static fn (): ?string => $condition ? 'decimal' : null);
        $this->rule('numeric', $condition);
        $this->step(static fn (): ?string => $condition ? 'any' : null);

        return $this;
    }

    public function password(bool | Closure $condition = true): static
    {
        $this->isPassword = $condition;

        return $this;
    }

    public function revealable(bool | Closure $condition = true): static
    {
        $this->isRevealable = $condition;
        $this->suffixActions([
            TextInput\Actions\ShowPasswordAction::make()->visible($condition),
            TextInput\Actions\HidePasswordAction::make()->visible($condition),
        ]);

        return $this;
    }

    public function isPasswordRevealable(): bool
    {
        if (! $this->evaluate($this->isRevealable)) {
            return false;
        }

        return $this->isPassword() ?: throw new LogicException("The text input [{$this->getStatePath()}] is not a [password()], so it cannot be [revealable()].");
    }

    public function copyable(
        bool | Closure $condition = true,
        string | Closure | null $copyMessage = null,
        int | Closure | null $copyMessageDuration = null
    ): static {
        $this->isCopyable = $condition;

        $this->suffixAction(
            TextInput\Actions\CopyAction::make()
                ->copyMessage($copyMessage)
                ->copyMessageDuration($copyMessageDuration)
                ->visible($condition),
        );

        return $this;
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->isCopyable);
    }

    public function tel(bool | Closure $condition = true): static
    {
        $this->isTel = $condition;

        $this->regex(static fn (TextInput $component) => $component->evaluate($condition) ? $component->getTelRegex() : null);

        return $this;
    }

    public function telRegex(string | Closure | null $regex): static
    {
        $this->telRegex = $regex;

        return $this;
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function url(bool | Closure $condition = true): static
    {
        $this->isUrl = $condition;

        $this->rule('url', $condition);

        return $this;
    }

    public function getMask(): string | RawJs | null
    {
        return $this->evaluate($this->mask);
    }

    /**
     * @return scalar | null
     */
    public function getMaxValue()
    {
        return $this->evaluate($this->maxValue);
    }

    /**
     * @return scalar | null
     */
    public function getMinValue()
    {
        return $this->evaluate($this->minValue);
    }

    public function getType(): string
    {
        if ($type = $this->evaluate($this->type)) {
            return $type;
        } elseif ($this->isEmail()) {
            return 'email';
        } elseif ($this->isNumeric()) {
            return 'number';
        } elseif ($this->isPassword()) {
            return 'password';
        } elseif ($this->isTel()) {
            return 'tel';
        } elseif ($this->isUrl()) {
            return 'url';
        }

        return 'text';
    }

    public function getTelRegex(): string
    {
        return $this->evaluate($this->telRegex) ?? '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/';
    }

    public function isEmail(): bool
    {
        return (bool) $this->evaluate($this->isEmail);
    }

    public function isNumeric(): bool
    {
        return (bool) $this->evaluate($this->isNumeric);
    }

    public function isPassword(): bool
    {
        return (bool) $this->evaluate($this->isPassword);
    }

    public function isTel(): bool
    {
        return (bool) $this->evaluate($this->isTel);
    }

    public function isUrl(): bool
    {
        return (bool) $this->evaluate($this->isUrl);
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            ...($this->hasStripCharacters() ? [app(StripCharactersStateCast::class, ['characters' => $this->getStripCharacters()])] : []),
            ...($this->isNumeric() ? [app(NumberStateCast::class, ['isNullable' => true])] : []),
        ];
    }

    public function toEmbeddedHtml(): string
    {
        $extraAlpineAttributes = $this->getExtraAlpineAttributes();
        $extraAttributeBag = $this->getExtraAttributeBag();
        $id = $this->getId();
        $isConcealed = $this->isConcealed();
        $isDisabled = $this->isDisabled();
        $isPasswordRevealable = $this->isPasswordRevealable();
        $isPrefixInline = $this->isPrefixInline();
        $isSuffixInline = $this->isSuffixInline();
        $mask = $this->getMask();
        $prefixActions = $this->getPrefixActions();
        $prefixIcon = $this->getPrefixIcon();
        $prefixIconColor = $this->getPrefixIconColor();
        $prefixLabel = $this->getPrefixLabel();
        $suffixActions = $this->getSuffixActions();
        $suffixIcon = $this->getSuffixIcon();
        $suffixIconColor = $this->getSuffixIconColor();
        $suffixLabel = $this->getSuffixLabel();
        $statePath = $this->getStatePath();
        $placeholder = $this->getPlaceholder();

        if ($isPasswordRevealable) {
            $xData = '{ isPasswordRevealed: false }';
        } elseif (count($extraAlpineAttributes) || filled($mask)) {
            $xData = '{}';
        } else {
            $xData = null;
        }

        if ($isPasswordRevealable) {
            $type = null;
        } elseif (filled($mask)) {
            $type = 'text';
        } else {
            $type = $this->getType();
        }

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge($extraAlpineAttributes, escape: false)
            ->merge([
                'autocapitalize' => $this->getAutocapitalize(),
                'autocomplete' => $this->getAutocomplete(),
                'autofocus' => $this->isAutofocused(),
                'disabled' => $isDisabled,
                'id' => $id,
                'inlinePrefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                'inlineSuffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                'inputmode' => $this->getInputMode(),
                'list' => ($datalistOptions = $this->getDatalistOptions()) ? $id . '-list' : null,
                'max' => (! $isConcealed) ? $this->getMaxValue() : null,
                'maxlength' => (! $isConcealed) ? $this->getMaxLength() : null,
                'min' => (! $isConcealed) ? $this->getMinValue() : null,
                'minlength' => (! $isConcealed) ? $this->getMinLength() : null,
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'readonly' => $this->isReadOnly(),
                'required' => $this->isRequired() && (! $isConcealed),
                'step' => $this->getStep(),
                'type' => $type,
                $this->applyStateBindingModifiers('wire:model') => $statePath,
                'x-bind:type' => $isPasswordRevealable ? 'isPasswordRevealed ? \'text\' : \'password\'' : null,
                'x-mask' . ($mask instanceof RawJs ? ':dynamic' : '') => filled($mask) ? $mask : null,
            ], escape: false)
            ->class([
                'fi-input',
                'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                'fi-revealable' => $isPasswordRevealable,
            ]);

        $wrapperAttributes = $extraAttributeBag
            ->merge([
                'x-data' => $xData,
                'x-on:focus-input.stop' => "\$el.querySelector('input')?.focus()",
            ], escape: false)
            ->class(['fi-fo-text-input']);

        ob_start(); ?>

        <input <?= $inputAttributes->toHtml() ?> />

        <?php if ($datalistOptions) { ?>
            <datalist id="<?= e($id) ?>-list">
                <?php foreach ($datalistOptions as $option) { ?>
                    <option value="<?= e($option) ?>"></option>
                <?php } ?>
            </datalist>
        <?php } ?>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
        );
    }

    public function mutateDehydratedState(mixed $state): mixed
    {
        $state = $this->trimState($state);

        return parent::mutateDehydratedState($state);
    }

    public function mutateStateForValidation(mixed $state): mixed
    {
        $state = $this->stripCharactersFromState($state);
        $state = $this->trimState($state);

        return parent::mutateStateForValidation($state);
    }

    public function mutatesDehydratedState(): bool
    {
        return parent::mutatesDehydratedState() || $this->isTrimmed();
    }

    public function mutatesStateForValidation(): bool
    {
        return parent::mutatesStateForValidation() || $this->hasStripCharacters() || $this->isTrimmed();
    }
}
