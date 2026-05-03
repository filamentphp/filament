<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanWrap;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Js;

class CodeEditor extends Field implements HasEmbeddedView
{
    use CanWrap;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.code-editor';

    protected Language | Closure | null $language = null;

    public function language(Language | Closure | null $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->evaluate($this->language);
    }

    public function toEmbeddedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $isLive = $this->isLive();
        $isLiveOnBlur = $this->isLiveOnBlur();
        $isLiveDebounced = $this->isLiveDebounced();
        $liveDebounce = $this->getLiveDebounce();
        $language = $this->getLanguage();
        $statePath = $this->getStatePath();
        $livewireKey = $this->getLivewireKey();

        $wrapperAttributes = $this->getExtraAttributeBag()
            ->class(['fi-fo-code-editor']);

        ob_start(); ?>

        <div
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('code-editor', 'filament/forms')) ?>"
            x-data="codeEditorFormComponent({
                        canWrap: <?= Js::from($this->canWrap()) ?>,
                        isDisabled: <?= Js::from($isDisabled) ?>,
                        isLive: <?= Js::from($isLive) ?>,
                        isLiveDebounced: <?= Js::from($isLiveDebounced) ?>,
                        isLiveOnBlur: <?= Js::from($isLiveOnBlur) ?>,
                        liveDebounce: <?= Js::from($liveDebounce) ?>,
                        language: <?= Js::from($language?->value) ?>,
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) ?>,
                    })"
            wire:ignore
            wire:key="<?= e($livewireKey) ?>.<?= substr(md5(serialize([$isDisabled, $language?->value])), 0, 64) ?>"
            <?= $this->getExtraAlpineAttributeBag()->toHtml() ?>
        >
            <div x-ref="editor" x-cloak></div>
        </div>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapFieldHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
                isDisabled: $isDisabled,
                isValid: ! $this->hasErrorForPath($statePath),
            ),
        );
    }
}
