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
        display-classes="block"
        x-init="this.livewire = $wire.__instance"
        x-on:modal-closed.stop="if ('mountedFormComponentAction' in this.livewire?.serverMemo.data) this.livewire.set('mountedFormComponentAction', null)"
    >
        @if ($action)
            @if ($action->isModalCentered())
                @if ($modalHeading = $action->getModalHeading())
                    <x-tables::modal.heading>
                        {{ $modalHeading }}
                    </x-tables::modal.heading>
                @endif

                @if ($subheading = $action->getModalSubheading())
                    <x-slot name="subheading">
                        {{ $subheading }}
                    </x-slot>
                @endif
            @else
                <x-slot name="header">
                    @if ($modalHeading = $action->getModalHeading())
                        <x-tables::modal.heading>
                            {{ $modalHeading }}
                        </x-tables::modal.heading>
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
