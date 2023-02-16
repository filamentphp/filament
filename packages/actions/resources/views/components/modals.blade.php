@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <form wire:submit.prevent="callMountedAction">
        @php
            $action = $this->getMountedAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-action'"
            :wire:key="$action ? $this->id . '.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedActionShouldOpenModal()) }}

                if (mountedActionShouldOpenModal && 'mountedAction' in livewire?.serverMemo.data) {
                    livewire.set('mountedAction', null)
                }
            "
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
                            <x-filament::modal.heading>
                                {{ $heading }}
                            </x-filament::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedActionHasForm())
                    {{ $this->getMountedActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasActionsModalRendered = true;
    @endphp
@endif

@if ($this instanceof \Filament\Tables\Contracts\HasTable && (! $this->hasTableModalRendered))
    <form wire:submit.prevent="callMountedTableAction">
        @php
            $action = $this->getMountedTableAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-table-action'"
            :wire:key="$action ? $this->id . '.table.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedTableActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableActionShouldOpenModal()) }}

                if (mountedTableActionShouldOpenModal && 'mountedTableAction' in livewire?.serverMemo.data) {
                    livewire.set('mountedTableAction', null)
                }

                if (mountedTableActionShouldOpenModal && 'mountedTableActionRecord' in livewire?.serverMemo.data) {
                    livewire.set('mountedTableActionRecord', null)
                }
            "
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
                            <x-filament::modal.heading>
                                {{ $heading }}
                            </x-filament::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableActionHasForm())
                    {{ $this->getMountedTableActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $this->getMountedTableBulkAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-table-bulk-action'"
            :wire:key="$action ? $this->id . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedTableBulkActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableBulkActionShouldOpenModal()) }}

                if (mountedTableBulkActionShouldOpenModal && 'mountedTableBulkAction' in livewire?.serverMemo.data) {
                    livewire.set('mountedTableBulkAction', null)
                }
            "
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
                            <x-filament::modal.heading>
                                {{ $heading }}
                            </x-filament::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableBulkActionHasForm())
                    {{ $this->getMountedTableBulkActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasTableModalRendered = true;
    @endphp
@endif

@if (! $this->hasFormsModalRendered)
    @php
        $action = $this->getMountedFormComponentAction();
    @endphp

    <form wire:submit.prevent="callMountedFormComponentAction">
        <x-filament::modal
            :id="$this->id . '-form-component-action'"
            :wire:key="$action ? $this->id . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedFormComponentActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedFormComponentActionShouldOpenModal()) }}

                if (mountedFormComponentActionShouldOpenModal && 'mountedFormComponentAction' in livewire?.serverMemo.data) {
                    livewire.set('mountedFormComponentAction', null)
                }
            "
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
                            <x-filament::modal.heading>
                                {{ $heading }}
                            </x-filament::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedFormComponentActionHasForm())
                    {{ $this->getMountedFormComponentActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasFormsModalRendered = true;
    @endphp
@endif
