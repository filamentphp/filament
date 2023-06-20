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
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->id }}') && $wire.mountedActions.length) open()"
            x-on:modal-closed.stop="
                const mountedActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedActionShouldOpenModal()) }}

                if (! mountedActionShouldOpenModal) {
                    return
                }

                if (
                    ('mountedFormComponentActions' in livewire?.serverMemo.data) &&
                    livewire.serverMemo.data.mountedFormComponentActions.length
                ) {
                    return
                }

                if ('mountedActions' in livewire?.serverMemo.data) {
                    livewire.call('unmountAction', false)
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

                {{ $action->getModalContentFooter() }}

                @if (count($modalActions = $action->getVisibleModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($modalActions as $modalAction)
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

@if ($this instanceof \Filament\Infolists\Contracts\HasInfolists && (! $this->hasInfolistsModalRendered))
    <form wire:submit.prevent="callMountedInfolistAction">
        @php
            $action = $this->getMountedInfolistAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-infolist-action'"
            :wire:key="$action ? $this->id . '.infolist.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->id }}') && $wire.mountedInfolistActions.length) open()"
            x-on:modal-closed.stop="
                const mountedInfolistActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedInfolistActionShouldOpenModal()) }}

                if (! mountedInfolistActionShouldOpenModal) {
                    return
                }

                if (
                    ('mountedFormComponentActions' in livewire?.serverMemo.data) &&
                    livewire.serverMemo.data.mountedFormComponentActions.length
                ) {
                    return
                }

                if ('mountedInfolistActions' in livewire?.serverMemo.data) {
                    livewire.call('unmountInfolistAction', false)
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
                @elseif ($this->mountedInfolistActionHasForm())
                    {{ $this->getMountedInfolistActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}

                @if (count($modalActions = $action->getVisibleModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($modalActions as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasInfolistsModalRendered = true;
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
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->id }}') && $wire.mountedTableActions.length) open()"
            x-on:modal-closed.stop="
                const mountedTableActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableActionShouldOpenModal()) }}

                if (! mountedTableActionShouldOpenModal) {
                    return
                }

                if (
                    ('mountedFormComponentActions' in livewire?.serverMemo.data) &&
                    livewire.serverMemo.data.mountedFormComponentActions.length
                ) {
                    return
                }

                if ('mountedTableActions' in livewire?.serverMemo.data) {
                    livewire.call('unmountTableAction', false)
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

                {{ $action->getModalContentFooter() }}

                @if (count($modalActions = $action->getVisibleModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($modalActions as $modalAction)
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
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->id }}') && $wire.mountedTableBulkAction) open()"
            x-on:modal-closed.stop="
                const mountedTableBulkActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableBulkActionShouldOpenModal()) }}

                if (! mountedTableBulkActionShouldOpenModal) {
                    return
                }

                if (
                    ('mountedFormComponentActions' in livewire?.serverMemo.data) &&
                    livewire.serverMemo.data.mountedFormComponentActions.length
                ) {
                    return
                }

                if ('mountedTableBulkAction' in livewire?.serverMemo.data) {
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

                {{ $action->getModalContentFooter() }}

                @if (count($modalActions = $action->getVisibleModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($modalActions as $modalAction)
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
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedFormComponentActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedFormComponentActionShouldOpenModal()) }}

                if (mountedFormComponentActionShouldOpenModal && 'mountedFormComponentActions' in livewire?.serverMemo.data) {
                    livewire.call('unmountFormComponentAction', false)
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

                {{ $action->getModalContentFooter() }}

                @if (count($modalActions = $action->getVisibleModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($modalActions as $modalAction)
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
