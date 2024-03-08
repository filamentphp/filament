<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Contracts\CanHaveNumericState;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\RawJs;

class TextInput extends Field implements CanHaveNumericState, Contracts\CanBeLengthConstrained, Contracts\HasAffixActions
{
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

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.text-input';

    protected string | RawJs | Closure | null $mask = null;

    protected bool | Closure $isEmail = false;

    protected bool | Closure $isNumeric = false;

    protected bool | Closure $isPassword = false;

    protected bool | Closure $isRevealable = false;

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

    public function currentPassword(bool | Closure $condition = true): static
    {
        $this->rule('current_password', $condition);

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

        return $this->isPassword() ?: throw new Exception("The text input [{$this->getStatePath()}] is not a [password()], so it cannot be [revealable()].");
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
}
