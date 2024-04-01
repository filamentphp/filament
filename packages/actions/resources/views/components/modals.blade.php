@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <div
        x-on:queue-open-modal.window="if ($event.detail.id.startsWith('{{ $this->getId() }}-action-')) $nextTick(() => $dispatch('open-modal', $event.detail))"
    >
        @foreach ($this->getMountedActions() as $actionNestingIndex => $action)
            <form wire:submit.prevent="callMountedAction">
                <x-filament::modal
                    :alignment="$action?->getModalAlignment()"
                    :close-button="$action?->hasModalCloseButton()"
                    :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
                    :description="$action?->getModalDescription()"
                    display-classes="block"
                    :footer-actions="$action?->getVisibleModalFooterActions()"
                    :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
                    :heading="$action?->getModalHeading()"
                    :icon="$action?->getModalIcon()"
                    :icon-color="$action?->getModalIconColor()"
                    :id="$this->getId() . '-action-' . $actionNestingIndex"
                    :slide-over="$action?->isModalSlideOver()"
                    :sticky-footer="$action?->isModalFooterSticky()"
                    :sticky-header="$action?->isModalHeaderSticky()"
                    :width="$action?->getModalWidth()"
                    :wire:key="$action ? $this->getId() . '.actions.' . $action->getName() . '.modal' : null"
                    x-on:modal-closed.stop="$wire.unmountAction(false)"
                >
                    {{ $action->getModalContent() }}

                    @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                        {{ $infolist }}
                    @elseif ($this->mountedActionHasForm(mountedAction: $action))
                        {{ $this->getMountedActionForm(mountedAction: $action) }}
                    @endif

                    {{ $action->getModalContentFooter() }}
                </x-filament::modal>
            </form>
        @endforeach
    </div>

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
            :alignment="$action?->getModalAlignment()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->getId() . '-table-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :sticky-header="$action?->isModalHeaderSticky()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->getId() . '.table.actions.' . $action->getName() . '.modal' : null"
            x-on:modal-closed.stop="
                const mountedTableActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableActionShouldOpenModal(mountedAction: $action)) }}

                if (! mountedTableActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedActions.length) {
                    return
                }

                $wire.unmountTableAction(false)
            "
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableActionHasForm(mountedAction: $action))
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
            :description="$action?->getModalDescription()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->getId() . '-table-bulk-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :sticky-header="$action?->isModalHeaderSticky()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->getId() . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            x-on:modal-closed.stop="
                const mountedTableBulkActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableBulkActionShouldOpenModal(mountedBulkAction: $action)) }}

                if (! mountedTableBulkActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedActions.length) {
                    return
                }

                $wire.unmountTableBulkAction(false)
            "
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedTableBulkActionHasForm(mountedBulkAction: $action))
                    {{ $this->getMountedTableBulkActionForm(mountedBulkAction: $action) }}
                @endif

                {{ $action->getModalContentFooter() }}
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasTableModalRendered = true;
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
            :description="$action?->getModalDescription()"
            display-classes="block"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->getId() . '-infolist-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :sticky-header="$action?->isModalHeaderSticky()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->getId() . '.infolist.actions.' . $action->getName() . '.modal' : null"
            x-on:modal-closed.stop="
                const mountedInfolistActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedInfolistActionShouldOpenModal(mountedAction: $action)) }}

                if (! mountedInfolistActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedActions.length) {
                    return
                }

                $wire.unmountInfolistAction(false)
            "
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedInfolistActionHasForm(mountedAction: $action))
                    {{ $this->getMountedInfolistActionForm(mountedAction: $action) }}
                @endif

                {{ $action->getModalContentFooter() }}
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasInfolistsModalRendered = true;
    @endphp
@endif
