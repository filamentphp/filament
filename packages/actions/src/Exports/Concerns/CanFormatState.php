<?php

namespace Filament\Actions\Exports\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected int | Closure | null $characterLimit = null;

    protected string | Closure | null $characterLimitEnd = null;

    protected int | Closure | null $wordLimit = null;

    protected string | Closure | null $wordLimitEnd = null;

    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    protected bool $isListedAsJson = false;

    protected bool | Closure | null $shouldPreventFormulaInjection = null;

    public function limit(int | Closure | null $length = 100, string | Closure | null $end = '...'): static
    {
        $this->characterLimit = $length;
        $this->characterLimitEnd = $end;

        return $this;
    }

    public function words(int | Closure | null $words = 100, string | Closure | null $end = '...'): static
    {
        $this->wordLimit = $words;
        $this->wordLimitEnd = $end;

        return $this;
    }

    public function prefix(string | Closure | null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Closure | null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function formatState(mixed $state): mixed
    {
        // Security: Export values are written to CSV/XLSX as-is after
        // formatting. Use `formatStateUsing()` to sanitize values that
        // may trigger formula injection (`=`, `+`, `-`, `@`).

        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        if ($characterLimit = $this->getCharacterLimit()) {
            $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd());
        }

        if ($wordLimit = $this->getWordLimit()) {
            $state = Str::words($state, $wordLimit, $this->getWordLimitEnd());
        }

        $prefix = $this->getPrefix();
        $suffix = $this->getSuffix();

        if (filled($prefix)) {
            $state = $prefix . $state;
        }

        if (filled($suffix)) {
            $state .= $suffix;
        }

        return $state;
    }

    public function preventFormulaInjection(bool | Closure | null $condition = true): static
    {
        $this->shouldPreventFormulaInjection = $condition;

        return $this;
    }

    public function getFormattedState(): ?string
    {
        $state = $this->getState();

        if (! is_array($state)) {
            $state = $this->formatState($state);
        } else {
            $state = array_map($this->formatState(...), $state);

            $state = $this->isListedAsJson()
                ? json_encode($state)
                : implode(', ', $state);
        }

        if ($this->shouldPreventFormulaInjection()) {
            // Security: Neutralize CSV/spreadsheet formula injection
            // (CWE-1236) on the final written value by prefixing a
            // single quote when it starts with a formula trigger.
            $state = $this->sanitizeStateAgainstFormulaInjection($state);
        }

        return $state;
    }

    protected function sanitizeStateAgainstFormulaInjection(mixed $state): mixed
    {
        if (! is_string($state) || ($state === '')) {
            return $state;
        }

        // The empty-string check above guarantees `$state[0]` is a valid byte.
        //
        // A purely numeric string that begins with a sign, such as `-5` or `+42`,
        // is interpreted by spreadsheet software as a number, not a formula, so it
        // is safe to leave unescaped. This avoids corrupting legitimate values
        // (e.g. negative numbers stored as strings). The leading-sign guard keeps
        // this narrow: `is_numeric()` also accepts a leading tab or carriage return
        // (e.g. "\t5"), which are formula triggers that must still be escaped.
        if (in_array($state[0], ['-', '+'], strict: true) && is_numeric($state)) {
            return $state;
        }

        // Security: These are the formula-triggering characters escaped by
        // `League\Csv\EscapeFormula::FORMULA_STARTING_CHARS`, the CSV
        // library's own protection. XLSX exports use OpenSpout, which has
        // no equivalent, so the same set is applied at this shared value
        // layer to cover both formats.
        if (in_array($state[0], ['=', '+', '-', '@', "\t", "\r"], strict: true)) {
            return "'" . $state;
        }

        return $state;
    }

    public function shouldPreventFormulaInjection(): bool
    {
        return (bool) $this->evaluate($this->shouldPreventFormulaInjection);
    }

    public function getCharacterLimit(): ?int
    {
        return $this->evaluate($this->characterLimit);
    }

    public function getCharacterLimitEnd(): ?string
    {
        return $this->evaluate($this->characterLimitEnd);
    }

    public function getWordLimit(): ?int
    {
        return $this->evaluate($this->wordLimit);
    }

    public function getWordLimitEnd(): ?string
    {
        return $this->evaluate($this->wordLimitEnd);
    }

    public function getPrefix(): ?string
    {
        return $this->evaluate($this->prefix);
    }

    public function getSuffix(): ?string
    {
        return $this->evaluate($this->suffix);
    }

    public function listAsJson(bool $condition = true): static
    {
        $this->isListedAsJson = $condition;

        return $this;
    }

    public function isListedAsJson(): bool
    {
        return $this->isListedAsJson;
    }
}
