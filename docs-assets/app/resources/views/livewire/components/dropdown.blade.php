    <div id="dropdownSimple" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 280px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Options
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-m-pencil-square">
                    Edit
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-document-duplicate">
                    Duplicate
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger">
                    Delete
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownColors" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 340px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Actions
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item color="primary" icon="heroicon-m-eye">
                    View details
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="info" icon="heroicon-m-pencil-square">
                    Edit record
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="success" icon="heroicon-m-check-circle">
                    Approve
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="warning" icon="heroicon-m-exclamation-triangle">
                    Archive
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item color="danger" icon="heroicon-m-trash">
                    Delete
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownIconColors" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 380px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Actions
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-m-eye" icon-color="primary">
                    View
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" icon-color="info">
                    Edit
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-document-duplicate" icon-color="success">
                    Duplicate
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-archive-box" icon-color="warning">
                    Archive
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-trash" icon-color="danger">
                    Delete
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownIcons" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 320px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Actions
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-m-eye">
                    View
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-pencil-square">
                    Edit
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-document-duplicate">
                    Duplicate
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-trash" color="danger">
                    Delete
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownBadge" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 280px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Menu
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-m-bell" :badge="5">
                    Notifications
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-envelope" :badge="3" badge-color="success">
                    Messages
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-cog-6-tooth">
                    Settings
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownImage" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 300px;">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::button>
                    Team
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item image="https://avatars.githubusercontent.com/u/41773797?v=4">
                    Dan Harrin
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item image="https://avatars.githubusercontent.com/u/44533235?v=4">
                    Ryan Chandler
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item image="https://avatars.githubusercontent.com/u/22632550?v=4">
                    Zep Fietje
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownWidth" class="p-8 pt-4 flex items-start justify-center max-w-2xl" style="min-height: 340px;">
        <x-filament::dropdown width="lg">
            <x-slot name="trigger">
                <x-filament::button>
                    Manage
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item icon="heroicon-m-pencil-square">
                    Edit profile
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-cog-6-tooth">
                    Account settings
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-bell">
                    Notification preferences
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item icon="heroicon-m-arrow-right-on-rectangle" color="danger">
                    Sign out
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>

    <div id="dropdownMaxHeight" class="p-8 pt-4 flex items-start justify-center max-w-xl" style="min-height: 320px;">
        <x-filament::dropdown max-height="200px">
            <x-slot name="trigger">
                <x-filament::button>
                    Select country
                </x-filament::button>
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item>Argentina</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Australia</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Brazil</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Canada</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>France</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Germany</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>India</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Japan</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>Mexico</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>United Kingdom</x-filament::dropdown.list.item>
                <x-filament::dropdown.list.item>United States</x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>
