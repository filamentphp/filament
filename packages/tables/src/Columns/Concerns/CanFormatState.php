<?php

namespace Filament\Tables\Columns\Concerns;

use BackedEnum;
use Closure;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Support\Concerns\CanConfigureCommonMark;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Support\Enums\ArgumentValue;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Stringable;

trait CanFormatState
{
    use CanConfigureCommonMark;

    protected ?Closure $formatStateUsing = null;

    protected int | Closure | null $characterLimit = null;

    protected string | Closure | null $characterLimitEnd = null;

    protected int | Closure | null $wordLimit = null;

    protected string | Closure | null $wordLimitEnd = null;

    protected string | Htmlable | Closure | null $prefix = null;

    protected string | Htmlable | Closure | null $suffix = null;

    protected string | Closure | null $timezone = null;

    protected bool | Closure $isHtml = false;

    protected bool | Closure $isMarkdown = false;

    protected bool $isDate = false;

    protected bool $isDateTime = false;

    protected bool $isMoney = false;

    protected bool $isNumeric = false;

    protected bool $isTime = false;

    public function markdown(bool | Closure $condition = true): static
    {
        // Security: Markdown is converted to HTML and then sanitized via
        // `Str::sanitizeHtml()`. Same inline `style` caveat as `html()`.

        $this->isMarkdown = $condition;

        return $this;
    }

    public function date(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDate = true;

        $this->formatStateUsing(static function (TextColumn $column, $state, ?Model $relatedRecord) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->translatedFormat($column->evaluate($format, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultDateDisplayFormat());
        });

        return $this;
    }

    public function dateTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultDateTimeDisplayFormat();

        $this->date($format, $timezone);

        return $this;
    }

    public function isoDate(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDate = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateDisplayFormat();

        $this->formatStateUsing(static function (TextColumn $column, $state, ?Model $relatedRecord) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->isoFormat($column->evaluate($format, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultIsoDateDisplayFormat());
        });

        return $this;
    }

    public function isoDateTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateTimeDisplayFormat();

        $this->isoDate($format, $timezone);

        return $this;
    }

    public function since(string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $this->formatStateUsing(static function (TextColumn $column, $state, ?Model $relatedRecord) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->diffForHumans();
        });

        return $this;
    }

    public function dateTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->tooltip(static function (TextColumn $column, mixed $state, ?Model $relatedRecord) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->translatedFormat($column->evaluate($format, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultDateDisplayFormat());
        });

        return $this;
    }

    public function dateTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultDateTimeDisplayFormat();

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function timeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultTimeDisplayFormat();

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function sinceTooltip(string | Closure | null $timezone = null): static
    {
        $this->tooltip(static function (TextColumn $column, mixed $state, ?Model $relatedRecord) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->diffForHumans();
        });

        return $this;
    }

    public function isoDateTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateDisplayFormat();

        $this->tooltip(static function (TextColumn $column, mixed $state, ?Model $relatedRecord) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTimezone($state, $relatedRecord))
                ->isoFormat($column->evaluate($format, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultIsoDateDisplayFormat());
        });

        return $this;
    }

    public function isoDateTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateTimeDisplayFormat();

        $this->isoDateTooltip($format, $timezone);

        return $this;
    }

    public function isoTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoTimeDisplayFormat();

        $this->isoDateTooltip($format, $timezone);

        return $this;
    }

    public function money(string | BackedEnum | Closure | null $currency = null, int | Closure $divideBy = 0, string | BackedEnum | Closure | null $locale = null, int | Closure | null $decimalPlaces = null): static
    {
        $this->isMoney = true;

        $this->formatStateUsing(static function (TextColumn $column, $state, ?Model $relatedRecord) use ($currency, $divideBy, $locale, $decimalPlaces): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $currency = $column->evaluate($currency, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultCurrency();
            $locale = $column->evaluate($locale, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultNumberLocale() ?? config('app.locale');
            $decimalPlaces = $column->evaluate($decimalPlaces, ['state' => $state, 'relatedRecord' => $relatedRecord]);

            if ($divideBy = $column->evaluate($divideBy, ['state' => $state, 'relatedRecord' => $relatedRecord])) {
                $state /= $divideBy;
            }

            if ($currency instanceof BackedEnum) {
                $currency = (string) $currency->value;
            }

            if ($locale instanceof BackedEnum) {
                $locale = (string) $locale->value;
            }

            return Number::currency($state, $currency, $locale, $decimalPlaces);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null | ArgumentValue $decimalSeparator = ArgumentValue::Default, string | Closure | null | ArgumentValue $thousandsSeparator = ArgumentValue::Default, int | Closure | null $maxDecimalPlaces = null, string | BackedEnum | Closure | null $locale = null): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(static function (TextColumn $column, $state, ?Model $relatedRecord) use ($decimalPlaces, $decimalSeparator, $locale, $maxDecimalPlaces, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $decimalPlaces = $column->evaluate($decimalPlaces, ['state' => $state, 'relatedRecord' => $relatedRecord]);
            $decimalSeparator = $column->evaluate($decimalSeparator, ['state' => $state, 'relatedRecord' => $relatedRecord]);
            $thousandsSeparator = $column->evaluate($thousandsSeparator, ['state' => $state, 'relatedRecord' => $relatedRecord]);

            if (
                ($decimalSeparator !== ArgumentValue::Default) ||
                ($thousandsSeparator !== ArgumentValue::Default)
            ) {
                return number_format(
                    $state,
                    $decimalPlaces,
                    $decimalSeparator === ArgumentValue::Default ? '.' : $decimalSeparator,
                    $thousandsSeparator === ArgumentValue::Default ? ',' : $thousandsSeparator,
                );
            }

            $locale = $column->evaluate($locale, ['state' => $state, 'relatedRecord' => $relatedRecord]) ?? $column->getTable()->getDefaultNumberLocale() ?? config('app.locale');

            if ($locale instanceof BackedEnum) {
                $locale = (string) $locale->value;
            }

            return Number::format($state, $decimalPlaces, $column->evaluate($maxDecimalPlaces, ['state' => $state, 'relatedRecord' => $relatedRecord]), $locale);
        });

        return $this;
    }

    public function time(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultTimeDisplayFormat();

        $this->date($format, $timezone);

        return $this;
    }

    public function isoTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoTimeDisplayFormat();

        $this->isoDate($format, $timezone);

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

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

    public function prefix(string | Htmlable | Closure | null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Htmlable | Closure | null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function html(bool | Closure $condition = true): static
    {
        // Security: Content is automatically sanitized via Symfony's
        // `HtmlSanitizer`. The default config permits inline `style`
        // attributes, which can enable CSS-based attacks (e.g.
        // `background: url(...)`). Configure a custom sanitizer
        // if rendering untrusted user content.

        $this->isHtml = $condition;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function formatState(mixed $state, ?Model $relatedRecord = null): mixed
    {
        if (! $this->hasStateFormatting()) {
            if ($state instanceof LabelInterface) {
                return $state->getLabel();
            }

            return $state;
        }

        $stateItem = $state;
        $isHtml = $this->isHtml($stateItem, $relatedRecord);

        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $stateItem,
            'relatedRecord' => $relatedRecord,
        ]);

        if (is_array($state)) {
            $state = json_encode($state);
        }

        // Preserve existing `Stringable` rendering and plain-mode label handling.
        if (
            $isHtml &&
            ($state instanceof LabelInterface) &&
            (! ($state instanceof Htmlable)) &&
            (! ($state instanceof Stringable))
        ) {
            $state = $state->getLabel();
        }

        if ($state instanceof RichContentAttribute) {
            $isHtml = true;
            $state = Str::sanitizeHtml($state->toHtml());
        } elseif ($state instanceof Htmlable) {
            $isHtml = true;
            $state = $state->toHtml();
        } elseif ($isHtml) {
            $state ??= '';

            if ($this->isMarkdown($stateItem, $relatedRecord)) {
                $state = Str::markdown(
                    $state,
                    $this->getCommonMarkOptions($stateItem, $relatedRecord),
                    $this->getCommonMarkExtensions($stateItem, $relatedRecord),
                );
            }

            $state = Str::sanitizeHtml($state);
        }

        if ($state instanceof LabelInterface) {
            $state = $state->getLabel();
        }

        if (! $isHtml) {
            if ($characterLimit = $this->getCharacterLimit($stateItem, $relatedRecord)) {
                $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd($stateItem, $relatedRecord));
            }

            if ($wordLimit = $this->getWordLimit($stateItem, $relatedRecord)) {
                $state = Str::words($state, $wordLimit, $this->getWordLimitEnd($stateItem, $relatedRecord));
            }
        }

        $prefix = $this->getPrefix($stateItem, $relatedRecord);
        $suffix = $this->getSuffix($stateItem, $relatedRecord);

        if (
            (($prefix instanceof Htmlable) || ($suffix instanceof Htmlable)) &&
            (! $isHtml)
        ) {
            $isHtml = true;
            $state = e($state);
        }

        if (filled($prefix)) {
            if ($prefix instanceof Htmlable) {
                $prefix = $prefix->toHtml();
            } elseif ($isHtml) {
                $prefix = e($prefix);
            }

            $state = $prefix . $state;
        }

        if (filled($suffix)) {
            if ($suffix instanceof Htmlable) {
                $suffix = $suffix->toHtml();
            } elseif ($isHtml) {
                $suffix = e($suffix);
            }

            $state .= $suffix;
        }

        return $isHtml ? new HtmlString($state) : $state;
    }

    public function getCharacterLimit(mixed $state = null, ?Model $relatedRecord = null): ?int
    {
        return $this->evaluate($this->characterLimit, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function getCharacterLimitEnd(mixed $state = null, ?Model $relatedRecord = null): ?string
    {
        return $this->evaluate($this->characterLimitEnd, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function getWordLimit(mixed $state = null, ?Model $relatedRecord = null): ?int
    {
        return $this->evaluate($this->wordLimit, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function getWordLimitEnd(mixed $state = null, ?Model $relatedRecord = null): ?string
    {
        return $this->evaluate($this->wordLimitEnd, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function getTimezone(mixed $state = null, ?Model $relatedRecord = null): string
    {
        return $this->evaluate($this->timezone, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0)) ?? ($this->isDateTime() ? FilamentTimezone::get() : config('app.timezone'));
    }

    public function isHtml(mixed $state = null, ?Model $relatedRecord = null): bool
    {
        $hasState = func_num_args() > 0;

        return $this->evaluate($this->isHtml, $this->getStateEvaluationParameters($state, $relatedRecord, $hasState)) || ($hasState ? $this->isMarkdown($state, $relatedRecord) : $this->isMarkdown());
    }

    public function getPrefix(mixed $state = null, ?Model $relatedRecord = null): string | Htmlable | null
    {
        return $this->evaluate($this->prefix, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function getSuffix(mixed $state = null, ?Model $relatedRecord = null): string | Htmlable | null
    {
        return $this->evaluate($this->suffix, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function isMarkdown(mixed $state = null, ?Model $relatedRecord = null): bool
    {
        return (bool) $this->evaluate($this->isMarkdown, $this->getStateEvaluationParameters($state, $relatedRecord, hasState: func_num_args() > 0));
    }

    public function isDate(): bool
    {
        return $this->isDate;
    }

    public function isDateTime(): bool
    {
        return $this->isDateTime;
    }

    public function isMoney(): bool
    {
        return $this->isMoney;
    }

    public function isNumeric(): bool
    {
        return $this->isNumeric;
    }

    public function isTime(): bool
    {
        return $this->isTime;
    }

    /**
     * Returns true if any state-formatting property is configured. Used to
     * decide whether to call `formatState()` or emit the raw state directly.
     * When adding a new state-formatting setter, ensure it is reflected here.
     */
    public function hasStateFormatting(): bool
    {
        return $this->formatStateUsing !== null
            || $this->characterLimit !== null
            || $this->wordLimit !== null
            || $this->prefix !== null
            || $this->suffix !== null
            || $this->isHtml !== false
            || $this->isMarkdown !== false
            || $this->isMoney
            || $this->isDate
            || $this->isDateTime
            || $this->isNumeric
            || $this->isTime;
    }
}
