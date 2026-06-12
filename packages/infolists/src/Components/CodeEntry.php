<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
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

        $isCopyable = $this->isCopyable($state);

        $copyableStateJs = $isCopyable
            ? Js::from($this->getCopyableState($state) ?? $state)
            : null;
        $copyMessageJs = $isCopyable
            ? Js::from($this->getCopyMessage($state))
            : null;
        $copyMessageDurationJs = $isCopyable
            ? Js::from($this->getCopyMessageDuration($state))
            : null;

        $attributes = $attributes
            ->merge([
                'x-on:click' => $isCopyable
                    ? <<<JS
                        window.navigator.clipboard.writeText({$copyableStateJs})
                        \$tooltip({$copyMessageJs}, {
                            theme: \$store.theme,
                            timeout: {$copyMessageDurationJs},
                        })
                        JS
                    : null,
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
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
            <?= (string) $phiki->codeToHtml($state, $grammar, [
                'light' => $lightTheme,
                'dark' => $darkTheme,
            ]) ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
