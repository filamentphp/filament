@php
    $isContained = $isContained();
    $key = $getKey();
    $previousAction = $getAction('previous');
    $nextAction = $getAction('next');
    $steps = $getChildSchema()->getComponents();
    $isHeaderHidden = $isHeaderHidden();
@endphp

<div
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('wizard', 'filament/schemas') }}"
    x-data="wizardSchemaComponent({
                isSkippable: @js($isSkippable()),
                isStepPersistedInQueryString: @js($isStepPersistedInQueryString()),
                key: @js($key),
                startStep: @js($getStartStep()),
                stepQueryStringKey: @js($getStepQueryStringKey()),
            })"
    x-on:next-wizard-step.window="if ($event.detail.key === @js($key)) goToNextStep()"
    wire:ignore.self
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-sc-wizard',
                'fi-contained' => $isContained,
                'fi-sc-wizard-header-hidden' => $isHeaderHidden,
            ])
    }}
>
    <input
        type="hidden"
        value="{{
            collect($steps)
                ->filter(static fn (\Filament\Schemas\Components\Wizard\Step $step): bool => $step->isVisible())
                ->map(static fn (\Filament\Schemas\Components\Wizard\Step $step): ?string => $step->getKey())
                ->values()
                ->toJson()
        }}"
        x-ref="stepsData"
    />

    @if (! $isHeaderHidden)
        <ol
            @if (filled($label = $getLabel()))
                aria-label="{{ $label }}"
            @endif
            role="list"
            x-cloak
            x-ref="header"
            class="fi-sc-wizard-header"
        >
            @foreach ($steps as $step)
                <li
                    class="fi-sc-wizard-header-step"
                    x-bind:class="{
                        'fi-active': getStepIndex(step) === {{ $loop->index }},
                        'fi-completed': getStepIndex(step) > {{ $loop->index }},
                    }"
                >
                    <button
                        type="button"
                        x-bind:aria-current="getStepIndex(step) === {{ $loop->index }} ? 'step' : null"
                        x-on:click="step = @js($step->getKey())"
                        x-bind:disabled="! isStepAccessible(@js($step->getKey())) || @js($previousAction->isDisabled())"
                        role="step"
                        class="fi-sc-wizard-header-step-btn"
                    >
                        <div class="fi-sc-wizard-header-step-icon-ctn">
                            @php
                                $completedIcon = $step->getCompletedIcon();
                            @endphp

                            {{
                                \Filament\Support\generate_icon_html(
                                    $completedIcon ?? \Filament\Support\Icons\Heroicon::OutlinedCheck,
                                    alias: filled($completedIcon) ? null : \Filament\Schemas\View\SchemaIconAlias::COMPONENTS_WIZARD_COMPLETED_STEP,
                                    attributes: new \Illuminate\View\ComponentAttributeBag([
                                        'x-cloak' => 'x-cloak',
                                        'x-show' => "getStepIndex(step) > {$loop->index}",
                                    ]),
                                    size: \Filament\Support\Enums\IconSize::Large,
                                )
                            }}

                            @if (filled($icon = $step->getIcon()))
                                {{
                                    \Filament\Support\generate_icon_html(
                                        $icon,
                                        attributes: new \Illuminate\View\ComponentAttributeBag([
                                            'x-cloak' => 'x-cloak',
                                            'x-show' => "getStepIndex(step) <= {$loop->index}",
                                        ]),
                                        size: \Filament\Support\Enums\IconSize::Large,
                                    )
                                }}
                            @else
                                <span
                                    x-show="getStepIndex(step) <= {{ $loop->index }}"
                                    class="fi-sc-wizard-header-step-number"
                                >
                                    {{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                        </div>

                        <div class="fi-sc-wizard-header-step-text">
                            @if (! $step->isLabelHidden())
                                <span class="fi-sc-wizard-header-step-label">
                                    {{ $step->getLabel() }}
                                </span>
                            @endif

                            @if (filled($description = $step->getDescription()))
                                <span
                                    class="fi-sc-wizard-header-step-description"
                                >
                                    {{ $description }}
                                </span>
                            @endif
                        </div>
                    </button>

                    @if (! $loop->last)
                        <svg
                            fill="none"
                            preserveAspectRatio="none"
                            viewBox="0 0 22 80"
                            aria-hidden="true"
                            class="fi-sc-wizard-header-step-separator"
                        >
                            <path
                                d="M0 -2L20 40L0 82"
                                stroke-linejoin="round"
                                stroke="currentcolor"
                                vector-effect="non-scaling-stroke"
                            ></path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif

    @foreach ($steps as $step)
        {{ $step }}
    @endforeach

    <div x-cloak class="fi-sc-wizard-footer">
        <div
            x-cloak
            @if (! $previousAction->isDisabled())
                x-on:click="goToPreviousStep"
            @endif
            x-show="! isFirstStep()"
        >
            {{ $previousAction }}
        </div>

        <div x-show="isFirstStep()">
            {{ $getCancelAction() }}
        </div>

        <div
            x-cloak
            @if (! $nextAction->isDisabled())
                x-on:click="requestNextStep()"
            @endif
            x-bind:class="{ 'fi-hidden': isLastStep() }"
            wire:loading.class="fi-disabled"
        >
            {{ $nextAction }}
        </div>

        <div x-bind:class="{ 'fi-hidden': ! isLastStep() }">
            {{ $getSubmitAction() }}
        </div>
    </div>
</div>
