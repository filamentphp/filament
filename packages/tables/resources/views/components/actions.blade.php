@props([
    'table',
    'disabled' => false,
])

<div class="flex shadow-sm">
    @if ($this->isActionable())
        <select
            wire:model="action"
            class="flex-shrink-0 text-sm border-gray-300 rounded sm:text-base focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $disabled ? 'opacity-50' : null }}"
            {{ $disabled ? 'disabled' : null }}
        >
            <option>{{ __('tables::table.actions.placeholder') }}</option>

            @foreach ($table->getVisibleActions() as $action)
                <option value="{{ $action->getName() }}">{{ __($action->getLabel()) }}</option>
            @endforeach
        </select>
    @endif
</div>

<x:tables::modal
    class="w-full max-w-lg"
    :name="static::class . 'TableActionsModal'"
    open="showingActionConfirmationModal"
>
    @if($this->getAction())
        <x-filament::card class="space-y-5">
            <x-filament::card-header :title="__($this->getAction()->getName())" />

            <div class="my-6">
                <p>Are you sure you want to run this action?</p>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <x-filament::button
                    x-on:click.prevent="open = false"
                    color="white"
                >
                    Cancel
                </x-filament::button>
                <x-filament::button
                    x-on:click.prevent="$wire.hasConfirmedAction = true"
                    color="primary"
                >
                    Run Action
                </x-filament::button>
            </div>
        </x-filament::card>
    @endif
</x:tables::modal>

