    <div id="inputSimple" class="p-8 max-w-md">
        <x-filament::input.wrapper>
            <x-filament::input type="text" placeholder="Enter your name" />
        </x-filament::input.wrapper>
    </div>

    <div id="inputPrefix" class="p-8 max-w-md">
        <x-filament::input.wrapper prefix="https://">
            <x-filament::input type="text" placeholder="example.com" />
        </x-filament::input.wrapper>
    </div>

    <div id="inputIcon" class="p-8 max-w-md">
        <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
            <x-filament::input type="text" placeholder="Search..." />
        </x-filament::input.wrapper>
    </div>

    <div id="inputDisabled" class="p-8 max-w-md">
        <x-filament::input.wrapper disabled>
            <x-filament::input
                type="text"
                value="john@example.com"
                disabled
            />
        </x-filament::input.wrapper>
    </div>

    <div id="inputSuffixIconColor" class="p-8 max-w-md">
        <x-filament::input.wrapper
            suffix-icon="heroicon-m-check-circle"
            suffix-icon-color="success"
        >
            <x-filament::input
                type="text"
                value="https://filamentphp.com"
            />
        </x-filament::input.wrapper>
    </div>
