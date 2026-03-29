    <div id="iconButton" class="p-8 flex items-center gap-4" style="width: 260px">
        <x-filament::icon-button icon="heroicon-m-pencil-square" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-trash" color="danger" label="Delete" />
        <x-filament::icon-button icon="heroicon-m-arrow-path" color="gray" label="Refresh" />
    </div>

    <div id="iconButtonSizes" class="p-8 flex items-end gap-4 max-w-xl">
        <x-filament::icon-button icon="heroicon-m-pencil-square" size="xs" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" size="sm" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" size="lg" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" size="xl" label="Edit" />
    </div>

    <div id="iconButtonColors" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="primary" label="Primary" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="success" label="Success" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="info" label="Info" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="warning" label="Warning" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="danger" label="Danger" />
        <x-filament::icon-button icon="heroicon-m-pencil-square" color="gray" label="Gray" />
    </div>

    <div id="iconButtonBadge" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::icon-button icon="heroicon-m-bell" label="Notifications" :badge="3" />
        <x-filament::icon-button icon="heroicon-m-bell" label="Notifications" :badge="5" badge-color="danger" />
    </div>
