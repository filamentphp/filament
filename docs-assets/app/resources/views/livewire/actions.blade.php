@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Support\Icons\Heroicon;
@endphp

<div class="min-h-screen">
    <div id="buttonAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->buttonAction }}
    </div>

    <div id="linkAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->linkAction }}
    </div>

    <div id="iconButtonAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->iconButtonAction }}
    </div>

    <div id="badgeAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->badgeAction }}
    </div>

    <div id="dangerAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->dangerAction }}
    </div>

    <div id="largeAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->largeAction }}
    </div>

    <div id="iconAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->iconAction }}
    </div>

    <div id="iconAfterAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->iconAfterAction }}
    </div>

    <div id="badgedAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->badgedAction }}
    </div>

    <div id="successBadgedAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->successBadgedAction }}
    </div>

    <div id="disabledAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->disabledAction }}
    </div>

    <div id="outlinedAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->outlinedAction }}
    </div>

    <div id="confirmationModalAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->confirmationModalAction }}
    </div>

    <div id="confirmationModalCustomTextAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->confirmationModalCustomTextAction }}
    </div>

    <div id="modalIconAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalIconAction }}
    </div>

    <div id="modalFormAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalFormAction }}
    </div>

    <div id="wizardAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->wizardAction }}
    </div>

    <div id="modalSchemaAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalSchemaAction }}
    </div>

    <div id="wideModalAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->wideModalAction }}
    </div>

    <div id="modalAlignmentAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalAlignmentAction }}
    </div>

    <div id="stickyModalHeaderAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->stickyModalHeaderAction }}
    </div>

    <div id="slideOverAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->slideOverAction }}
    </div>

    <div id="slideOverStartAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->slideOverStartAction }}
    </div>

    <div id="modalNoCloseButtonAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalNoCloseButtonAction }}
    </div>

    <div id="disabledFormAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->disabledFormAction }}
    </div>

    <div id="modalIconColorAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->modalIconColorAction }}
    </div>

    <div id="extraFooterActionsAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->extraFooterActionsAction }}
    </div>

    <div id="actionGroup" class="pr-40 pt-8 pb-40 flex items-center justify-center max-w-xl">
        <x-filament-actions::group :actions="[
            Action::make('view'),
            Action::make('edit'),
            Action::make('delete'),
        ]"/>
    </div>

    <div id="customizedActionGroup" class="pr-24 pt-8 pb-40 flex items-center justify-center max-w-xl">
        <x-filament-actions::group
            :actions="[
                Action::make('view'),
                Action::make('edit'),
                Action::make('delete'),
            ]"
            label="More actions"
            :icon="Heroicon::EllipsisVertical"
            size="sm"
            color="primary"
            button
        />
    </div>

    <div id="actionButtonGroup" class="p-16 flex items-center justify-center max-w-xl">
        {{ ActionGroup::make([
            Action::make('edit')
                ->color('gray')
                ->icon(Heroicon::PencilSquare)
                ->hiddenLabel(),
            Action::make('delete')
                ->color('gray')
                ->icon(Heroicon::Trash)
                ->hiddenLabel(),
        ])->buttonGroup() }}
    </div>

    <div id="actionGroupPlacement" class="pr-40 pb-8 pt-40 flex items-center justify-center max-w-xl">
        <x-filament-actions::group
            :actions="[
                Action::make('view'),
                Action::make('edit'),
                Action::make('delete'),
            ]"
            dropdown-placement="top-start"
        />
    </div>

    <div id="nestedActionGroups" class="pr-40 pt-8 pb-40 flex items-center justify-center max-w-xl">
        <x-filament-actions::group
            :actions="[
                ActionGroup::make([
                    Action::make('view'),
                    Action::make('edit'),
                ])->dropdown(false),
                Action::make('delete'),
            ]"
        />
    </div>

    <div id="overlayingChildModalAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->overlayingChildModalAction }}
    </div>

    <div id="actionGroupDropdownWidth" class="pr-40 pt-8 pb-40 flex items-center justify-center max-w-xl">
        <x-filament-actions::group
            :actions="[
                Action::make('view'),
                Action::make('edit'),
                Action::make('duplicate'),
                Action::make('delete'),
            ]"
            :dropdown-width="\Filament\Support\Enums\Width::ExtraSmall"
        />
    </div>

    <div id="actionGroupMaxHeight" class="pr-40 pt-8 pb-72 flex items-center justify-center max-w-xl">
        <x-filament-actions::group
            :actions="[
                Action::make('view')->icon(Heroicon::Eye),
                Action::make('edit')->icon(Heroicon::PencilSquare),
                Action::make('duplicate')->icon(Heroicon::DocumentDuplicate),
                Action::make('archive')->icon(Heroicon::ArchiveBox),
                Action::make('move')->icon(Heroicon::ArrowsRightLeft),
                Action::make('share')->icon(Heroicon::Share),
                Action::make('export')->icon(Heroicon::ArrowDownTray),
                Action::make('print')->icon(Heroicon::Printer),
                Action::make('delete')->icon(Heroicon::Trash)->color('danger'),
            ]"
            max-height="120px"
        />
    </div>

    <div id="authorizationTooltipAction" class="p-16 flex items-center justify-center max-w-xl">
        {{ $this->authorizationTooltipAction }}
    </div>

    <div id="modal">
        <x-filament-actions::modals/>
    </div>

    <style>
        .fi-modal-close-overlay {
            background-color: rgb(129, 129, 129);
        }

        @media (prefers-color-scheme: dark) {
            .fi-modal-close-overlay {
                background-color: rgb(9, 9, 11);
            }
        }
    </style>
</div>
