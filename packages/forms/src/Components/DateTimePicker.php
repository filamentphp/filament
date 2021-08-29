<?php

namespace Filament\Forms\Components;

class DateTimePicker extends DatePicker
{
    protected $defaultDisplayFormat = 'F j, Y H:i:s';

    protected $defaultDisplayFormatWithoutSeconds = 'F j, Y H:i';

    protected $defaultFormat = 'Y-m-d H:i:s';

    protected $defaultFormatWithoutSeconds = 'Y-m-d H:i';

    protected $hasTime = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->displayFormat($this->defaultDisplayFormat);

        $this->format($this->defaultFormat);
    }

    public function withoutSeconds(bool | callable $condition = true): static
    {
        $this->hasSeconds = fn (): bool => ! $this->evaluate($condition);

        return $this;
    }

    public function hasSeconds(): bool
    {
        $hasSeconds = $this->evaluate($this->hasSeconds);

        if (! $hasSeconds) {
            if ($this->getDisplayFormat() === $this->defaultDisplayFormat) {
                $this->displayFormat($this->defaultDisplayFormatWithoutSeconds);
            }

            if ($this->getFormat() === $this->defaultFormat) {
                $this->format($this->defaultFormatWithoutSeconds);
            }
        }

        return (bool) $hasSeconds;
    }
}
