    <div id="buttonSimple" class="p-8 flex items-center justify-center max-w-xl">
        <x-filament::button>
            New user
        </x-filament::button>
    </div>

    <div id="buttonSizes" class="p-8 flex items-center gap-4 max-w-2xl">
        <x-filament::button size="xs">
            Extra small
        </x-filament::button>
        <x-filament::button size="sm">
            Small
        </x-filament::button>
        <x-filament::button>
            Medium
        </x-filament::button>
        <x-filament::button size="lg">
            Large
        </x-filament::button>
        <x-filament::button size="xl">
            Extra large
        </x-filament::button>
    </div>

    <div id="buttonColors" class="p-8 flex items-center gap-4 max-w-2xl">
        <x-filament::button color="primary">
            Primary
        </x-filament::button>
        <x-filament::button color="success">
            Success
        </x-filament::button>
        <x-filament::button color="info">
            Info
        </x-filament::button>
        <x-filament::button color="warning">
            Warning
        </x-filament::button>
        <x-filament::button color="danger">
            Danger
        </x-filament::button>
        <x-filament::button color="gray">
            Gray
        </x-filament::button>
    </div>

    <div id="buttonOutlined" class="p-8 flex items-center gap-4 max-w-2xl">
        <x-filament::button color="primary" outlined>
            Primary
        </x-filament::button>
        <x-filament::button color="success" outlined>
            Success
        </x-filament::button>
        <x-filament::button color="danger" outlined>
            Danger
        </x-filament::button>
        <x-filament::button color="gray" outlined>
            Gray
        </x-filament::button>
    </div>

    <div id="buttonIcon" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::button icon="heroicon-m-sparkles">
            Star
        </x-filament::button>
        <x-filament::button icon="heroicon-m-pencil-square" icon-position="after">
            Edit
        </x-filament::button>
    </div>

    <div id="buttonOutlinedColors" class="p-8 flex flex-wrap items-center gap-3 max-w-2xl">
        <x-filament::button outlined color="primary">
            Primary
        </x-filament::button>

        <x-filament::button outlined color="success">
            Success
        </x-filament::button>

        <x-filament::button outlined color="info">
            Info
        </x-filament::button>

        <x-filament::button outlined color="warning">
            Warning
        </x-filament::button>

        <x-filament::button outlined color="danger">
            Danger
        </x-filament::button>

        <x-filament::button outlined color="gray">
            Gray
        </x-filament::button>
    </div>

    <div id="buttonBadge" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::button :badge="3">
            Notifications
        </x-filament::button>
        <x-filament::button :badge="5" badge-color="danger">
            Errors
        </x-filament::button>
    </div>
