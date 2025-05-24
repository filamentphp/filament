@php
    $isContained = $isContained();
    $statePath = $getStatePath();
    $previousAction = $getAction('previous');
    $nextAction = $getAction('next');
@endphp

<div
    wire:ignore.self
    x-cloak
    x-data="{
        step: null,

        nextStep: function () {
            let nextStepIndex = this.getStepIndex(this.step) + 1

            if (nextStepIndex >= this.getSteps().length) {
                return
            }

            this.step = this.getSteps()[nextStepIndex]

            this.autofocusFields()
            this.scroll()
        },

        previousStep: function () {
            let previousStepIndex = this.getStepIndex(this.step) - 1

            if (previousStepIndex < 0) {
                return
            }

            this.step = this.getSteps()[previousStepIndex]

            this.autofocusFields()
            this.scroll()
        },

        scroll: function () {
            this.$nextTick(() => {
                this.$refs.header.children[
                    this.getStepIndex(this.step)
                ].scrollIntoView({ behavior: 'smooth', block: 'start' })
            })
        },

        autofocusFields: function () {
            $nextTick(() =>
                this.$refs[`step-${this.step}`]
                    .querySelector('[autofocus]')
                    ?.focus(),
            )
        },

        getStepIndex: function (step) {
            let index = this.getSteps().findIndex(
                (indexedStep) => indexedStep === step,
            )

            if (index === -1) {
                return 0
            }

            return index
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

        isStepAccessible: function (stepId) {
            return (
                @js($isSkippable()) || this.getStepIndex(this.step) > this.getStepIndex(stepId)
            )
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
    x-init="
        $watch('step', () => updateQueryString())

        step = getSteps().at({{ $getStartStep() - 1 }})

        autofocusFields()
    "
    x-on:next-wizard-step.window="if ($event.detail.statePath === '{{ $statePath }}') nextStep()"
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-fo-wizard',
                'fi-contained rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => $isContained,
            ])
    }}
>
    <input
        type="hidden"
        value="{{
            collect($getChildComponentContainer()->getComponents())
                ->filter(static fn (\Filament\Forms\Components\Wizard\Step $step): bool => $step->isVisible())
                ->map(static fn (\Filament\Forms\Components\Wizard\Step $step) => $step->getId())
                ->values()
                ->toJson()
        }}"
        x-ref="stepsData"
    />

    <ol
        @if (filled($label = $getLabel()))
            aria-label="{{ $label }}"
        @endif
        role="list"
        @class([
            'fi-fo-wizard-header grid divide-y divide-gray-200 dark:divide-white/5 md:grid-flow-col md:divide-y-0 md:overflow-x-auto',
            'border-b border-gray-200 dark:border-white/10' => $isContained,
            'rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => ! $isContained,
        ])
        x-ref="header"
    >
        @foreach ($getChildComponentContainer()->getComponents() as $step)
            <li
                class="fi-fo-wizard-header-step relative flex"
                x-bind:class="{
                    'fi-active': getStepIndex(step) === {{ $loop->index }},
                    'fi-completed': getStepIndex(step) > {{ $loop->index }},
                }"
            >
                <button
                    type="button"
                    x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                    x-on:click="step = @js($step->getId())"
                    x-bind:disabled="! isStepAccessible(@js($step->getId())) || @js($previousAction->isDisabled())"
                    role="step"
                    class="fi-fo-wizard-header-step-button flex h-full items-center gap-x-4 px-6 py-4 text-start"
                >
                    <div
                        class="fi-fo-wizard-header-step-icon-ctn flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                        x-bind:class="{
                            'bg-primary-600 dark:bg-primary-500':
                                getStepIndex(step) > {{ $loop->index }},
                            'border-2': getStepIndex(step) <= {{ $loop->index }},
                            'border-primary-600 dark:border-primary-500':
                                getStepIndex(step) === {{ $loop->index }},
                            'border-gray-300 dark:border-gray-600':
                                getStepIndex(step) < {{ $loop->index }},
                        }"
                    >
                        @php
                            $completedIcon = $step->getCompletedIcon();
                        @endphp

                        <x-filament::icon
                            :alias="filled($completedIcon) ? null : 'forms::components.wizard.completed-step'"
                            :icon="$completedIcon ?? 'heroicon-o-check'"
                            x-cloak="x-cloak"
                            x-show="getStepIndex(step) > {{ $loop->index }}"
                            class="fi-fo-wizard-header-step-icon h-6 w-6 text-white"
                        />

                        @if (filled($icon = $step->getIcon()))
                            <x-filament::icon
                                :icon="$icon"
                                x-cloak="x-cloak"
                                x-show="getStepIndex(step) <= {{ $loop->index }}"
                                class="fi-fo-wizard-header-step-icon h-6 w-6"
                                x-bind:class="{
                                    'text-gray-500 dark:text-gray-400': getStepIndex(step) !== {{ $loop->index }},
                                    'text-primary-600 dark:text-primary-500': getStepIndex(step) === {{ $loop->index }},
                                }"
                            />
                        @else
                            <span
                                x-show="getStepIndex(step) <= {{ $loop->index }}"
                                class="fi-fo-wizard-header-step-indicator text-sm font-medium"
                                x-bind:class="{
                                    'text-gray-500 dark:text-gray-400':
                                        getStepIndex(step) !== {{ $loop->index }},
                                    'text-primary-600 dark:text-primary-500':
                                        getStepIndex(step) === {{ $loop->index }},
                                }"
                            >
                                {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        @endif
                    </div>

                    <div class="grid justify-items-start md:w-max md:max-w-60">
                        @if (! $step->isLabelHidden())
                            <span
                                class="fi-fo-wizard-header-step-label text-sm font-medium"
                                x-bind:class="{
                                    'text-gray-500 dark:text-gray-400':
                                        getStepIndex(step) < {{ $loop->index }},
                                    'text-primary-600 dark:text-primary-400':
                                        getStepIndex(step) === {{ $loop->index }},
                                    'text-gray-950 dark:text-white': getStepIndex(step) > {{ $loop->index }},
                                }"
                            >
                                {{ $step->getLabel() }}
                            </span>
                        @endif

                        @if (filled($description = $step->getDescription()))
                            <span
                                class="fi-fo-wizard-header-step-description text-start text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ $description }}
                            </span>
                        @endif
                    </div>
                </button>

                @if (! $loop->last)
                    <div
                        aria-hidden="true"
                        class="fi-fo-wizard-header-step-separator absolute end-0 hidden h-full w-5 md:block"
                    >
                        <svg
                            fill="none"
                            preserveAspectRatio="none"
                            viewBox="0 0 22 80"
                            class="h-full w-full text-gray-200 dark:text-white/5 rtl:rotate-180"
                        >
                            <path
                                d="M0 -2L20 40L0 82"
                                stroke-linejoin="round"
                                stroke="currentcolor"
                                vector-effect="non-scaling-stroke"
                            ></path>
                        </svg>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>

    @foreach ($getChildComponentContainer()->getComponents() as $step)
        {{ $step }}
    @endforeach

    <div
        @class([
            'flex items-center justify-between gap-x-3',
            'px-6 pb-6' => $isContained,
            'mt-6' => ! $isContained,
        ])
    >
        <span
            x-cloak
            @if (! $previousAction->isDisabled())
                x-on:click="previousStep"
            @endif
            x-show="! isFirstStep()"
        >
            {{ $previousAction }}
        </span>

        <span x-show="isFirstStep()">
            {{ $getCancelAction() }}
        </span>

        <span
            x-cloak
            @if (! $nextAction->isDisabled())
                x-on:click="
                    $wire.dispatchFormEvent(
                        'wizard::nextStep',
                        '{{ $statePath }}',
                        getStepIndex(step),
                    )
                "
            @endif
            x-bind:class="{ 'hidden': isLastStep(), 'block': ! isLastStep() }"
            wire:loading.class="pointer-events-none opacity-70"
        >
            {{ $nextAction }}
        </span>

        <span
            x-bind:class="{ 'hidden': ! isLastStep(), 'block': isLastStep() }"
        >
            {{ $getSubmitAction() }}
        </span>
    </div>
</div>
