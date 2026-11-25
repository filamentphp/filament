<?php

namespace Filament\Schemas\Components\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class DateRangeStateCast implements StateCast
{
    public function __construct(
        protected string $format,
        protected string $internalFormat,
        protected ?string $timezone,
    ) {}

    /**
     * @return array{start: ?string, end: ?string} | null
     */
    public function get(mixed $state): ?array
    {
        if ((! is_array($state)) || blank($state['start'] ?? null) || blank($state['end'] ?? null)) {
            return null;
        }

        $dateTimeStateCast = $this->getDateTimeStateCast();

        return [
            'start' => $dateTimeStateCast->get($state['start']),
            'end' => $dateTimeStateCast->get($state['end']),
        ];
    }

    /**
     * @return array{start: ?string, end: ?string} | null
     */
    public function set(mixed $state): ?array
    {
        if ((! is_array($state)) || blank($state['start'] ?? null) || blank($state['end'] ?? null)) {
            return null;
        }

        $dateTimeStateCast = $this->getDateTimeStateCast();

        return [
            'start' => $dateTimeStateCast->set($state['start']),
            'end' => $dateTimeStateCast->set($state['end']),
        ];
    }

    protected function getDateTimeStateCast(): DateTimeStateCast
    {
        return app(DateTimeStateCast::class, [
            'format' => $this->format,
            'internalFormat' => $this->internalFormat,
            'timezone' => $this->timezone,
        ]);
    }
}
