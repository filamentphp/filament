<?php

namespace Filament\Schemas\Components\Wizard;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Wizard;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class Step extends Component implements CanConcealComponents, HasEmbeddedView
{
    use HasLabel;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.wizard.step';

    protected ?Closure $afterValidation = null;

    protected ?Closure $beforeValidation = null;

    protected string | Closure | null $description = null;

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

    protected string | BackedEnum | Htmlable | Closure | null $completedIcon = null;

    protected bool | Closure $hasFormWrapper = true;

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
        if ($this->columns === null) {
            return $this->getContainer()->getAllColumns();
        }

        return parent::getAllColumns();
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

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $key = $this->getKey();
        /** @var Wizard $wizard */
        $wizard = $this->getContainer()->getParentComponent();
        $alpineSubmitHandler = $this->hasFormWrapper() ? $wizard->getAlpineSubmitHandler() : null;

        $tag = filled($alpineSubmitHandler) ? 'form' : 'div';

        // The header button carrying `id="{$id}-tab"` only renders when the wizard header is shown, so a
        // `hiddenHeader()` wizard has no such element — reference it only when it exists, otherwise the
        // `role="group"` panel would point `aria-labelledby` at a nonexistent id and lose its name.
        $hasHeaderReference = filled($id) && ! $wizard->isHeaderHidden();

        $label = $this->getLabel();
        $labelText = $label instanceof Htmlable ? strip_tags($label->toHtml()) : $label;

        $attributes = (new FilamentComponentAttributeBag)
            ->merge([
                // Name the panel by its header button (`{id}-tab`) rather than itself. The header is an `<ol>`
                // stepper of plain buttons with `aria-current="step"`, not a `tablist` of `role="tab"` controls,
                // so `role="tabpanel"` (which implies an owning tab) is incoherent — `role="group"` is honest.
                // When the header is hidden, fall back to the step's own label so the panel keeps a name.
                'aria-labelledby' => $hasHeaderReference ? "{$id}-tab" : null,
                'aria-label' => (! $hasHeaderReference && filled($labelText)) ? e($labelText) : null,
                'id' => $id,
                'role' => 'group',
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class(['fi-sc-wizard-step']);

        ob_start(); ?>

        <<?= $tag ?>
            x-bind:tabindex="$el.querySelector('[autofocus]') ? '-1' : '0'"
            x-bind:class="{
                'fi-active': step === <?= Js::from($key) ?>,
            }"
            x-on:expand="
                if (! isStepAccessible(<?= Js::from($key) ?>)) {
                    return
                }

                step = <?= Js::from($key) ?>
            "
            <?php if (filled($alpineSubmitHandler)) { ?>
                x-on:submit.prevent="isLastStep() ? <?= $alpineSubmitHandler ?> : requestNextStep()"
            <?php } ?>
            x-cloak
            x-ref="step-<?= e($key) ?>"
            <?= $attributes->toHtml() ?>
        >
            <?= $this->getChildSchema()->toHtml() ?>

            <?php if (filled($alpineSubmitHandler)) { ?>
                <input type="submit" hidden />
            <?php } ?>
        </<?= $tag ?>>

        <?php return ob_get_clean();
    }
}
