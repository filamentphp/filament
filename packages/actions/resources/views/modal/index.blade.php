<form wire:submit.prevent="callMountedAction">
    @php
        $action = $this->getMountedAction();
    @endphp

    <x-filament-support::modal
        id="page-action"
        :wire:key="$action ? $this->id . '.actions.' . $action->getName() . '.modal' : null"
        :visible="filled($action)"
        :width="$action?->getModalWidth()"
        :slide-over="$action?->isModalSlideOver()"
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
                    <x-filament-support::modal.heading>
                        {{ $action->getModalHeading() }}
                    </x-filament-support::modal.heading>

                    @if ($subheading = $action->getModalSubheading())
                        <x-filament-support::modal.subheading>
                            {{ $subheading }}
                        </x-filament-support::modal.subheading>
                    @endif
                </x-slot>
            @endif

            {{ $action->getModalContent() }}

            @if ($this->mountedActionHasForm())
                {{ $this->getMountedActionForm() }}
            @endif

            @if (count($action->getModalActions()))
                <x-slot name="footer">
                    <x-filament-support::modal.actions :full-width="$action->isModalCentered()">
                        @foreach ($action->getModalActions() as $modalAction)
                            {{ $modalAction }}
                        @endforeach
                    </x-filament-support::modal.actions>
                </x-slot>
            @endif
        @endif
    </x-filament-support::modal>
</form>
