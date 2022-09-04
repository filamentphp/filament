@php
    $action = $this->getMountedFormComponentAction();
@endphp

<form wire:submit.prevent="callMountedFormComponentAction">
    <x-forms::modal
        :id="$this->id . '-form-component-action'"
        :wire:key="$action ? $this->id . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
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
