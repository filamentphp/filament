<?php

namespace Filament\Schemas\Components\Wizard;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Step extends Component implements CanConcealComponents
{
    use HasLabel;

    protected ?Closure $afterValidation = null;

    protected ?Closure $beforeValidation = null;

    protected string | Closure | null $description = null;

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

    protected string | BackedEnum | Htmlable | Closure | null $completedIcon = null;

    protected bool | Closure $hasFormWrapper = true;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.wizard.step';

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(function (Step $component): string {
            $label = $component->getLabel();
            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($label, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::wizard-step" : 'wizard-step');
        }, isInheritable: false);
    }

    public function afterValidation(?Closure $callback): static
    {
        $this->afterValidation = $callback;

        return $this;
    }

    /**
     * @deprecated Use `afterValidation()` instead.
     */
    public function afterValidated(?Closure $callback): static
    {
        $this->afterValidation($callback);

        return $this;
    }

    public function beforeValidation(?Closure $callback): static
    {
        $this->beforeValidation = $callback;

        return $this;
    }

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function completedIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->completedIcon = $icon;

        return $this;
    }

    public function callAfterValidation(): void
    {
        $this->evaluate($this->afterValidation);
    }

    public function callBeforeValidation(): void
    {
        $this->evaluate($this->beforeValidation);
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->icon);
    }

    public function getCompletedIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->completedIcon);
    }

    /**
     * @return array<string, int | null>
     */
    public function getAllColumns(): array
    {
        return $this->columns ?? $this->getContainer()->getAllColumns();
    }

    public function canConcealComponents(): bool
    {
        return true;
    }

    public function formWrapper(bool | Closure $condition = true): static
    {
        $this->hasFormWrapper = $condition;

        return $this;
    }

    public function hasFormWrapper(): bool
    {
        return (bool) $this->evaluate($this->hasFormWrapper);
    }
}
