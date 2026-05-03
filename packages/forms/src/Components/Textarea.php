<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableGrammarly;
use Filament\Schemas\Components\Concerns\CanStripCharactersFromState;
use Filament\Schemas\Components\Concerns\CanTrimState;
use Filament\Schemas\Components\StateCasts\StripCharactersStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Js;

class Textarea extends Field implements Contracts\CanBeLengthConstrained, HasEmbeddedView
{
    use CanDisableGrammarly;
    use CanStripCharactersFromState;
    use CanTrimState;
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeLengthConstrained;
    use Concerns\CanBeReadOnly;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.textarea';

    protected int | Closure | null $cols = null;

    protected int | Closure | null $rows = null;

    protected bool | Closure $shouldAutosize = false;

    public function autosize(bool | Closure $condition = true): static
    {
        $this->shouldAutosize = $condition;

        return $this;
    }

    public function cols(int | Closure | null $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    public function rows(int | Closure | null $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getCols(): ?int
    {
        return $this->evaluate($this->cols);
    }

    public function getRows(): ?int
    {
        return $this->evaluate($this->rows);
    }

    public function shouldAutosize(): bool
    {
        return (bool) $this->evaluate($this->shouldAutosize);
    }

    public function toEmbeddedHtml(): string
    {
        $isConcealed = $this->isConcealed();
        $isDisabled = $this->isDisabled();
        $rows = $this->getRows();
        $placeholder = $this->getPlaceholder();
        $shouldAutosize = $this->shouldAutosize();
        $statePath = $this->getStatePath();

        $initialHeight = (($rows ?? 2) * 1.5) + 0.75;

        $wrapperAttributes = $this->getExtraAttributeBag()
            ->class([
                'fi-fo-textarea',
                'fi-autosizable' => $shouldAutosize,
            ]);

        $textareaAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'autocomplete' => $this->getAutocomplete(),
                'autofocus' => $this->isAutofocused(),
                'cols' => $this->getCols(),
                'disabled' => $isDisabled,
                'id' => $this->getId(),
                'maxlength' => (! $isConcealed) ? $this->getMaxLength() : null,
                'minlength' => (! $isConcealed) ? $this->getMinLength() : null,
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'readonly' => $this->isReadOnly(),
                'required' => $this->isRequired() && (! $isConcealed),
                'rows' => $rows,
                $this->applyStateBindingModifiers('wire:model') => $statePath,
            ], escape: false);

        $alpineAttributes = $this->getExtraAlpineAttributeBag();

        ob_start(); ?>

        <div wire:ignore.self style="height: <?= e($initialHeight) ?>rem">
            <textarea
                x-load
                x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('textarea', 'filament/forms')) ?>"
                x-data="textareaFormComponent({
                            initialHeight: <?= Js::from($initialHeight) ?>,
                            shouldAutosize: <?= Js::from($shouldAutosize) ?>,
                            state: $wire.$entangle('<?= e($statePath) ?>'),
                        })"
                <?php if ($shouldAutosize) { ?>
                    x-intersect.once="resize()"
                    x-on:resize.window="resize()"
                <?php } ?>
                x-model="state"
                <?php if ($this->isGrammarlyDisabled()) { ?>
                    data-gramm="false"
                    data-gramm_editor="false"
                    data-enable-grammarly="false"
                <?php } ?>
                <?= $alpineAttributes->toHtml() ?>
                <?= $textareaAttributes->toHtml() ?>
            ></textarea>
        </div>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            extraWrapperAttributes: ['class' => 'fi-fo-textarea-wrp'],
        );
    }

    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            ...($this->hasStripCharacters() ? [app(StripCharactersStateCast::class, ['characters' => $this->getStripCharacters()])] : []),
        ];
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
