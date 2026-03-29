    <div id="tabsSimple" class="p-8 max-w-2xl">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item active>
                General
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-bell">
                Notifications
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-cog-6-tooth">
                Settings
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    <div id="tabsBadge" class="p-8 max-w-2xl">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item active>
                All
            </x-filament::tabs.item>

            <x-filament::tabs.item badge="5" badge-color="success">
                Published
            </x-filament::tabs.item>

            <x-filament::tabs.item badge="2" badge-color="warning">
                Draft
            </x-filament::tabs.item>

            <x-filament::tabs.item badge="1" badge-color="danger">
                Trashed
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    <div id="tabsVertical" class="p-8 max-w-2xl">
        <x-filament::tabs label="Content tabs" vertical>
            <x-filament::tabs.item active icon="heroicon-m-user">
                General
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-bell">
                Notifications
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-cog-6-tooth">
                Settings
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    <div id="tabsIconPositionAfter" class="p-8 max-w-2xl">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item icon="heroicon-m-user" icon-position="after" active>
                Profile
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-bell" icon-position="after">
                Notifications
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-cog-6-tooth" icon-position="after">
                Settings
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-shield-check" icon-position="after">
                Security
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    <div id="tabsIcon" class="p-8 max-w-2xl">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item icon="heroicon-m-user" active>
                Profile
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-bell">
                Notifications
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-cog-6-tooth">
                Settings
            </x-filament::tabs.item>

            <x-filament::tabs.item icon="heroicon-m-shield-check">
                Security
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>
