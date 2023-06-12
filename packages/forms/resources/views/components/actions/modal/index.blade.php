@php
    $action = $this->getMountedFormComponentAction();
@endphp

<form wire:submit.prevent="callMountedFormComponentAction">
    <x-forms::modal
        :id="$this->id . '-form-component-action'"
        :wire:key="$action ? $this->id . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
        :visible="filled($action)"
        :width="$action?->getModalWidth()"
        :slide-over="$action?->isModalSlideOver()"
        :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
        display-classes="block"
        x-init="livewire = $wire.__instance"
        x-on:modal-closed.stop="if ('mountedFormComponentAction' in livewire?.serverMemo.data) livewire.set('mountedFormComponentAction', null)"
    >
        @if ($action)
            @if ($action->isModalCentered())
                @if ($heading = $action->getModalHeading())
                    <x-slot name="heading">
                        {{ $heading }}
                    </x-slot>
                @endif

                @if ($subheading = $action->getModalSubheading())
                    <x-slot name="subheading">
                        {{ $subheading }}
                    </x-slot>
                @endif
            @else
                <x-slot name="header">
                    @if ($heading = $action->getModalHeading())
                        <x-forms::modal.heading>
                            {{ $heading }}
                        </x-forms::modal.heading>
                    @endif

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

            {{ $action->getModalFooter() }}

            @if (count($action->getModalActions()))
                <x-slot name="footer">
                    <x-forms::modal.actions
                        :full-width="$action->isModalCentered()"
                    >
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-forms::modal.actions>
                </x-slot>
            @endif
        @endif
    </x-forms::modal>
</form>
