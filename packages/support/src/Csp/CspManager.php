<?php

namespace Filament\Support\Csp;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\HtmlString;

class CspManager
{
    use EvaluatesClosures;

    protected string | Closure | null $nonce = null;

    protected ?string $resolvedNonce = null;

    protected bool $hasResolvedNonce = false;

    public function useNonce(string | Closure | null $nonce = null): void
    {
        $this->nonce = $nonce;

        $this->resolvedNonce = null;
        $this->hasResolvedNonce = false;
    }

    /**
     * The nonce is resolved only once, so that every element rendered during a
     * request receives the same value. A `Closure` that returned a new nonce
     * for each element would cause the browser to reject all but the first.
     */
    public function getNonce(): ?string
    {
        if ($this->hasResolvedNonce) {
            return $this->resolvedNonce;
        }

        $this->hasResolvedNonce = true;

        return $this->resolvedNonce = $this->evaluate($this->nonce);
    }

    public function hasNonce(): bool
    {
        return filled($this->getNonce());
    }

    /**
     * Renders a `nonce` attribute, including the leading space, or an empty
     * string when no nonce is configured. Returning the whole attribute allows
     * templates to remain byte-for-byte identical when CSP is not in use.
     */
    public function getNonceHtml(): HtmlString
    {
        $nonce = $this->getNonce();

        if (blank($nonce)) {
            return new HtmlString('');
        }

        return new HtmlString(' nonce="' . e($nonce) . '"');
    }
}
