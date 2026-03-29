    <div id="linkSimple" class="p-8 flex items-center justify-center" style="width: 220px">
        <x-filament::link href="#">
            View details
        </x-filament::link>
    </div>

    <div id="linkColors" class="p-8 flex items-center gap-6 max-w-2xl">
        <x-filament::link href="#" color="primary">
            Primary
        </x-filament::link>
        <x-filament::link href="#" color="success">
            Success
        </x-filament::link>
        <x-filament::link href="#" color="danger">
            Danger
        </x-filament::link>
        <x-filament::link href="#" color="gray">
            Gray
        </x-filament::link>
    </div>

    <div id="linkIcon" class="p-8 flex items-center gap-6 max-w-xl">
        <x-filament::link href="#" icon="heroicon-m-arrow-top-right-on-square">
            Open link
        </x-filament::link>
        <x-filament::link href="#" icon="heroicon-m-arrow-down-tray" icon-position="after">
            Download
        </x-filament::link>
    </div>

    <div id="linkBadge" class="p-8 flex items-center gap-6 max-w-xl">
        <x-filament::link href="#" :badge="3">
            Notifications
        </x-filament::link>
        <x-filament::link href="#" :badge="12" badge-color="success">
            Completed
        </x-filament::link>
    </div>

    <div id="linkWeights" class="p-8 flex items-center gap-6 max-w-2xl">
        <x-filament::link href="#" weight="thin">
            Thin
        </x-filament::link>
        <x-filament::link href="#" weight="extralight">
            Extralight
        </x-filament::link>
        <x-filament::link href="#" weight="light">
            Light
        </x-filament::link>
        <x-filament::link href="#" weight="normal">
            Normal
        </x-filament::link>
        <x-filament::link href="#" weight="medium">
            Medium
        </x-filament::link>
        <x-filament::link href="#" weight="semibold">
            Semibold
        </x-filament::link>
        <x-filament::link href="#" weight="bold">
            Bold
        </x-filament::link>
        <x-filament::link href="#" weight="black">
            Black
        </x-filament::link>
    </div>

    <div id="linkSizes" class="p-8 flex items-end gap-6 max-w-xl">
        <x-filament::link href="#" size="sm">
            Small
        </x-filament::link>
        <x-filament::link href="#">
            Medium
        </x-filament::link>
        <x-filament::link href="#" size="lg">
            Large
        </x-filament::link>
        <x-filament::link href="#" size="xl">
            Extra large
        </x-filament::link>
    </div>
