    <div id="fieldsetSimple" class="p-8 max-w-2xl">
        <x-filament::fieldset>
            <x-slot name="label">
                Address
            </x-slot>

            <div class="space-y-4">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" placeholder="123 Main Street" />
                </x-filament::input.wrapper>

                <div class="grid grid-cols-2 gap-4">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="London" />
                    </x-filament::input.wrapper>

                    <x-filament::input.wrapper>
                        <x-filament::input type="text" placeholder="SW1A 1AA" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-filament::fieldset>
    </div>
