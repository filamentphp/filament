<?php

namespace Filament\Forms\Components;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Support\HtmlString;

class Checkbox extends Field implements HasEmbeddedView
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasExtraInputAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.checkbox';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->rule('boolean');
    }

    public function toEmbeddedHtml(): string
    {
        $statePath = $this->getStatePath();
        $hasError = $this->hasErrorForPath($statePath);

        $attributes = (new FilamentComponentAttributeBag)
            ->merge([
                'autofocus' => $this->isAutofocused(),
                'disabled' => $this->isDisabled(),
                'id' => $this->getId(),
                'required' => $this->isRequired() && (! $this->isConcealed()),
                'wire:loading.attr' => 'disabled',
                $this->applyStateBindingModifiers('wire:model') => $statePath,
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->merge($this->getExtraInputAttributes(), escape: false)
            ->class([
                'fi-checkbox-input',
                'fi-valid' => ! $hasError,
                'fi-invalid' => $hasError,
            ]);

        $inputHtml = '<input type="checkbox" ' . $attributes->toHtml() . ' />';

        if ($this->isInline()) {
            return $this->wrapEmbeddedHtml(
                '',
                labelPrefix: new HtmlString($inputHtml),
                inlineLabelVerticalAlignment: VerticalAlignment::Center,
            );
        }

        return $this->wrapEmbeddedHtml(
            $inputHtml,
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(BooleanStateCast::class, ['isNullable' => false]),
        ];
    }
}
