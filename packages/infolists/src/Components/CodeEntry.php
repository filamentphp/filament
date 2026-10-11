<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
use Filament\Support\View\ComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;

class CodeEntry extends Entry implements HasEmbeddedView
{
    use CanBeCopied;

    protected string | Grammar | Closure | null $grammar = null;

    protected string | Theme | Closure | null $lightTheme = null;

    protected string | Theme | Closure | null $darkTheme = null;

    protected int | Closure $jsonFlags = JSON_PRETTY_PRINT;

    public function grammar(string | Grammar | Closure | null $grammar): static
    {
        $this->grammar = $grammar;

        return $this;
    }

    public function getGrammar(): string | Grammar | null
    {
        return $this->evaluate($this->grammar);
    }

    public function lightTheme(string | Theme | Closure | null $theme): static
    {
        $this->lightTheme = $theme;

        return $this;
    }

    public function getLightTheme(): string | Theme | null
    {
        return $this->evaluate($this->lightTheme);
    }

    public function darkTheme(string | Theme | Closure | null $theme): static
    {
        $this->darkTheme = $theme;

        return $this;
    }

    public function getDarkTheme(): string | Theme | null
    {
        return $this->evaluate($this->darkTheme);
    }

    public function jsonFlags(int | Closure $flags): static
    {
        $this->jsonFlags = $flags;

        return $this;
    }

    public function getJsonFlags(): int
    {
        return $this->evaluate($this->jsonFlags);
    }

    public function toEmbeddedHtml(): string
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-in-code',
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
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        $phiki = new Phiki;
        $grammar = $this->getGrammar();
        $lightTheme = $this->getLightTheme();
        $darkTheme = $this->getDarkTheme();

        if (is_array($state)) {
            $state = json_encode($state, flags: $this->getJsonFlags());
            $grammar ??= Grammar::Json;
        }

        $grammar ??= Grammar::Html;
        $lightTheme ??= Theme::GithubLight;
        $darkTheme ??= Theme::GithubDarkHighContrast;

        $relatedRecord = $this->getRelatedRecord();
        $isCopyable = $this->isCopyable($state, $relatedRecord);

        $copyableStateJs = $isCopyable
            ? Js::from($this->getCopyableState($state, $relatedRecord) ?? $state)
            : null;
        $copyMessageJs = $isCopyable
            ? Js::from($this->getCopyMessage($state, $relatedRecord))
            : null;
        $copyMessageDurationJs = $isCopyable
            ? Js::from($this->getCopyMessageDuration($state, $relatedRecord))
            : null;

        if ($isCopyable) {
            $label = $this->getLabel();
            $copyLabelJs = Js::from(__('filament::components/copyable.label', [
                'label' => ($label instanceof Htmlable) ? html_entity_decode(strip_tags($label->toHtml()), ENT_QUOTES | ENT_HTML5) : ($label ?? $this->getName()),
            ]));
            $copyFailureMessageJs = Js::from(__('filament::components/copyable.messages.failed'));
        }

        $hasCustomAlpineScope = $isCopyable && ($attributes->has('x-data') || $attributes->has('x-bind') || $attributes->has('x-id'));

        $copyableAttributes = (new ComponentAttributeBag)
            ->merge([
                'x-data' => $isCopyable ? 'filamentCopyable' : null,
                'x-bind' => $isCopyable ? 'bindings' : null,
                'x-id' => $isCopyable ? "['copyable-code']" : null,
                'x-bind:aria-label' => $isCopyable ? "isStandalone ? {$copyLabelJs} : null" : null,
                'x-bind:aria-describedby' => $isCopyable ? "isStandalone ? \$id('copyable-code') : null" : null,
                'x-on:click' => $isCopyable
                    ? <<<JS
                        if (isStandalone) {
                            copy({$copyableStateJs}, {$copyMessageJs}, {$copyMessageDurationJs}, {$copyFailureMessageJs})
                        } else {
                            window.navigator.clipboard.writeText({$copyableStateJs})
                            \$tooltip({$copyMessageJs}, {
                                theme: \$store.theme,
                                timeout: {$copyMessageDurationJs},
                            })
                        }
                        JS
                    : null,
            ], escape: false);

        if (! $hasCustomAlpineScope) {
            $attributes = $attributes->merge($copyableAttributes->getAttributes(), escape: false);
        }

        $attributes = $attributes
            ->merge([
                'x-tooltip' => filled($tooltip = $this->getTooltip($state, $relatedRecord))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                        allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-copyable' => $isCopyable,
            ]);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php if ($hasCustomAlpineScope) { ?>
                <div <?= $copyableAttributes->class(['fi-copyable'])->toHtml() ?>>
            <?php } ?>

            <?php if ($isCopyable) { ?>
                <div x-bind:id="$id('copyable-code')">
            <?php } ?>

            <?= (string) $phiki->codeToHtml($state, $grammar, [
                'light' => $lightTheme,
                'dark' => $darkTheme,
            ]) ?>

            <?php if ($isCopyable) { ?>
                </div>
            <?php } ?>

            <?php if ($hasCustomAlpineScope) { ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
