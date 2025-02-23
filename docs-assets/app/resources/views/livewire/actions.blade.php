@php use Filament\Actions\ActionGroup; @endphp
@php use Filament\Actions\Action; @endphp
<div class="min-h-screen">
    @if (! count($this->mountedActions))
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

        <div id="slideOverAction" class="p-16 flex items-center justify-center max-w-xl">
            {{ $this->slideOverAction }}
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
                :icon="\Filament\Support\Icons\Heroicon::EllipsisVertical"
                size="sm"
                color="primary"
                button
            />
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
    @endif

    <div id="modal">
        <x-filament-actions::modals/>
    </div>
</div>
