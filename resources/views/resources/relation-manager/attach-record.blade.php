<div>
    <x-forms::container :form="$this->getForm()" class="space-y-6">
        <div class="space-y-3 sm:space-x-3 sm:space-y-0">
            <x-filament::button
                type="submit"
                color="primary"
                class="w-full sm:w-auto"
            >
                {{ $attachButtonLabel }}
            </x-filament::button>

            <x-filament::button
                type="button"
                color="primary"
                wire:click="attach(true)"
                class="w-full sm:w-auto"
            >
                {{ $attachAnotherButtonLabel }}
            </x-filament::button>

            <x-filament::button
                x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerAttachModal')"
                class="w-full sm:w-auto"
            >
                {{ $cancelButtonLabel }}
            </x-filament::button>
        </div>
    </x-forms::container>
</div>
