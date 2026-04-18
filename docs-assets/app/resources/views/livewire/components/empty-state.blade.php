    <div id="emptyStateSimple" class="p-8 max-w-2xl">
        <x-filament::empty-state
            icon="heroicon-o-document-text"
        >
            <x-slot name="heading">
                No posts yet
            </x-slot>

            <x-slot name="description">
                Get started by creating your first post.
            </x-slot>

            <x-slot name="footer">
                <x-filament::button icon="heroicon-m-plus">
                    Create post
                </x-filament::button>
            </x-slot>
        </x-filament::empty-state>
    </div>

    <div id="emptyStateActions" class="p-8 max-w-xl">
        <x-filament::empty-state
            icon="heroicon-o-document-text"
            icon-color="primary"
        >
            <x-slot name="heading">
                No posts yet
            </x-slot>

            <x-slot name="description">
                Get started by creating your first blog post.
            </x-slot>

            <x-slot name="footer">
                <x-filament::button icon="heroicon-m-plus">
                    Create post
                </x-filament::button>
            </x-slot>
        </x-filament::empty-state>
    </div>

    <div id="emptyStateIconColor" class="p-8 max-w-2xl">
        <x-filament::empty-state
            icon="heroicon-o-user"
            icon-color="info"
        >
            <x-slot name="heading">
                No users yet
            </x-slot>
        </x-filament::empty-state>
    </div>

    <div id="emptyStateIconSizes" class="p-8 max-w-2xl space-y-4">
        <x-filament::empty-state
            icon="heroicon-m-user"
            icon-size="sm"
        >
            <x-slot name="heading">
                Small icon
            </x-slot>
        </x-filament::empty-state>

        <x-filament::empty-state
            icon="heroicon-m-user"
            icon-size="md"
        >
            <x-slot name="heading">
                Medium icon
            </x-slot>
        </x-filament::empty-state>
    </div>

    <div id="emptyStateNotContained" class="p-8 max-w-2xl">
        <x-filament::empty-state
            icon="heroicon-o-document-text"
            :contained="false"
        >
            <x-slot name="heading">
                No posts yet
            </x-slot>

            <x-slot name="description">
                Get started by creating your first post.
            </x-slot>
        </x-filament::empty-state>
    </div>

    <div id="emptyStateDescription" class="p-8 max-w-2xl">
        <x-filament::empty-state
            icon="heroicon-o-document-text"
        >
            <x-slot name="heading">
                No posts yet
            </x-slot>

            <x-slot name="description">
                Get started by creating your first post.
            </x-slot>
        </x-filament::empty-state>
    </div>
