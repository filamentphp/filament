<div>
    <x-forms::container :form="$this->getForm()" class="space-y-6">
        <div class="space-x-3">
            <x-filament::button
                type="submit"
                color="primary"
            >
                {{ $attachButtonLabel }}
            </x-filament::button>

            <x-filament::button
                type="button"
                color="primary"
                wire:click="submit(true)"
            >
                {{ $attachAnotherButtonLabel }}
            </x-filament::button>

            <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($manager)->replace('\\', '\\\\') }}RelationManagerAttachModal')">
                {{ $cancelButtonLabel }}
            </x-filament::button>
        </div>
    </x-forms::container>
</div>
