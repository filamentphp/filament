@if ($this instanceof \Filament\Actions\Contracts\HasActions && (! $this->hasActionsModalRendered))
    <form wire:submit.prevent="callMountedAction">
        @php
            $action = $this->getMountedAction();
        @endphp

        <x-filament::modal
            :alignment="$action?->getModalAlignment()"
            :autofocus="$action?->isModalAutofocused()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :close-by-escaping="$action?->isModalClosedByEscaping()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :extra-modal-window-attribute-bag="$action?->getExtraModalWindowAttributeBag()"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->getId() . '-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :sticky-header="$action?->isModalHeaderSticky()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->getId() . '.actions.' . $action->getName() . '.modal' : null"
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->getId() }}') && $wire.mountedActions.length) open()"
            x-on:modal-closed.stop="
                if (!$event.detail?.id?.startsWith('{{ $this->getId() }}-')) {
                    return
                }

                const mountedActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedActionShouldOpenModal(mountedAction: $action)) }}

                if (! mountedActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedFormComponentActions.length) {
                    return
                }

                $wire.unmountAction(false, false)
            "
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->getId() }}') close()"
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedActionHasForm(mountedAction: $action))
                    {{ $this->getMountedActionForm(mountedAction: $action) }}
                @endif

                {{ $action->getModalContentFooter() }}
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
            :alignment="$action?->getModalAlignment()"
            :autofocus="$action?->isModalAutofocused()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :close-by-escaping="$action?->isModalClosedByEscaping()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :extra-modal-window-attribute-bag="$action?->getExtraModalWindowAttributeBag()"
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
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->getId() }}') && $wire.mountedTableActions.length) open()"
            x-on:modal-closed.stop="
                if (!$event.detail?.id?.startsWith('{{ $this->getId() }}-')) {
                    return
                }

                const mountedTableActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableActionShouldOpenModal(mountedAction: $action)) }}

                if (! mountedTableActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedFormComponentActions.length) {
                    return
                }

                $wire.unmountTableAction(false, false)
            "
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->getId() }}') close()"
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
            :autofocus="$action?->isModalAutofocused()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :close-by-escaping="$action?->isModalClosedByEscaping()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :extra-modal-window-attribute-bag="$action?->getExtraModalWindowAttributeBag()"
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
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->getId() }}') && $wire.mountedTableBulkAction) open()"
            x-on:modal-closed.stop="
                if (!$event.detail?.id?.startsWith('{{ $this->getId() }}-')) {
                    return
                }

                const mountedTableBulkActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedTableBulkActionShouldOpenModal(mountedBulkAction: $action)) }}

                if (! mountedTableBulkActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedFormComponentActions.length) {
                    return
                }

                $wire.unmountTableBulkAction(false)
            "
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->getId() }}') close()"
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
            :autofocus="$action?->isModalAutofocused()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :close-by-escaping="$action?->isModalClosedByEscaping()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :extra-modal-window-attribute-bag="$action?->getExtraModalWindowAttributeBag()"
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
            x-on:closed-form-component-action-modal.window="if (($event.detail.id === '{{ $this->getId() }}') && $wire.mountedInfolistActions.length) open()"
            x-on:modal-closed.stop="
                if (!$event.detail?.id?.startsWith('{{ $this->getId() }}-')) {
                    return
                }

                const mountedInfolistActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedInfolistActionShouldOpenModal(mountedAction: $action)) }}

                if (! mountedInfolistActionShouldOpenModal) {
                    return
                }

                if ($wire.mountedFormComponentActions.length) {
                    return
                }

                $wire.unmountInfolistAction(false, false)
            "
            x-on:opened-form-component-action-modal.window="if ($event.detail.id === '{{ $this->getId() }}') close()"
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

@if (! $this->hasFormsModalRendered)
    @php
        $action = $this->getMountedFormComponentAction();
    @endphp

    <form wire:submit.prevent="callMountedFormComponentAction">
        <x-filament::modal
            :alignment="$action?->getModalAlignment()"
            :autofocus="$action?->isModalAutofocused()"
            :close-button="$action?->hasModalCloseButton()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            :close-by-escaping="$action?->isModalClosedByEscaping()"
            :description="$action?->getModalDescription()"
            display-classes="block"
            :extra-modal-window-attribute-bag="$action?->getExtraModalWindowAttributeBag()"
            :footer-actions="$action?->getVisibleModalFooterActions()"
            :footer-actions-alignment="$action?->getModalFooterActionsAlignment()"
            :heading="$action?->getModalHeading()"
            :icon="$action?->getModalIcon()"
            :icon-color="$action?->getModalIconColor()"
            :id="$this->getId() . '-form-component-action'"
            :slide-over="$action?->isModalSlideOver()"
            :sticky-footer="$action?->isModalFooterSticky()"
            :sticky-header="$action?->isModalHeaderSticky()"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :wire:key="$action ? $this->getId() . '.' . $action->getComponent()->getStatePath() . '.actions.' . $action->getName() . '.modal' : null"
            x-on:modal-closed.stop="
                if (!$event.detail?.id?.startsWith('{{ $this->getId() }}-')) {
                    return
                }

                const mountedFormComponentActionShouldOpenModal = {{ \Illuminate\Support\Js::from($action && $this->mountedFormComponentActionShouldOpenModal()) }}

                if (mountedFormComponentActionShouldOpenModal) {
                    $wire.unmountFormComponentAction(false, false)
                }
            "
        >
            @if ($action)
                {{ $action->getModalContent() }}

                @if (count(($infolist = $action->getInfolist())?->getComponents() ?? []))
                    {{ $infolist }}
                @elseif ($this->mountedFormComponentActionHasForm(mountedAction: $action))
                    {{ $this->getMountedFormComponentActionForm(mountedAction: $action) }}
                @endif

                {{ $action->getModalContentFooter() }}
            @endif
        </x-filament::modal>
    </form>

    @php
        $this->hasFormsModalRendered = true;
    @endphp
@endif
