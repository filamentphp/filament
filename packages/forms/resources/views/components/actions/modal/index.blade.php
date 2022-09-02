@php
    $action = $this->getMountedFormComponentAction();
@endphp

<form wire:submit.prevent="callMountedFormComponentAction">
    <x-forms::modal
        :id="$this->id . '-form-component-action'"
        :wire:key="$action ? $this->id . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
        x-init="
            // https://github.com/filamentphp/filament/issues/3665
            this.wire = $wire.__instance

            $watch('isOpen', () => {
                if (isOpen) {
                    return
                }

                // https://github.com/filamentphp/filament/pull/3525
                this.wire.set('mountedFormComponentAction', null)
            })
        "
        :visible="filled($action)"
        :width="$action?->getModalWidth()"
        display-classes="block"
    >
        @if ($action)
            @if ($action->isModalCentered())
                <x-slot name="heading">
                    {{ $action->getModalHeading() }}
                </x-slot>

                @if ($subheading = $action->getModalSubheading())
                    <x-slot name="subheading">
                        {{ $subheading }}
                    </x-slot>
                @endif
            @else
                <x-slot name="header">
                    <x-forms::modal.heading>
                        {{ $action->getModalHeading() }}
                    </x-forms::modal.heading>

                    @if ($subheading = $action->getModalSubheading())
                        <x-forms::modal.subheading>
                            {{ $subheading }}
                        </x-forms::modal.subheading>
                    @endif
                </x-slot>
            @endif

            {{ $action->getModalContent() }}

            @if ($action->hasFormSchema())
                {{ $this->getMountedFormComponentActionForm() }}
            @endif

            @if (count($action->getModalActions()))
                <x-slot name="footer">
                    <x-forms::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-forms::modal.actions>
                </x-slot>
            @endif
        @endif
    </x-forms::modal>
</form>
