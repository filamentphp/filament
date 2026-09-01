<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasDefaultDataFormattingSettings
{
    protected string | Closure $defaultCurrency = 'usd';

    protected string | Closure $defaultDateDisplayFormat = 'M j, Y';

    protected string | Closure $defaultIsoDateDisplayFormat = 'L';

    protected string | Closure $defaultDateTimeDisplayFormat = 'M j, Y H:i:s';

    protected string | Closure $defaultIsoDateTimeDisplayFormat = 'LLL';

    protected string | Closure | null $defaultNumberLocale = null;

    protected string | Closure $defaultTimeDisplayFormat = 'H:i:s';

    protected string | Closure $defaultIsoTimeDisplayFormat = 'LT';

    public function defaultDateDisplayFormat(string | Closure $format): static
    {
        $this->defaultDateDisplayFormat = $format;

        return $this;
    }

    public function defaultDateTimeDisplayFormat(string | Closure $format): static
    {
        $this->defaultDateTimeDisplayFormat = $format;

        return $this;
    }

    public function defaultTimeDisplayFormat(string | Closure $format): static
    {
        $this->defaultTimeDisplayFormat = $format;

        return $this;
    }

    public function defaultIsoDateDisplayFormat(string | Closure $format): static
    {
        $this->defaultIsoDateDisplayFormat = $format;

        return $this;
    }

    public function defaultIsoDateTimeDisplayFormat(string | Closure $format): static
    {
        $this->defaultIsoDateTimeDisplayFormat = $format;

        return $this;
    }

    public function defaultIsoTimeDisplayFormat(string | Closure $format): static
    {
        $this->defaultIsoTimeDisplayFormat = $format;

        return $this;
    }

    public function defaultNumberLocale(string | Closure | null $locale): static
    {
        $this->defaultNumberLocale = $locale;

        return $this;
    }

    public function defaultCurrency(string | Closure $currency): static
    {
        $this->defaultCurrency = $currency;

        return $this;
    }

    public function getDefaultDateDisplayFormat(): string
    {
        return $this->evaluate($this->defaultDateDisplayFormat);
    }

    public function getDefaultDateTimeDisplayFormat(): string
    {
        return $this->evaluate($this->defaultDateTimeDisplayFormat);
    }

    public function getDefaultTimeDisplayFormat(): string
    {
        return $this->evaluate($this->defaultTimeDisplayFormat);
    }

    public function getDefaultIsoDateDisplayFormat(): string
    {
        return $this->evaluate($this->defaultIsoDateDisplayFormat);
    }

    public function getDefaultIsoDateTimeDisplayFormat(): string
    {
        return $this->evaluate($this->defaultIsoDateTimeDisplayFormat);
    }

    public function getDefaultIsoTimeDisplayFormat(): string
    {
        return $this->evaluate($this->defaultIsoTimeDisplayFormat);
    }

    public function getDefaultNumberLocale(): ?string
    {
        return $this->evaluate($this->defaultNumberLocale);
    }

    public function getDefaultCurrency(): string
    {
        return $this->evaluate($this->defaultCurrency);
    }
}
