@php
    $statePath = $getStatePath();
@endphp

<div
    x-data="{

        step: null,

        init: function () {
            this.$watch('step', () => this.updateQueryString())

            this.step = this.getSteps()[{{ $getStartStep() }} - 1]
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
            this.$el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        },

        autofocusFields: function () {
            $nextTick(() => this.$refs[`step-${this.step}`].querySelector('[autofocus]')?.focus())
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
            return (this.getStepIndex(this.step) + 1) >= this.getSteps().length
        },

        isStepAccessible: function(step, index) {
            return @js($isSkippable()) || (this.getStepIndex(step) > index)
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
    x-on:next-wizard-step.window="if ($event.detail.statePath === '{{ $statePath }}') nextStep()"
    x-cloak
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class(['filament-forms-wizard-component grid gap-y-6'])
    }}
>
    <input
        type="hidden"
        value='{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Forms\Components\Wizard\Step $step): bool => $step->isVisible())
                ->map(static fn (\Filament\Forms\Components\Wizard\Step $step) => $step->getId())
                ->values()
                ->toJson()
        }}'
        x-ref="stepsData"
    />

    <ol
        @if ($label = $getLabel()) aria-label="{{ $label }}" @endif
        role="list"
        class="border border-gray-300 shadow-sm bg-white rounded-xl overflow-hidden divide-y divide-gray-300 md:flex md:divide-y-0 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700"
    >
        @foreach ($getChildComponentContainer()->getComponents() as $step)
            <li class="relative overflow-hidden group md:flex-1">
                <button
                    type="button"
                    x-on:click="if (isStepAccessible(step, {{ $loop->index }})) step = '{{ $step->getId() }}'"
                    x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                    x-bind:class="{
                        'pointer-events-none': ! isStepAccessible(step, {{ $loop->index }}),
                    }"
                    role="step"
                    class="flex items-center w-full h-full text-start"
                >
                    <div
                        x-bind:class="{
                            'bg-primary-600': getStepIndex(step) === {{ $loop->index }},
                            'bg-transparent group-hover:bg-gray-200 dark:group-hover:bg-gray-600': getStepIndex(step) > {{ $loop->index }},
                        }"
                        class="absolute top-0 left-0 w-1 h-full md:w-full md:h-1 md:bottom-0 md:top-auto"
                        aria-hidden="true"
                    ></div>

                    <div class="flex items-center gap-3 px-5 py-4 text-sm font-medium">
                        <div class="shrink-0">
                            <div
                                x-bind:class="{
                                    'bg-primary-600': getStepIndex(step) > {{ $loop->index }},
                                    'border-2': getStepIndex(step) <= {{ $loop->index }},
                                    'border-primary-500': getStepIndex(step) === {{ $loop->index }},
                                    'border-gray-300 dark:border-gray-500': getStepIndex(step) < {{ $loop->index }},
                                }"
                                class="flex items-center justify-center w-10 h-10 rounded-full"
                            >
                                <x-filament::icon
                                    name="heroicon-m-check"
                                    alias="filament-forms::components.wizard.completed-step"
                                    color="text-white"
                                    size="h-5 w-5"
                                    x-show="getStepIndex(step) > {{ $loop->index }}"
                                    x-cloak="x-cloak"
                                />

                                @if ($icon = $step->getIcon())
                                    <x-filament::icon
                                        :name="$icon"
                                        alias="filament-forms::components.wizard.current-step"
                                        size="h-5 w-5"
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-cloak="x-cloak"
                                        x-bind:class="{
                                            'text-gray-500 dark:text-gray-400': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-500': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                    />
                                @else
                                    <span
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-bind:class="{
                                            'text-gray-500 dark:text-gray-400': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-500': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                    >
                                        {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-start justify-center">
                            <div class="text-sm font-medium">
                                {{ $step->getLabel() }}
                            </div>

                            @if (filled($description = $step->getDescription()))
                                <div class="text-sm leading-4 font-medium text-gray-500 dark:text-gray-400">
                                    {{ $description }}
                                </div>
                            @endif
                        </div>
                    </div>
                </button>

                @if (! $loop->first)
                    <div class="hidden absolute top-0 left-0 w-3 inset-0 md:block" aria-hidden="true">
                        <svg
                            class="h-full w-full text-gray-300 rtl:rotate-180 dark:text-gray-700"
                            viewBox="0 0 12 82"
                            fill="none"
                            preserveAspectRatio="none"
                        >
                            <path d="M0.5 0V31L10.5 41L0.5 51V82" stroke="currentcolor" vector-effect="non-scaling-stroke" />
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
            <div
                x-on:click="previousStep"
                x-show="! isFirstStep()"
                x-cloak
            >
                {{ $getAction('previous') }}
            </div>

            <div x-show="isFirstStep()">
                {{ $getCancelAction() }}
            </div>
        </div>

        <div>
            <div
                x-on:click="$wire.dispatchFormEvent('wizard::nextStep', '{{ $statePath }}', getStepIndex(step))"
                x-show="! isLastStep()"
                x-cloak
            >
                {{ $getAction('next') }}
            </div>

            <div x-show="isLastStep()">
                {{ $getSubmitAction() }}
            </div>
        </div>
    </div>
</div>
