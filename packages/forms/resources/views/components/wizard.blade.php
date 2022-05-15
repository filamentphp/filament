<div
    x-data="{

        step: '{{ count($getConfig()) ? array_key_first($getConfig()) : null }}',

        steps: @js($getConfig()),

        nextStep: function () {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= Object.keys(this.steps).length) {
                return
            }

            this.step = this.getIndexedSteps()[nextStepIndex][0]
        },

        previousStep: function () {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getIndexedSteps()[previousStepIndex][0]
        },

        getStepIndex: function (step) {
            return this.getIndexedSteps().findIndex((indexedStep) => indexedStep[0] === step)
        },

        getIndexedSteps: function () {
            return Object.entries(this.steps)
        },

        isFirstStep: function () {
            return this.getStepIndex(this.step) <= 0
        },

        isLastStep: function () {
            return (this.getStepIndex(this.step) + 1) >= Object.keys(this.steps).length
        },

    }"
    x-on:expand-concealing-component.window="if ($event.detail.id in steps) step = $event.detail.id"
    x-on:next-wizard-step.window="if ($event.detail.statePath === '{{ $getStatePath() }}') nextStep()"
    x-cloak
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['space-y-6 filament-forms-wizard-component']) }}
    {{ $getExtraAlpineAttributeBag() }}
>
    <ol
        {!! $getLabel() ? 'aria-label="' . $getLabel() . '"' : null !!}
        role="list"
        @class([
            'border border-gray-300 bg-white rounded-xl overflow-hidden divide-y divide-gray-300 lg:flex lg:divide-y-0',
            'dark:border-gray-700 dark:divide-gray-700' => config('forms.dark_mode'),
        ])
    >
        @foreach ($getChildComponentContainer()->getComponents() as $step)
            <li class="relative overflow-hidden lg:flex-1">
                <button
                    type="button"
                    x-on:click="if (getStepIndex(step) > {{ $loop->index }}) step = '{{ $step->getId() }}'"
                    x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                    x-bind:class="{
                        'cursor-not-allowed': getStepIndex(step) <= {{ $loop->index }},
                    }"
                    role="step"
                    class="group"
                >
                    <div
                        x-bind:class="{
                            'bg-primary-600': getStepIndex(step) === {{ $loop->index }},
                            'bg-transparent group-hover:bg-gray-200': getStepIndex(step) > {{ $loop->index }},
                        }"
                        class="absolute top-0 left-0 w-1 h-full lg:w-full lg:h-1 lg:bottom-0 lg:top-auto"
                        aria-hidden="true"
                    ></div>

                    <div class="px-6 py-5 flex gap-4 items-center text-sm font-medium">
                        <div class="flex-shrink-0">
                            <div
                                x-bind:class="{
                                    'bg-primary-600': getStepIndex(step) > {{ $loop->index }},
                                    'border-2': getStepIndex(step) <= {{ $loop->index }},
                                    'border-primary-600': getStepIndex(step) === {{ $loop->index }},
                                    'border-gray-300': getStepIndex(step) < {{ $loop->index }},
                                }"
                                class="w-10 h-10 flex items-center justify-center rounded-full"
                            >
                                <x-heroicon-o-check
                                    x-show="getStepIndex(step) > {{ $loop->index }}"
                                    x-cloak
                                    class="w-5 h-5 text-white"
                                />

                                @if ($step->getIcon())
                                    <x-dynamic-component
                                        :component="$step->getIcon()"
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-cloak
                                        x-bind:class="{
                                            'text-gray-500': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-600': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                        class="w-5 h-5"
                                    />
                                @else
                                    <span
                                        x-show="getStepIndex(step) <= {{ $loop->index }}"
                                        x-bind:class="{
                                            'text-gray-500': getStepIndex(step) !== {{ $loop->index }},
                                            'text-primary-600': getStepIndex(step) === {{ $loop->index }},
                                        }"
                                    >
                                        {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col items-start justify-center">
                            <div class="text-xs font-semibold tracking-wide uppercase">
                                {{ $step->getLabel() }}
                            </div>

                            @if (filled($description = $step->getDescription()))
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $description }}
                                </div>
                            @endif
                        </div>
                    </div>
                </button>

                @if (! $loop->first)
                    <div class="hidden absolute top-0 left-0 w-3 inset-0 lg:block" aria-hidden="true">
                        <svg class="h-full w-full text-gray-300" viewBox="0 0 12 82" fill="none" preserveAspectRatio="none">
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
            <x-forms::button
                icon="heroicon-s-chevron-left"
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
                icon="heroicon-s-chevron-right"
                icon-position="after"
                x-show="! isLastStep()"
                x-cloak
                x-on:click="$wire.dispatchFormEvent('wizard::nextStep', '{{ $getStatePath() }}', getStepIndex(step))"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-wait"
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
