<?php

namespace Filament\Forms\Components;

use BackedEnum;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use DateTime;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\DateTimeStateCast;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\View\ComponentAttributeBag;

class DateTimePicker extends Field implements HasAffixActions
{
    use Concerns\CanBeNative;
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasDatalistOptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use Concerns\HasStep;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.date-time-picker';

    protected string | Closure | null $displayFormat = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraTriggerAttributes = [];

    protected ?int $firstDayOfWeek = null;

    protected string | Closure | null $format = null;

    protected bool | Closure $hasDate = true;

    protected bool | Closure $hasSeconds = true;

    protected bool | Closure $hasTime = true;

    protected bool | Closure $shouldCloseOnDateSelection = false;

    protected CarbonInterface | string | Closure | null $maxDate = null;

    protected CarbonInterface | string | Closure | null $minDate = null;

    protected CarbonInterface | string | Closure | null $defaultFocusedDate = null;

    protected string | Closure | null $timezone = null;

    protected string | Closure | null $locale = null;

    /**
     * @var array<DateTime | string> | Closure
     */
    protected array | Closure $disabledDates = [];

    protected string | Closure $defaultDateDisplayFormat = 'M j, Y';

    protected string | Closure $defaultDateTimeDisplayFormat = 'M j, Y H:i';

    protected string | Closure $defaultDateTimeWithSecondsDisplayFormat = 'M j, Y H:i:s';

    protected string | Closure $defaultTimeDisplayFormat = 'H:i';

    protected string | Closure $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    protected int | Closure | null $hoursStep = null;

    protected int | Closure | null $minutesStep = null;

    protected int | Closure | null $secondsStep = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule(
            'date',
            static fn (DateTimePicker $component): bool => $component->hasDate(),
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(DateTimeStateCast::class, [
                'format' => $this->getFormat(),
                'internalFormat' => $this->getInternalFormat(),
                'timezone' => $this->getTimezone(),
            ]),
        ];
    }

    public function getInternalFormat(): string
    {
        if (! $this->isNative()) {
            return 'Y-m-d H:i:s';
        }

        if (! $this->hasTime()) {
            return 'Y-m-d';
        }

        if (! $this->hasDate()) {
            return $this->hasSeconds() ? 'H:i:s' : 'H:i';
        }

        return $this->hasSeconds() ? 'Y-m-d H:i:s' : 'Y-m-d H:i';
    }

    public function displayFormat(string | Closure | null $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraTriggerAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraAttributes[] = $attributes;
        } else {
            $this->extraAttributes = [$attributes];
        }

        return $this;
    }

    public function firstDayOfWeek(?int $day): static
    {
        if ($day < 0 || $day > 7) {
            $day = null;
        }

        $this->firstDayOfWeek = $day;

        return $this;
    }

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @deprecated Use `suffixIcon(Heroicon::Calendar)` instead.
     */
    public function icon(string | BackedEnum | bool | null $icon = null): static
    {
        if ($icon === false) {
            return $this;
        }

        return $this->suffixIcon($icon ?? Heroicon::Calendar, isInline: true);
    }

    public function maxDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->maxDate = $date;

        $this->rule(static function (DateTimePicker $component) {
            return "before_or_equal:{$component->getMaxDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMaxDate());

        return $this;
    }

    public function minDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->minDate = $date;

        $this->rule(static function (DateTimePicker $component) {
            return "after_or_equal:{$component->getMinDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMinDate());

        return $this;
    }

    public function defaultFocusedDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->defaultFocusedDate = $date;

        return $this;
    }

    /**
     * @param  array<DateTime | string> | Closure  $dates
     */
    public function disabledDates(array | Closure $dates): static
    {
        $this->disabledDates = $dates;

        return $this;
    }

    public function resetFirstDayOfWeek(): static
    {
        $this->firstDayOfWeek(null);

        return $this;
    }

    public function hoursStep(int | Closure | null $hoursStep): static
    {
        $this->hoursStep = $hoursStep;

        return $this;
    }

    public function minutesStep(int | Closure | null $minutesStep): static
    {
        $this->minutesStep = $minutesStep;

        return $this;
    }

    public function secondsStep(int | Closure | null $secondsStep): static
    {
        $this->secondsStep = $secondsStep;

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function locale(string | Closure | null $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function weekStartsOnMonday(): static
    {
        $this->firstDayOfWeek(1);

        return $this;
    }

    public function weekStartsOnSunday(): static
    {
        $this->firstDayOfWeek(7);

        return $this;
    }

    public function date(bool | Closure $condition = true): static
    {
        $this->hasDate = $condition;

        return $this;
    }

    public function seconds(bool | Closure $condition = true): static
    {
        $this->hasSeconds = $condition;

        return $this;
    }

    public function time(bool | Closure $condition = true): static
    {
        $this->hasTime = $condition;

        return $this;
    }

    /**
     * @deprecated Use `date()` instead.
     */
    public function withoutDate(bool | Closure $condition = true): static
    {
        $this->date(fn (DateTimePicker $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `seconds()` instead.
     */
    public function withoutSeconds(bool | Closure $condition = true): static
    {
        $this->seconds(fn (DateTimePicker $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `time()` instead.
     */
    public function withoutTime(bool | Closure $condition = true): static
    {
        $this->time(fn (DateTimePicker $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    public function closeOnDateSelection(bool | Closure $condition = true): static
    {
        $this->shouldCloseOnDateSelection = $condition;

        return $this;
    }

    public function getDisplayFormat(): string
    {
        $format = $this->evaluate($this->displayFormat);

        if ($format) {
            return $format;
        }

        if (! $this->hasTime()) {
            return $this->getDefaultDateDisplayFormat();
        }

        if (! $this->hasDate()) {
            return $this->hasSeconds() ?
                $this->getDefaultTimeWithSecondsDisplayFormat() :
                $this->getDefaultTimeDisplayFormat();
        }

        return $this->hasSeconds() ?
            $this->getDefaultDateTimeWithSecondsDisplayFormat() :
            $this->getDefaultDateTimeDisplayFormat();
    }

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

    public function defaultDateTimeWithSecondsDisplayFormat(string | Closure $format): static
    {
        $this->defaultDateTimeWithSecondsDisplayFormat = $format;

        return $this;
    }

    public function defaultTimeDisplayFormat(string | Closure $format): static
    {
        $this->defaultTimeDisplayFormat = $format;

        return $this;
    }

    public function defaultTimeWithSecondsDisplayFormat(string | Closure $format): static
    {
        $this->defaultTimeWithSecondsDisplayFormat = $format;

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

    public function getDefaultDateTimeWithSecondsDisplayFormat(): string
    {
        return $this->evaluate($this->defaultDateTimeWithSecondsDisplayFormat);
    }

    public function getDefaultTimeDisplayFormat(): string
    {
        return $this->evaluate($this->defaultTimeDisplayFormat);
    }

    public function getDefaultTimeWithSecondsDisplayFormat(): string
    {
        return $this->evaluate($this->defaultTimeWithSecondsDisplayFormat);
    }

    /**
     * @return array<mixed>
     */
    public function getExtraTriggerAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraTriggerAttributes as $extraTriggerAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraTriggerAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraTriggerAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraTriggerAttributes());
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->firstDayOfWeek ?? 1;
    }

    public function getFormat(): string
    {
        $format = $this->evaluate($this->format);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'Y-m-d' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getMaxDate(): ?string
    {
        return $this->evaluate($this->maxDate);
    }

    public function getMinDate(): ?string
    {
        return $this->evaluate($this->minDate);
    }

    public function getDefaultFocusedDate(): ?string
    {
        $defaultFocusedDate = $this->evaluate($this->defaultFocusedDate);

        if (filled($defaultFocusedDate)) {
            if (! $defaultFocusedDate instanceof CarbonInterface) {
                try {
                    $defaultFocusedDate = Carbon::createFromFormat($this->getFormat(), (string) $defaultFocusedDate, config('app.timezone'));
                } catch (InvalidFormatException $exception) {
                    try {
                        $defaultFocusedDate = Carbon::parse($defaultFocusedDate, config('app.timezone'));
                    } catch (InvalidFormatException $exception) {
                        return null;
                    }
                }
            }

            $defaultFocusedDate = $defaultFocusedDate->setTimezone($this->getTimezone());
        }

        return $defaultFocusedDate;
    }

    /**
     * @return array<DateTime | string>
     */
    public function getDisabledDates(): array
    {
        return $this->evaluate($this->disabledDates);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? ($this->hasTime() ? FilamentTimezone::get() : config('app.timezone'));
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? config('app.locale');
    }

    public function hasDate(): bool
    {
        return (bool) $this->evaluate($this->hasDate);
    }

    public function hasSeconds(): bool
    {
        return (bool) $this->evaluate($this->hasSeconds);
    }

    public function hasTime(): bool
    {
        return (bool) $this->evaluate($this->hasTime);
    }

    public function getHoursStep(): int
    {
        return $this->evaluate($this->hoursStep) ?? 1;
    }

    public function getMinutesStep(): int
    {
        return $this->evaluate($this->minutesStep) ?? 1;
    }

    public function getSecondsStep(): int
    {
        return $this->evaluate($this->secondsStep) ?? 1;
    }

    public function shouldCloseOnDateSelection(): bool
    {
        return (bool) $this->evaluate($this->shouldCloseOnDateSelection);
    }

    public function getStep(): int | float | string | null
    {
        $step = $this->evaluate($this->step);

        if (filled($step)) {
            return $step;
        }

        if (! $this->hasTime()) {
            return null;
        }

        $secondsStep = $this->getSecondsStep();

        if ($secondsStep > 1) {
            return $secondsStep;
        }

        $minutesStep = $this->getMinutesStep();

        if ($minutesStep > 1) {
            return $minutesStep * 60;
        }

        $hoursStep = $this->getHoursStep();

        if ($hoursStep > 1) {
            return $hoursStep * 3600;
        }

        if (! $this->hasSeconds()) {
            return null;
        }

        return 1;
    }

    public function getType(): string
    {
        if (! $this->hasDate()) {
            return 'time';
        }

        if (! $this->hasTime()) {
            return 'date';
        }

        return 'datetime-local';
    }
}
