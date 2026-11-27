<?php

namespace Filament\Forms\Components;

use Carbon\CarbonInterface;
use Closure;
use DateTime;
use Filament\Forms\Components\DateRangePicker\Enums\Preset;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\DateRangeStateCast;
use Filament\Schemas\Components\StateCasts\DateTimeStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Js;
use Illuminate\Validation\Validator;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class DateRangePicker extends Field implements Contracts\HasAffixes, HasEmbeddedView
{
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasLoadingPlaceholder;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string | Closure | null $displayFormat = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraTriggerAttributes = [];

    protected ?int $firstDayOfWeek = null;

    protected string | Closure | null $format = null;

    protected bool | Closure $shouldCloseOnDateSelection = false;

    protected CarbonInterface | string | Closure | null $maxDate = null;

    protected int | Closure | null $maxDays = null;

    protected CarbonInterface | string | Closure | null $minDate = null;

    protected CarbonInterface | string | Closure | null $defaultFocusedDate = null;

    protected string | Closure | null $locale = null;

    /**
     * @var array<DateTime | string> | Closure
     */
    protected array | Closure $disabledDates = [];

    /**
     * @var array<Preset | array{CarbonInterface | string, CarbonInterface | string}> | bool | Closure
     */
    protected array | bool | Closure $presets = false;

    protected string | Closure $defaultDateDisplayFormat = 'M j, Y';

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule(static fn (): Closure => static function (string $attribute, mixed $value, Closure $fail, Validator $validator): void {
            if (
                is_array($value) &&
                $validator->validateDateFormat($attribute, $value['start'] ?? null, ['Y-m-d']) &&
                $validator->validateDateFormat($attribute, $value['end'] ?? null, ['Y-m-d']) &&
                ($value['start'] <= $value['end'])
            ) {
                return;
            }

            $validator->addFailure($attribute, 'Date');
        });
    }

    public function toEmbeddedHtml(): string
    {
        $disabledDates = $this->getDisabledDates();
        $extraAttributeBag = $this->getExtraAttributeBag();
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $isAutofocused = $this->isAutofocused();
        $maxDate = $this->getMaxDate();
        $maxDays = $this->getMaxDays();
        $minDate = $this->getMinDate();
        $defaultFocusedDate = $this->getDefaultFocusedDate();
        $statePath = $this->getStatePath();
        $placeholder = $this->getPlaceholder();
        $isReadOnly = $this->isReadOnly();
        $presets = $this->getPresets();
        $livewireKey = $this->getLivewireKey();
        $label = $this->getLabel();
        $labelText = $label instanceof Htmlable ? html_entity_decode(strip_tags($label->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8') : $label;

        $triggerAttributes = $this->getExtraTriggerAttributeBag()
            ->merge($this->getAccessibilityAttributes(), escape: false)
            ->merge([
                'aria-controls' => $id . '-panel',
                'aria-expanded' => 'false',
                'aria-haspopup' => 'dialog',
                'disabled' => $isDisabled,
                'id' => $id,
                'placeholder' => $placeholder,
                'role' => 'combobox',
                'type' => 'text',
            ])
            ->class(['fi-fo-date-time-picker-trigger', 'fi-fo-date-time-picker-display-text-input']);

        $wrapperAttributes = $extraAttributeBag
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input:not([type=hidden])')?.focus()",
            ], escape: false)
            ->class(['fi-fo-date-time-picker fi-fo-date-range-picker']);

        $key = $livewireKey . '.' . md5(serialize([$disabledDates, $isDisabled, $isReadOnly, $maxDate, $maxDays, $minDate, $presets]));

        ob_start(); ?>

        <div hidden data-field-accessibility <?= $triggerAttributes->only(['aria-describedby', 'aria-invalid'])->toHtml() ?>></div>
        <div
            x-bind:inert="isLoading"
            x-bind:aria-hidden="isLoading"
            inert
            aria-hidden="true"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('date-range-picker', 'filament/forms')) ?>"
            x-data="dateRangePickerFormComponent({
                        defaultFocusedDate: <?= Js::from($defaultFocusedDate) ?>,
                        displayFormat: <?= Js::from(convert_date_format($this->getDisplayFormat())->to('day.js')) ?>,
                        firstDayOfWeek: <?= $this->getFirstDayOfWeek() ?>,
                        isAutofocused: <?= Js::from($isAutofocused && (! $isDisabled) && (! $isReadOnly)) ?>,
                        locale: <?= Js::from($this->getLocale()) ?>,
                        maxDays: <?= Js::from($maxDays) ?>,
                        shouldCloseOnDateSelection: <?= Js::from($this->shouldCloseOnDateSelection()) ?>,
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                    })"
            wire:ignore
            wire:key="<?= e($key) ?>"
            x-on:dropdown-escape="if (isOpen()) { closePanel(true); $event.preventDefault() }"
            x-on:focusout="handleFocusOut($event)"
            x-on:keydown.escape="if (isOpen()) { $event.preventDefault(); $event.stopPropagation(); closePanel(true) }"
            x-on:click.outside="isOpen() && closePanel()"
            x-on:livewire:navigating.window="closePanel()"
            <?= $this->getExtraAlpineAttributeBag()->class(['fi-async-component-content'])->toHtml() ?>
        >
            <input x-ref="maxDate" type="hidden" value="<?= e($maxDate) ?>" />
            <input x-ref="minDate" type="hidden" value="<?= e($minDate) ?>" />
            <input x-ref="disabledDates" type="hidden" value="<?= e(json_encode($disabledDates)) ?>" />

            <input
                x-ref="button"
                <?php if ($isDisabled || $isReadOnly) { ?>
                    x-on:keydown.enter.prevent.stop
                <?php } else { ?>
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown="handleTriggerKeydown($event)"
                    x-bind:aria-expanded="isPanelOpen.toString()"
                    x-on:keydown.backspace.prevent.stop="clearState()"
                    x-on:keydown.clear.prevent.stop="clearState()"
                    x-on:keydown.delete.prevent.stop="clearState()"
                <?php } ?>
                readonly
                wire:key="<?= e($livewireKey) ?>.display-text"
                x-model="displayText"
                <?= $triggerAttributes->toHtml() ?>
            />

            <div
                x-ref="panel"
                id="<?= e($id) ?>-panel"
                role="dialog"
                aria-label="<?= e($labelText) ?>"
                tabindex="-1"
                x-cloak
                x-show="isPanelOpen"
                wire:ignore
                wire:key="<?= e($livewireKey) ?>.panel"
                class="fi-fo-date-time-picker-panel fi-fo-date-range-picker-panel"
            >
                <?php if ($presets) { ?>
                    <div role="group" aria-label="<?= e(__('filament-forms::components.date_range_picker.presets.label')) ?>" class="fi-fo-date-range-picker-presets">
                        <?php foreach ($presets as $presetLabel => $preset) { ?>
                            <?php $presetJs = Js::from(($preset instanceof Preset) ? $preset->value : $preset); ?>
                            <button
                                type="button"
                                x-on:click="selectPreset(<?= $presetJs ?>)"
                                x-bind:disabled="presetIsDisabled(<?= $presetJs ?>)"
                                x-bind:aria-pressed="presetIsSelected(<?= $presetJs ?>).toString()"
                                x-bind:class="{
                                    'fi-active': presetIsSelected(<?= $presetJs ?>),
                                    'fi-disabled': presetIsDisabled(<?= $presetJs ?>),
                                }"
                                class="fi-fo-date-range-picker-preset"
                            >
                                <?= e(($preset instanceof Preset) ? $preset->getLabel() : $presetLabel) ?>
                            </button>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div aria-live="polite" aria-atomic="true" x-text="getCalendarLabel()" class="fi-fo-date-time-picker-calendar-description"></div>

                <div x-ref="calendar" tabindex="-1" x-on:keydown="handleCalendarKeydown($event)" x-on:mouseleave="hoveredDate = null" class="fi-fo-date-range-picker-months">
                    <template x-for="(month, monthIndex) in focusedMonths" x-bind:key="monthIndex">
                        <div class="fi-fo-date-range-picker-month">
                            <div class="fi-fo-date-time-picker-panel-header">
                                <button
                                    type="button"
                                    aria-label="<?= e(__('filament-forms::components.date_range_picker.previous_month_button.label')) ?>"
                                    x-on:click="focusPreviousMonth()"
                                    class="fi-fo-date-range-picker-previous-month-btn"
                                >
                                    <?= generate_icon_html(Heroicon::ChevronLeft, alias: FormsIconAlias::COMPONENTS_DATE_RANGE_PICKER_PREVIOUS_MONTH_BUTTON)?->toHtml() ?>
                                </button>

                                <template x-if="monthIndex === 0">
                                    <div class="fi-fo-date-range-picker-month-inputs">
                                        <select aria-label="<?= e(__('filament-forms::components.date_range_picker.month_select.label')) ?>" x-model="focusedMonth" class="fi-fo-date-time-picker-month-select">
                                            <template x-for="(monthName, monthNumber) in months">
                                                <option x-bind:value="monthNumber" x-bind:selected="monthNumber === focusedMonth" x-text="monthName"></option>
                                            </template>
                                        </select>
                                        <input type="number" inputmode="numeric" aria-label="<?= e(__('filament-forms::components.date_range_picker.year_input.label')) ?>" x-model.debounce="focusedYear" class="fi-fo-date-time-picker-year-input" />
                                    </div>
                                </template>

                                <template x-if="monthIndex !== 0">
                                    <span x-text="month.label" class="fi-fo-date-range-picker-month-label"></span>
                                </template>

                                <button
                                    type="button"
                                    aria-label="<?= e(__('filament-forms::components.date_range_picker.next_month_button.label')) ?>"
                                    x-on:click="focusNextMonth()"
                                    class="fi-fo-date-range-picker-next-month-btn"
                                >
                                    <?= generate_icon_html(Heroicon::ChevronRight, alias: FormsIconAlias::COMPONENTS_DATE_RANGE_PICKER_NEXT_MONTH_BUTTON)?->toHtml() ?>
                                </button>
                            </div>

                            <div role="grid" x-bind:aria-label="month.label" class="fi-fo-date-time-picker-calendar">
                                <div role="row" class="fi-fo-date-time-picker-calendar-header">
                                    <template x-for="(dayLabel, dayLabelIndex) in dayLabels" x-bind:key="dayLabelIndex">
                                        <div role="columnheader" x-bind:aria-label="fullDayLabels[dayLabelIndex]" x-text="dayLabel" class="fi-fo-date-time-picker-calendar-header-day"></div>
                                    </template>
                                </div>
                                <template x-for="(week, weekIndex) in month.weeks" x-bind:key="weekIndex">
                                    <div role="row" class="fi-fo-date-time-picker-calendar-week">
                                        <template x-for="(date, dateIndex) in week" x-bind:key="date ?? ('empty-' + dateIndex)">
                                            <div
                                                role="gridcell"
                                                x-text="date ? +date.slice(8) : ''"
                                                x-bind:data-date="date"
                                                x-bind:aria-label="date ? getDateLabel(date) : null"
                                                x-bind:aria-selected="date && dateIsInRange(date) ? 'true' : 'false'"
                                                x-bind:aria-current="date && dateIsToday(date) ? 'date' : null"
                                                x-bind:aria-disabled="date && dateIsDisabled(date) ? 'true' : 'false'"
                                                x-bind:tabindex="date ? (dateIsFocused(date) ? 0 : -1) : null"
                                                x-on:focus="date && setFocusedDate(date)"
                                                x-on:mouseenter="hoveredDate = date"
                                                x-on:click="if (date) { $el.focus(); selectDate(date) }"
                                                x-bind:class="{
                                                    'fi-fo-date-time-picker-calendar-day': date,
                                                    'fi-fo-date-time-picker-calendar-day-today': date && dateIsToday(date),
                                                    'fi-fo-date-range-picker-calendar-day-in-range': date && dateIsInRange(date),
                                                    'fi-fo-date-range-picker-calendar-day-range-start': date && dateIsRangeStart(date),
                                                    'fi-fo-date-range-picker-calendar-day-range-end': date && dateIsRangeEnd(date),
                                                    'fi-focused': date && dateIsFocused(date),
                                                    'fi-selected': date && dateIsSelected(date),
                                                    'fi-disabled': date && dateIsDisabled(date),
                                                }"
                                            ></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <?php $slotHtml = $this->wrapAsyncComponentHtml(ob_get_clean(), key: $key);

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
            app(DateRangeStateCast::class, [
                'format' => $this->getFormat(),
                'internalFormat' => 'Y-m-d',
                'timezone' => $this->getStateCastTimezone(),
            ]),
        ];
    }

    protected function getStateCastTimezone(): ?string
    {
        // Epoch formats need a timezone to determine their calendar date. Ignore escaped tokens.
        return str_contains(preg_replace('/\\\\./s', '', $this->getFormat()), 'U')
            ? config('app.timezone')
            : null;
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
            if (is_array($attributes) && in_array($attributes, $this->extraTriggerAttributes, strict: true)) {
                return $this;
            }

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

    public function maxDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->maxDate = $date;

        $this->rule(static fn (DateRangePicker $component): Closure => static function (string $attribute, mixed $value, Closure $fail, Validator $validator) use ($component): void {
            if (is_string($value['end'] ?? null) && ($value['end'] > $component->getMaxDate())) {
                $validator->addFailure($attribute, 'BeforeOrEqual', [$component->getMaxDate()]);
            }
        }, static fn (DateRangePicker $component): bool => (bool) $component->getMaxDate());

        return $this;
    }

    public function maxDays(int | Closure | null $days): static
    {
        $this->maxDays = $days;

        $this->rule(static fn (DateRangePicker $component): Closure => static function (string $attribute, mixed $value, Closure $fail, Validator $validator) use ($component): void {
            if (
                (! $validator->validateDateFormat($attribute, $value['start'] ?? null, ['Y-m-d'])) ||
                (! $validator->validateDateFormat($attribute, $value['end'] ?? null, ['Y-m-d']))
            ) {
                return;
            }

            $maxDays = $component->getMaxDays();

            if ((Carbon::parse($value['start'])->diffInDays($value['end']) + 1) > $maxDays) {
                $fail(trans_choice($component->getValidationMessages()['max_days'] ?? 'filament-forms::validation.date_range.max_days', $maxDays, [
                    'attribute' => $component->getValidationAttribute(),
                    'days' => $maxDays,
                ]));
            }
        }, static fn (DateRangePicker $component): bool => (bool) $component->getMaxDays());

        return $this;
    }

    public function minDate(CarbonInterface | string | Closure | null $date): static
    {
        $this->minDate = $date;

        $this->rule(static fn (DateRangePicker $component): Closure => static function (string $attribute, mixed $value, Closure $fail, Validator $validator) use ($component): void {
            if (is_string($value['start'] ?? null) && ($value['start'] < $component->getMinDate())) {
                $validator->addFailure($attribute, 'AfterOrEqual', [$component->getMinDate()]);
            }
        }, static fn (DateRangePicker $component): bool => (bool) $component->getMinDate());

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

    public function locale(string | Closure | null $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param  array<Preset | array{CarbonInterface | string, CarbonInterface | string}> | bool | Closure  $presets
     */
    public function presets(array | bool | Closure $presets = true): static
    {
        $this->presets = $presets;

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

    public function closeOnDateSelection(bool | Closure $condition = true): static
    {
        $this->shouldCloseOnDateSelection = $condition;

        return $this;
    }

    public function getDisplayFormat(): string
    {
        return $this->evaluate($this->displayFormat) ?? $this->getDefaultDateDisplayFormat();
    }

    public function defaultDateDisplayFormat(string | Closure $format): static
    {
        $this->defaultDateDisplayFormat = $format;

        return $this;
    }

    public function getDefaultDateDisplayFormat(): string
    {
        return $this->evaluate($this->defaultDateDisplayFormat);
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
        return $this->evaluate($this->format) ?? 'Y-m-d';
    }

    public function getMaxDate(): ?string
    {
        return $this->getDateLimit($this->evaluate($this->maxDate));
    }

    public function getMaxDays(): ?int
    {
        return $this->evaluate($this->maxDays);
    }

    public function getMinDate(): ?string
    {
        return $this->getDateLimit($this->evaluate($this->minDate));
    }

    protected function getDateLimit(CarbonInterface | string | null $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        if (! $date instanceof CarbonInterface) {
            $date = Carbon::parse($date, Carbon::hasRelativeKeywords($date) ? config('app.timezone') : 'UTC');
        }

        return $date->toDateString();
    }

    public function getDefaultFocusedDate(): ?string
    {
        return app(DateTimeStateCast::class, [
            'format' => $this->getFormat(),
            'internalFormat' => 'Y-m-d',
            'timezone' => $this->getStateCastTimezone(),
        ])->set($this->evaluate($this->defaultFocusedDate));
    }

    /**
     * @return array<DateTime | string>
     */
    public function getDisabledDates(): array
    {
        return $this->evaluate($this->disabledDates);
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? config('app.locale');
    }

    public function shouldCloseOnDateSelection(): bool
    {
        return (bool) $this->evaluate($this->shouldCloseOnDateSelection);
    }

    /**
     * @return array<Preset | array{string, string}>
     */
    public function getPresets(): array
    {
        $presets = $this->evaluate($this->presets);

        if (! is_array($presets)) {
            return $presets ? Preset::cases() : [];
        }

        return array_map(
            function (Preset | array $preset): Preset | array {
                if ($preset instanceof Preset) {
                    return $preset;
                }

                $dates = array_map(
                    fn (CarbonInterface | string $date): string => $this->getDateLimit($date),
                    array_values($preset),
                );

                sort($dates);

                return $dates;
            },
            $presets,
        );
    }
}
