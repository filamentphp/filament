@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <form wire:submit.prevent="callMountedAction">
        @php
            $action = $this->getMountedAction();
        @endphp

        <x-filament::modal
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->id . '-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :description="$action?->getModalDescription()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->id . '.actions.' . $action->getName() . '.modal' : null"
            x-init="livewire = $wire.__instance"
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
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedActionHasForm())
                    {{ $this->getMountedActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}
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
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->id . '-infolist-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :description="$action?->getModalDescription()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->id . '.infolist.actions.' . $action->getName() . '.modal' : null"
            x-init="livewire = $wire.__instance"
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
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedInfolistActionHasForm())
                    {{ $this->getMountedInfolistActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}
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
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->id . '-table-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :description="$action?->getModalDescription()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->id . '.table.actions.' . $action->getName() . '.modal' : null"
            x-init="livewire = $wire.__instance"
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
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableActionHasForm())
                    {{ $this->getMountedTableActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}
            @endif
        </x-filament::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $this->getMountedTableBulkAction();
        @endphp

        <x-filament::modal
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->id . '-table-bulk-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :description="$action?->getModalDescription()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->id . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            x-init="livewire = $wire.__instance"
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
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->id }}') close()"
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableBulkActionHasForm())
                    {{ $this->getMountedTableBulkActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}
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
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->id . '-form-component-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :description="$action?->getModalDescription()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->id . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                const mountedFormComponentActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedFormComponentActionShouldOpenModal()) }}

                if (mountedFormComponentActionShouldOpenModal && 'mountedFormComponentActions' in livewire?.serverMemo.data) {
                    livewire.call('unmountFormComponentAction', false)
                }
            "
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedFormComponentActionHasForm())
                    {{ $this->getMountedFormComponentActionForm() }}
                @endif

                {{ $action->getModalContentFooter() }}
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasFormsModalRendered = true;
    @endphp
@endif
