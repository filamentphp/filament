<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class PhoneInput extends Field implements Contracts\HasAffixes, HasEmbeddedView
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasInputMode;
    use Concerns\HasLoadingPlaceholder;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $countries = null;

    protected string | Closure | null $defaultCountry = null;

    protected bool | Closure $isCopyable = false;

    protected string | Closure | null $locale = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $preferredCountries = [];

    protected string | Closure | null $telRegex = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regex(static fn (PhoneInput $component): string => $component->getTelRegex());
    }

    public function toEmbeddedHtml(): string
    {
        $countries = $this->getCountries();
        $defaultCountry = $this->getDefaultCountry();
        $extraAttributeBag = $this->getExtraAttributeBag();
        $id = $this->getId();
        $isAutofocused = $this->isAutofocused();
        $isDisabled = $this->isDisabled();
        $isPrefixInline = $this->isPrefixInline();
        $isReadOnly = $this->isReadOnly();
        $isSuffixInline = $this->isSuffixInline();
        $livewireKey = $this->getLivewireKey();
        $locale = $this->getLocale();
        $preferredCountries = $this->getPreferredCountries();
        $prefixActions = $this->getPrefixActions();
        $prefixIcon = $this->getPrefixIcon();
        $prefixLabel = $this->getPrefixLabel();
        $suffixActions = $this->getSuffixActions();
        $suffixIcon = $this->getSuffixIcon();
        $suffixLabel = $this->getSuffixLabel();
        $statePath = $this->getStatePath();
        $placeholder = $this->getPlaceholder();

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge($this->getAccessibilityAttributes(), escape: false)
            ->merge([
                'autocomplete' => $this->getAutocomplete(),
                'disabled' => $isDisabled,
                'id' => $id,
                'inputmode' => $this->getInputMode(),
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'readonly' => $isReadOnly,
                'required' => $this->isRequired() && (! $isDisabled) && (! $isReadOnly),
                'type' => 'tel',
                'x-on:blur' => 'setDisplayText()',
                'x-on:input' => 'setNumber($event.target.value)',
            ], escape: false)
            ->class([
                'fi-input',
                'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
            ]);

        $wrapperAttributes = $extraAttributeBag
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input[type=tel]')?.focus()",
            ], escape: false)
            ->class(['fi-fo-phone-input']);

        $key = $livewireKey . '.' . md5(serialize([$countries, $defaultCountry, $isDisabled, $isReadOnly, $locale, $preferredCountries]));

        ob_start(); ?>

        <div hidden data-field-accessibility <?= $inputAttributes->only(['aria-describedby', 'aria-invalid'])->toHtml() ?>></div>
        <div
            x-bind:inert="isLoading"
            x-bind:aria-hidden="isLoading"
            inert
            aria-hidden="true"
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('phone-input', 'filament/forms')) ?>"
            x-data="phoneInputFormComponent({
                        countries: <?= Js::from($countries) ?>,
                        countryButtonLabel: <?= Js::from(__('filament-forms::components.phone_input.country_button.label')) ?>,
                        defaultCountry: <?= Js::from($defaultCountry) ?>,
                        isAutofocused: <?= Js::from($isAutofocused && (! $isDisabled) && (! $isReadOnly)) ?>,
                        locale: <?= Js::from($locale) ?>,
                        preferredCountries: <?= Js::from($preferredCountries) ?>,
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                    })"
            wire:ignore
            wire:key="<?= e($key) ?>"
            x-on:focusout="handleFocusOut($event)"
            x-on:keydown.escape="if (isOpen()) { $event.preventDefault(); $event.stopPropagation(); closePanel(true) }"
            x-on:click.outside="isOpen() && closePanel()"
            x-on:livewire:navigating.window="closePanel()"
            <?= $this->getExtraAlpineAttributeBag()->class(['fi-input-wrp-content', 'fi-async-component-content'])->toHtml() ?>
        >
            <button
                x-ref="button"
                type="button"
                aria-controls="<?= e($id) ?>-panel"
                aria-expanded="false"
                aria-haspopup="listbox"
                aria-label="<?= e(__('filament-forms::components.phone_input.country_button.label')) ?>"
                x-bind:aria-expanded="isPanelOpen.toString()"
                x-bind:aria-label="getCountryButtonLabel()"
                <?php if ($isDisabled || $isReadOnly) { ?>
                    disabled
                <?php } else { ?>
                    x-on:click="togglePanelVisibility()"
                <?php } ?>
                class="fi-fo-phone-input-country-btn"
            >
                <img
                    alt=""
                    x-show="getCountryOption(country)"
                    x-bind:src="getCountryOption(country)?.flag"
                    class="fi-fo-phone-input-country-flag"
                />

                <span
                    x-text="getCountryOption(country)?.callingCode"
                    class="fi-fo-phone-input-country-calling-code"
                ></span>

                <?= generate_icon_html(Heroicon::ChevronDown, alias: FormsIconAlias::COMPONENTS_PHONE_INPUT_COUNTRY_BUTTON)?->toHtml() ?>
            </button>

            <input
                x-ref="input"
                <?= $inputAttributes->toHtml() ?>
            />

            <div
                x-ref="panel"
                id="<?= e($id) ?>-panel"
                x-cloak
                x-show="isPanelOpen"
                wire:ignore
                wire:key="<?= e($livewireKey) ?>.panel"
                class="fi-dropdown-panel fi-scrollable fi-fo-phone-input-panel"
            >
                <div class="fi-fo-phone-input-search-ctn">
                    <input
                        x-ref="search"
                        type="search"
                        autocomplete="off"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-controls="<?= e($id) ?>-countries"
                        aria-label="<?= e(__('filament-forms::components.phone_input.search_input.label')) ?>"
                        x-bind:aria-expanded="isPanelOpen.toString()"
                        x-bind:aria-activedescendant="getFocusedCountryOptionId()"
                        placeholder="<?= e(__('filament-forms::components.phone_input.search_input.placeholder')) ?>"
                        x-model="search"
                        x-on:keydown="handleSearchKeydown($event)"
                        class="fi-input"
                    />
                </div>

                <ul
                    id="<?= e($id) ?>-countries"
                    role="listbox"
                    aria-label="<?= e(__('filament-forms::components.phone_input.country_button.label')) ?>"
                    x-on:mousedown.prevent
                    class="fi-dropdown-list"
                >
                    <template x-for="option in filteredCountryOptions" x-bind:key="option.code">
                        <li
                            role="option"
                            x-bind:id="getCountryOptionId(option.code)"
                            x-bind:aria-selected="(option.code === country).toString()"
                            x-on:click="selectCountry(option.code)"
                            x-on:mouseenter="focusedCountry = option.code"
                            x-bind:class="{
                                'fi-selected': option.code === focusedCountry,
                            }"
                            class="fi-dropdown-list-item fi-fo-phone-input-country"
                        >
                            <img alt="" x-bind:src="option.flag" class="fi-fo-phone-input-country-flag" />

                            <span x-text="option.name" class="fi-fo-phone-input-country-name"></span>

                            <span x-text="option.callingCode" class="fi-fo-phone-input-country-calling-code"></span>
                        </li>
                    </template>
                </ul>

                <div x-show="! filteredCountryOptions.length" aria-hidden="true" x-on:mousedown.prevent class="fi-fo-phone-input-message">
                    <?= e(__('filament-forms::components.phone_input.no_search_results_message')) ?>
                </div>

                <div role="status" aria-live="polite" aria-atomic="true" x-text="filteredCountryOptions.length ? '' : <?= e(Js::from(__('filament-forms::components.phone_input.no_search_results_message'))) ?>" class="fi-sr-only"></div>
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

    public function copyable(
        bool | Closure $condition = true,
        string | Closure | null $copyMessage = null,
        int | Closure | null $copyMessageDuration = null
    ): static {
        $this->isCopyable = $condition;

        $this->suffixAction(
            TextInput\Actions\CopyAction::make()
                ->copyMessage($copyMessage)
                ->copyMessageDuration($copyMessageDuration)
                ->visible($condition),
        );

        return $this;
    }

    /**
     * @param  array<string> | Closure | null  $countries
     */
    public function countries(array | Closure | null $countries): static
    {
        $this->countries = $countries;

        return $this;
    }

    public function defaultCountry(string | Closure | null $country): static
    {
        $this->defaultCountry = $country;

        return $this;
    }

    public function locale(string | Closure | null $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param  array<string> | Closure  $countries
     */
    public function preferredCountries(array | Closure $countries): static
    {
        $this->preferredCountries = $countries;

        return $this;
    }

    public function telRegex(string | Closure | null $regex): static
    {
        $this->telRegex = $regex;

        return $this;
    }

    /**
     * @return array<string> | null
     */
    public function getCountries(): ?array
    {
        return $this->evaluate($this->countries);
    }

    public function getDefaultCountry(): ?string
    {
        return $this->evaluate($this->defaultCountry);
    }

    public function getLocale(): string
    {
        return $this->evaluate($this->locale) ?? config('app.locale');
    }

    /**
     * @return array<string>
     */
    public function getPreferredCountries(): array
    {
        return $this->evaluate($this->preferredCountries);
    }

    public function getTelRegex(): string
    {
        return $this->evaluate($this->telRegex) ?? '/^\+[1-9][0-9]{1,14}$/';
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->isCopyable);
    }
}
