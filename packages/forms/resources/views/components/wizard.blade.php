@php
    $isRtl = __('filament::layout.direction') === 'rtl';
    $previousArrowIcon = $isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left';
    $nextArrowIcon = $isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right';
@endphp

<div
    x-data="{
        step: null,

        init: function () {
            this.$watch('step', () => this.updateQueryString())

            this.step = this.getSteps().at({{ $getStartStep() - 1 }})
        },

        nextStep: function () {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= this.getSteps().length) {
                return
            }

            this.step = this.getSteps()[nextStepIndex]

            this.autofocusFields()
            this.scrollToTop()
        },

        previousStep: function () {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getSteps()[previousStepIndex]

            this.autofocusFields()
            this.scrollToTop()
        },

        scrollToTop: function () {
            this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' })
        },

        autofocusFields: function () {
            $nextTick(() =>
                this.$refs[`step-${this.step}`]
                    .querySelector('[autofocus]')
                    ?.focus(),
            )
        },

        getStepIndex: function (step) {
            return this.getSteps().findIndex((indexedStep) => indexedStep === step)
        },

        getSteps: function () {
            return JSON.parse(this.$refs.stepsData.value)
        },

        isFirstStep: function () {
            return this.getStepIndex(this.step) <= 0
        },

        isLastStep: function () {
            return this.getStepIndex(this.step) + 1 >= this.getSteps().length
        },

        isStepAccessible: function (step, index) {
            return @js($isSkippable()) || this.getStepIndex(step) > index
        },

        updateQueryString: function () {
            if (! @js($isStepPersistedInQueryString())) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(@js($getStepQueryStringKey()), this.step)

            history.pushState(null, document.title, url.toString())
        },
    }"
    x-on:next-wizard-step.window="if ($event.detail.statePath === '{{ $getStatePath() }}') nextStep()"
    x-cloak
    wire:ignore.self
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-wizard-component grid gap-y-6']) }}
    {{ $getExtraAlpineAttributeBag() }}
>
    <input
        type="hidden"
        value="{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Forms\Components\Wizard\Step $step): bool => ! $step->isHidden())
                ->map(static fn (\Filament\Forms\Components\Wizard\Step $step) => $step->getId())
                ->values()
                ->toJson()
        }}"
        x-ref="stepsData"
    />

    <ol
        {!! $getLabel() ? 'aria-label="' . $getLabel() . '"' : null !!}
        role="list"
        @class([
            'filament-forms-wizard-component-header divide-y divide-gray-300 overflow-hidden rounded-xl border border-gray-300 bg-white shadow-sm md:flex md:divide-y-0',
            'dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800' => config('forms.dark_mode'),
        ])
    >
        @foreach ($getChildComponentContainer()->getComponents() as $step)
            <li
                class="filament-forms-wizard-component-header-step group relative overflow-hidden md:flex-1"
            >
                <button
                    type="button"
                    x-on:click="if (isStepAccessible(step, {{ $loop->index }})) step = '{{ $step->getId() }}'"
                    x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                    x-bind:class="{
                        'cursor-not-allowed pointer-events-none': ! isStepAccessible(
                            step,
                            {{ $loop->index }},
                        ),
                    }"
                    role="step"
                    class="flex h-full w-full items-center text-start"
                >
                    <div
                        x-bind:class="{
                            'bg-primary-600': getStepIndex(step) === {{ $loop->index }},
                            'bg-transparent group-hover:bg-gray-200 @if (config('forms.dark_mode')) dark:group-hover:bg-gray-600 @endif': getStepIndex(step) > {{ $loop->index }},
                        }"
                        class="absolute left-0 top-0 h-full w-1 md:bottom-0 md:top-auto md:h-1 md:w-full"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="flex items-center gap-3 px-5 py-4 text-sm font-medium"
                    >
                        <div class="flex-shrink-0">
                            <div
                                x-bind:class="{
                                    'bg-primary-600': getStepIndex(step) > {{ $loop->index }},
                                    'border-2': getStepIndex(step) <= {{ $loop->index }},
                                    'border-primary-500': getStepIndex(step) === {{ $loop->index }},
                                    'border-gray-300 @if (config('forms.dark_mode')) dark:border-gray-500 @endif': getStepIndex(step) < {{ $loop->index }},
                                }"
                                class="filament-forms-wizard-component-header-step-icon flex h-10 w-10 items-center justify-center rounded-full"
                            >
                                <x-heroicon-o-check
                                    x-show="getStepIndex(step) > {{ $loop->index }}"
                                    x-cloak
                                    class="h-5 w-5 text-white"
                                />

                                @if ($step->getIcon())
                                    <x-dynamic-component
                                        :component="$step->getIcon()"
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-cloak
                                        x-bind:class="{
                                            'text-gray-500 @if (config('forms.dark_mode')) dark:text-gray-400 @endif': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-500': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                        class="h-5 w-5"
                                    />
                                @else
                                    <span
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-bind:class="{
                                            'text-gray-500 @if (config('forms.dark_mode')) dark:text-gray-400 @endif': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-500': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                    >
                                        {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-start justify-center">
                            <div
                                class="filament-forms-wizard-component-header-step-label text-sm font-semibold uppercase tracking-wide"
                            >
                                {{ $step->getLabel() }}
                            </div>

                            @if (filled($description = $step->getDescription()))
                                <div
                                    @class([
                                        'filament-forms-wizard-component-header-step-description text-sm font-medium leading-4 text-gray-500',
                                        'dark:text-gray-400' => config('forms.dark_mode'),
                                    ])
                                >
                                    {{ $description }}
                                </div>
                            @endif
                        </div>
                    </div>
                </button>

                @if (! $loop->first)
                    <div
                        class="absolute inset-0 left-0 top-0 hidden w-3 md:block"
                        aria-hidden="true"
                    >
                        <svg
                            @class([
                                'h-full w-full text-gray-300 rtl:rotate-180',
                                'dark:text-gray-700' => config('forms.dark_mode'),
                            ])
                            viewBox="0 0 12 82"
                            fill="none"
                            preserveAspectRatio="none"
                        >
                            <path
                                d="M0.5 0V31L10.5 41L0.5 51V82"
                                stroke="currentcolor"
                                vector-effect="non-scaling-stroke"
                            />
                        </svg>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>

    <div>
        @foreach ($getChildComponentContainer()->getComponents() as $step)
            {{ $step }}
        @endforeach
    </div>

    <div class="flex items-center justify-between">
        <div>
            <x-forms::button
                :icon="$previousArrowIcon"
                x-show="! isFirstStep()"
                x-cloak
                x-on:click="previousStep"
                color="secondary"
                size="sm"
            >
                {{ __('forms::components.wizard.buttons.previous_step.label') }}
            </x-forms::button>

            <div x-show="isFirstStep()">
                {{ $getCancelAction() }}
            </div>
        </div>

        <div>
            <x-forms::button
                :icon="$nextArrowIcon"
                icon-position="after"
                x-show="! isLastStep()"
                x-cloak
                x-on:click="$wire.dispatchFormEvent('wizard::nextStep', '{{ $getStatePath() }}', getStepIndex(step))"
                wire:loading.class.delay="opacity-70 cursor-wait"
                size="sm"
            >
                {{ __('forms::components.wizard.buttons.next_step.label') }}
            </x-forms::button>

            <div x-show="isLastStep()">
                {{ $getSubmitAction() }}
            </div>
        </div>
    </div>
</div>
