<?php

namespace Filament\Forms\Components;

use BackedEnum;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use DateTime;
use Filament\Actions\Action;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\DateTimeStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

class DateTimePicker extends Field implements Contracts\HasAffixes, HasEmbeddedView
{
    use Concerns\CanBeNative;
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasDatalistOptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use Concerns\HasStep;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.date-time-picker';

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

    public function toEmbeddedHtml(): string
    {
        $datalistOptions = $this->getDatalistOptions();
        $disabledDates = $this->getDisabledDates();
        $extraAlpineAttributes = $this->getExtraAlpineAttributes();
        $extraAttributeBag = $this->getExtraAttributeBag();
        $extraInputAttributeBag = $this->getExtraInputAttributeBag();
        $hasDate = $this->hasDate();
        $hasTime = $this->hasTime();
        $hasSeconds = $this->hasSeconds();
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $isAutofocused = $this->isAutofocused();
        $isPrefixInline = $this->isPrefixInline();
        $isSuffixInline = $this->isSuffixInline();
        $maxDate = $this->getMaxDate();
        $minDate = $this->getMinDate();
        $defaultFocusedDate = $this->getDefaultFocusedDate();
        $prefixActions = $this->getPrefixActions();
        $prefixIcon = $this->getPrefixIcon();
        $prefixIconColor = $this->getPrefixIconColor();
        $prefixLabel = $this->getPrefixLabel();
        $suffixActions = $this->getSuffixActions();
        $suffixIcon = $this->getSuffixIcon();
        $suffixIconColor = $this->getSuffixIconColor();
        $suffixLabel = $this->getSuffixLabel();
        $statePath = $this->getStatePath();
        $placeholder = $this->getPlaceholder();
        $isReadOnly = $this->isReadOnly();
        $isRequired = $this->isRequired();
        $isConcealed = $this->isConcealed();
        $step = $this->getStep();
        $type = $this->getType();
        $livewireKey = $this->getLivewireKey();
        $isNative = $this->isNative();

        // Mirror the snapshot Blade: the input's inline prefix/suffix classes
        // are computed against the unfiltered prefix/suffix actions, while the
        // wrapper Blade filtered actions before computing `$hasPrefix` /
        // `$hasSuffix` for the prefix/suffix div rendering.
        $hasInlinePrefix = count($prefixActions) || $prefixIcon || filled($prefixLabel);
        $hasInlineSuffix = count($suffixActions) || $suffixIcon || filled($suffixLabel);

        // Filter visible prefix/suffix actions
        $prefixActions = array_filter(
            $prefixActions,
            static fn (Action $action): bool => $action->isVisible(),
        );
        $suffixActions = array_filter(
            $suffixActions,
            static fn (Action $action): bool => $action->isVisible(),
        );

        $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefixLabel);
        $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffixLabel);

        $wrapperAttributes = $extraAttributeBag
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input:not([type=hidden])')?.focus()",
            ], escape: false)
            ->class(['fi-fo-date-time-picker']);

        ob_start(); ?>


                <?php if ($isNative) { ?>
                    <input
                        <?= $extraInputAttributeBag
                            ->merge($extraAlpineAttributes, escape: false)
                            ->merge([
                                'autofocus' => $isAutofocused,
                                'disabled' => $isDisabled,
                                'id' => $id,
                                'list' => $datalistOptions ? $id . '-list' : null,
                                'max' => $hasTime ? $maxDate : ($maxDate ? Carbon::parse($maxDate)->toDateString() : null),
                                'min' => $hasTime ? $minDate : ($minDate ? Carbon::parse($minDate)->toDateString() : null),
                                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                                'readonly' => $isReadOnly,
                                'required' => $isRequired && (! $isConcealed),
                                'step' => $step,
                                'type' => $type,
                                $this->applyStateBindingModifiers('wire:model') => $statePath,
                                'x-data' => count($extraAlpineAttributes) ? '{}' : null,
                            ], escape: false)
                            ->class([
                                'fi-input',
                                'fi-input-has-inline-prefix' => $isPrefixInline && $hasInlinePrefix,
                                'fi-input-has-inline-suffix' => $isSuffixInline && $hasInlineSuffix,
                            ])
                            ->toHtml() ?>
                    />
                <?php } else { ?>
                    <div
                        x-load
                        x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('date-time-picker', 'filament/forms')) ?>"
                        x-data="dateTimePickerFormComponent({
                                    defaultFocusedDate: <?= Js::from($defaultFocusedDate) ?>,
                                    displayFormat: <?= Js::from(convert_date_format($this->getDisplayFormat())->to('day.js')) ?>,
                                    firstDayOfWeek: <?= $this->getFirstDayOfWeek() ?>,
                                    isAutofocused: <?= Js::from($isAutofocused) ?>,
                                    locale: <?= Js::from($this->getLocale()) ?>,
                                    shouldCloseOnDateSelection: <?= Js::from($this->shouldCloseOnDateSelection()) ?>,
                                    state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                                })"
                        wire:ignore
                        wire:key="<?= e($livewireKey) ?>.<?= substr(md5(serialize([$disabledDates, $isDisabled, $isReadOnly, $maxDate, $minDate, $hasDate, $hasTime, $hasSeconds])), 0, 64) ?>"
                        x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                        <?= $this->getExtraAlpineAttributeBag()->toHtml() ?>
                    >
                        <input x-ref="maxDate" type="hidden" value="<?= e($maxDate) ?>" />
                        <input x-ref="minDate" type="hidden" value="<?= e($minDate) ?>" />
                        <input x-ref="disabledDates" type="hidden" value="<?= e(json_encode($disabledDates)) ?>" />

                        <button
                            x-ref="button"
                            x-on:click="togglePanelVisibility()"
                            x-on:keydown.enter.prevent.stop="if (! $el.disabled) { isOpen() ? selectDate() : togglePanelVisibility() }"
                            x-on:keydown.arrow-left.prevent.stop="if (! $el.disabled) focusPreviousDay()"
                            x-on:keydown.arrow-right.prevent.stop="if (! $el.disabled) focusNextDay()"
                            x-on:keydown.arrow-up.prevent.stop="if (! $el.disabled) focusPreviousWeek()"
                            x-on:keydown.arrow-down.prevent.stop="if (! $el.disabled) focusNextWeek()"
                            x-on:keydown.backspace.prevent.stop="if (! $el.disabled) clearState()"
                            x-on:keydown.clear.prevent.stop="if (! $el.disabled) clearState()"
                            x-on:keydown.delete.prevent.stop="if (! $el.disabled) clearState()"
                            aria-label="<?= e($placeholder) ?>"
                            type="button"
                            tabindex="-1"
                            <?php if ($isDisabled || $isReadOnly) { ?> disabled <?php } ?>
                            <?= $this->getExtraTriggerAttributeBag()->class(['fi-fo-date-time-picker-trigger'])->toHtml() ?>
                        >
                            <input
                                <?php if ($isDisabled) { ?> disabled <?php } ?>
                                readonly
                                placeholder="<?= e($placeholder) ?>"
                                wire:key="<?= e($livewireKey) ?>.display-text"
                                x-model="displayText"
                                <?php if ($id) { ?> id="<?= e($id) ?>" <?php } ?>
                                class="fi-fo-date-time-picker-display-text-input"
                            />
                        </button>

                        <div
                            x-ref="panel"
                            x-cloak
                            x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                            wire:ignore
                            wire:key="<?= e($livewireKey) ?>.panel"
                            class="fi-fo-date-time-picker-panel"
                        >
                            <?php if ($hasDate) { ?>
                                <div class="fi-fo-date-time-picker-panel-header">
                                    <select x-model="focusedMonth" class="fi-fo-date-time-picker-month-select">
                                        <template x-for="(month, index) in months">
                                            <option x-bind:value="index" x-text="month"></option>
                                        </template>
                                    </select>
                                    <input type="number" inputmode="numeric" x-model.debounce="focusedYear" class="fi-fo-date-time-picker-year-input" />
                                </div>

                                <div class="fi-fo-date-time-picker-calendar-header">
                                    <template x-for="(day, index) in dayLabels" x-bind:key="index">
                                        <div x-text="day" class="fi-fo-date-time-picker-calendar-header-day"></div>
                                    </template>
                                </div>

                                <div role="grid" class="fi-fo-date-time-picker-calendar">
                                    <template x-for="day in emptyDaysInFocusedMonth" x-bind:key="day">
                                        <div></div>
                                    </template>
                                    <template x-for="day in daysInFocusedMonth" x-bind:key="day">
                                        <div
                                            x-text="day"
                                            x-on:click="dayIsDisabled(day) || selectDate(day)"
                                            x-on:mouseenter="setFocusedDay(day)"
                                            role="option"
                                            x-bind:aria-selected="focusedDate.date() === day"
                                            x-bind:class="{
                                                'fi-fo-date-time-picker-calendar-day-today': dayIsToday(day),
                                                'fi-focused': focusedDate.date() === day,
                                                'fi-selected': dayIsSelected(day),
                                                'fi-disabled': dayIsDisabled(day),
                                            }"
                                            class="fi-fo-date-time-picker-calendar-day"
                                        ></div>
                                    </template>
                                </div>
                            <?php } ?>

                            <?php if ($hasTime) { ?>
                                <div class="fi-fo-date-time-picker-time-inputs">
                                    <input max="23" min="0" step="<?= $this->getHoursStep() ?>" type="number" inputmode="numeric" x-on:blur="checkTimeInputValidity" x-on:invalid="timeInputInvalid" x-model.debounce="hour" />
                                    <span class="fi-fo-date-time-picker-time-input-separator">:</span>
                                    <input max="59" min="0" step="<?= $this->getMinutesStep() ?>" type="number" inputmode="numeric" x-on:blur="checkTimeInputValidity" x-on:invalid="timeInputInvalid" x-model.debounce="minute" />
                                    <?php if ($hasSeconds) { ?>
                                        <span class="fi-fo-date-time-picker-time-input-separator">:</span>
                                        <input max="59" min="0" step="<?= $this->getSecondsStep() ?>" type="number" inputmode="numeric" x-on:blur="checkTimeInputValidity" x-on:invalid="timeInputInvalid" x-model.debounce="second" />
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

        <?php if ($datalistOptions) { ?>
            <datalist id="<?= e($id) ?>-list">
                <?php foreach ($datalistOptions as $option) { ?>
                    <option value="<?= e($option) ?>" />
                <?php } ?>
            </datalist>
        <?php } ?>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
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
        // Security: Attribute values are not escaped when rendered. Never
        // pass unsanitized user input as attribute names or values.

        if ($merge) {
            $this->extraTriggerAttributes[] = $attributes;
        } else {
            $this->extraTriggerAttributes = [$attributes];
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
        $temporaryAttributeBag = new FilamentComponentAttributeBag;

        foreach ($this->extraTriggerAttributes as $extraTriggerAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraTriggerAttributes), escape: false);
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraTriggerAttributeBag(): ComponentAttributeBag
    {
        return new FilamentComponentAttributeBag($this->getExtraTriggerAttributes());
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
