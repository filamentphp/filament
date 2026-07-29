<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Forms\Components\Concerns\CanBeMarkedAsRequired;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;

class Fieldset extends Component implements CanEntangleWithSingularRelationships, HasEmbeddedView
{
    use CanBeContained;
    use CanBeMarkedAsRequired;
    use EntanglesStateWithSingularRelationship;
    use HasLabel;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.fieldset';

    final public function __construct(string | Htmlable | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(2);
    }

    public function isRequired(): bool
    {
        return false;
    }

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $isLabelHidden = $this->isLabelHidden();
        $label = $this->getLabel();
        $isContained = $this->isContained();
        $isRequired = $this->isMarkedAsRequired();

        $attributes = (new FilamentComponentAttributeBag)
            ->merge(['id' => $id], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class([
                'fi-sc-fieldset',
                'fi-fieldset',
                'fi-fieldset-label-hidden' => $isLabelHidden,
                'fi-fieldset-not-contained' => ! $isContained,
            ]);

        ob_start(); ?>
        <fieldset <?= $attributes->toHtml() ?>>
            <?php if (filled($label)) { ?>
                <legend><?= e($label) ?><?php if ($isRequired) { ?><sup class="fi-fieldset-label-required-mark">*</sup><?php } ?></legend>
            <?php } ?>
            <?= $this->getChildSchema()->toHtml() ?>
        </fieldset>
        <?php return ob_get_clean();
    }
}
