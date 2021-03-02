<div>
    <x-filament::app-header :title="__($title)" />

    <x-filament::app-content>
        <x-filament::card>
            <x-forms::container :form="$this->getForm()" class="space-y-6">
                <x-filament::button
                    type="submit"
                    color="primary"
                >
                    {{ __('filament::edit-account.buttons.save.label') }}
                </x-filament::button>
            </x-forms::container>
        </x-filament::card>
    </x-filament::app-content>
</div>
