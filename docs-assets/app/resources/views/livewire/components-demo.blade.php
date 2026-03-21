<div class="min-h-screen">
    {{-- Buttons --}}
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

    {{-- Icon Buttons --}}
    <div id="iconButton" class="p-8 flex items-center gap-4" style="width: 260px">
        <x-filament::icon-button icon="heroicon-m-pencil-square" label="Edit" />
        <x-filament::icon-button icon="heroicon-m-trash" color="danger" label="Delete" />
        <x-filament::icon-button icon="heroicon-m-arrow-path" color="gray" label="Refresh" />
    </div>

    {{-- Badges --}}
    <div id="badgeSimple" class="p-8 flex items-center justify-center" style="width: 180px">
        <x-filament::badge>
            New
        </x-filament::badge>
    </div>

    <div id="badgeColors" class="p-8 flex items-center gap-4 max-w-2xl">
        <x-filament::badge color="primary">
            Primary
        </x-filament::badge>
        <x-filament::badge color="success">
            Active
        </x-filament::badge>
        <x-filament::badge color="info">
            Info
        </x-filament::badge>
        <x-filament::badge color="warning">
            Pending
        </x-filament::badge>
        <x-filament::badge color="danger">
            Failed
        </x-filament::badge>
        <x-filament::badge color="gray">
            Draft
        </x-filament::badge>
    </div>

    <div id="badgeIcon" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::badge icon="heroicon-m-sparkles">
            Featured
        </x-filament::badge>
        <x-filament::badge icon="heroicon-m-check-circle" color="success">
            Verified
        </x-filament::badge>
    </div>

    {{-- Link --}}
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

    {{-- Section --}}
    <div id="sectionSimple" class="p-8 max-w-2xl">
        <x-filament::section>
            <x-slot name="heading">
                User details
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">This is the section content. Sections group related content together with an optional heading, description, and icon.</p>
        </x-filament::section>
    </div>

    <div id="sectionDescription" class="p-8 max-w-2xl">
        <x-filament::section icon="heroicon-o-user">
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="description">
                This is all the information we hold about the user.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage user profile information, including name, email address, and account settings.</p>
        </x-filament::section>
    </div>

    {{-- Tabs --}}
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

    {{-- Input --}}
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

    {{-- Fieldset --}}
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

    {{-- Loading indicator --}}
    <div id="loadingIndicator" class="p-8 flex items-center justify-center" style="width: 160px">
        <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
    </div>

    {{-- Dropdown --}}
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

    {{-- Breadcrumbs --}}
    <div id="breadcrumbsSimple" class="p-8 max-w-2xl">
        <x-filament::breadcrumbs :breadcrumbs="[
            '/admin' => 'Dashboard',
            '/admin/posts' => 'Posts',
            '' => 'Introducing Filament v4',
        ]" />
    </div>

    {{-- Select --}}
    <div id="selectSimple" class="p-8 max-w-md">
        <x-filament::input.wrapper>
            <x-filament::input.select>
                <option value="">Select a status</option>
                <option value="draft">Draft</option>
                <option value="reviewing">Reviewing</option>
                <option value="published">Published</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    {{-- Checkbox --}}
    <div id="checkboxSimple" class="p-8 max-w-md space-y-3">
        <label class="flex items-center gap-3">
            <x-filament::input.checkbox checked />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Send email notifications</span>
        </label>
        <label class="flex items-center gap-3">
            <x-filament::input.checkbox />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Enable two-factor authentication</span>
        </label>
        <label class="flex items-center gap-3">
            <x-filament::input.checkbox checked />
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Accept terms and conditions</span>
        </label>
    </div>

    {{-- Callout --}}
    <div id="calloutSimple" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-information-circle"
            color="info"
        >
            <x-slot name="heading">
                Important Notice
            </x-slot>

            <x-slot name="description">
                Please read this information carefully before proceeding.
            </x-slot>
        </x-filament::callout>
    </div>

    <div id="calloutColors" class="p-8 max-w-2xl space-y-4">
        <x-filament::callout
            icon="heroicon-o-check-circle"
            color="success"
        >
            <x-slot name="heading">
                Success
            </x-slot>

            <x-slot name="description">
                Your changes have been saved.
            </x-slot>
        </x-filament::callout>

        <x-filament::callout
            icon="heroicon-o-exclamation-circle"
            color="warning"
        >
            <x-slot name="heading">
                Warning
            </x-slot>

            <x-slot name="description">
                Please review the following items.
            </x-slot>
        </x-filament::callout>

        <x-filament::callout
            icon="heroicon-o-x-circle"
            color="danger"
        >
            <x-slot name="heading">
                Error
            </x-slot>

            <x-slot name="description">
                Something went wrong. Please try again.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with footer --}}
    <div id="calloutFooter" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-exclamation-circle"
            color="warning"
        >
            <x-slot name="heading">
                Subscription expiring
            </x-slot>

            <x-slot name="description">
                Your subscription will expire in 7 days. Renew now to avoid interruption.
            </x-slot>

            <x-slot name="footer">
                <x-filament::button size="sm">
                    Renew now
                </x-filament::button>
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with controls --}}
    <div id="calloutControls" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-information-circle"
            color="info"
        >
            <x-slot name="heading">
                Dismissible Callout
            </x-slot>

            <x-slot name="description">
                This callout can be dismissed using the control in the top-right corner.
            </x-slot>

            <x-slot name="controls">
                <x-filament::icon-button
                    icon="heroicon-m-x-mark"
                    color="gray"
                    label="Dismiss"
                />
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout without icon --}}
    <div id="calloutNoIcon" class="p-8 max-w-2xl">
        <x-filament::callout>
            <x-slot name="heading">
                No Icon
            </x-slot>

            <x-slot name="description">
                This callout has no icon.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with heading only --}}
    <div id="calloutHeadingOnly" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-information-circle"
            color="info"
        >
            <x-slot name="heading">
                Simple Notice
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with custom icon --}}
    <div id="calloutCustomIconBlade" class="p-8 max-w-2xl">
        <x-filament::callout icon="heroicon-o-sparkles">
            <x-slot name="heading">
                Tip
            </x-slot>

            <x-slot name="description">
                You can use custom icons for your callouts.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with custom icon color --}}
    <div id="calloutIconColor" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-shield-check"
            icon-color="success"
        >
            <x-slot name="heading">
                Custom Icon Color
            </x-slot>

            <x-slot name="description">
                The icon color is independent of the background color.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with icon sizes --}}
    <div id="calloutIconSizes" class="p-8 max-w-2xl space-y-4">
        <x-filament::callout
            icon="heroicon-m-information-circle"
            icon-size="sm"
            color="info"
        >
            <x-slot name="heading">
                Small Icon
            </x-slot>

            <x-slot name="description">
                This callout has a smaller icon.
            </x-slot>
        </x-filament::callout>

        <x-filament::callout
            icon="heroicon-m-information-circle"
            icon-size="md"
            color="info"
        >
            <x-slot name="heading">
                Medium Icon
            </x-slot>

            <x-slot name="description">
                This callout has a medium icon.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Callout with custom background color --}}
    <div id="calloutPrimaryColor" class="p-8 max-w-2xl">
        <x-filament::callout
            icon="heroicon-o-star"
            color="primary"
        >
            <x-slot name="heading">
                Announcement
            </x-slot>

            <x-slot name="description">
                A special announcement with a custom color.
            </x-slot>
        </x-filament::callout>
    </div>

    {{-- Input disabled --}}
    <div id="inputDisabled" class="p-8 max-w-md">
        <x-filament::input.wrapper disabled>
            <x-filament::input
                type="text"
                value="john@example.com"
                disabled
            />
        </x-filament::input.wrapper>
    </div>

    {{-- Input with suffix icon color --}}
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

    {{-- Badge sizes --}}
    <div id="badgeSizes" class="p-8 flex items-end gap-4 max-w-xl">
        <x-filament::badge size="xs">
            Extra small
        </x-filament::badge>
        <x-filament::badge size="sm">
            Small
        </x-filament::badge>
        <x-filament::badge>
            Medium
        </x-filament::badge>
        <x-filament::badge size="lg">
            Large
        </x-filament::badge>
        <x-filament::badge size="xl">
            Extra large
        </x-filament::badge>
    </div>

    {{-- Empty State --}}
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

    {{-- Modal --}}
    <div id="modalSimple" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 400px;">
        <x-filament::modal id="demo-modal">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Confirm your action
            </x-slot>

            <x-slot name="description">
                Are you sure you want to proceed? This action cannot be undone.
            </x-slot>

            <x-slot name="footerActions">
                <x-filament::button>
                    Confirm
                </x-filament::button>

                <x-filament::button color="gray">
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with icon --}}
    <div id="modalIcon" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 450px;">
        <x-filament::modal id="demo-modal-icon" icon="heroicon-o-exclamation-triangle" icon-color="danger">
            <x-slot name="trigger">
                <x-filament::button color="danger">
                    Delete record
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Delete record
            </x-slot>

            <x-slot name="description">
                Are you sure you would like to do this?
            </x-slot>

            <x-slot name="footerActions">
                <x-filament::button color="danger">
                    Confirm
                </x-filament::button>

                <x-filament::button color="gray">
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal slide-over --}}
    <div id="modalSlideOver" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 450px;">
        <x-filament::modal id="demo-modal-slide-over" slide-over>
            <x-slot name="trigger">
                <x-filament::button>
                    Open slide-over
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Edit user
            </x-slot>

            <x-slot name="description">
                Update the user's profile information below.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                This is the slide-over content area. You can add forms, text, or any other content here. The slide-over panel slides in from the right side of the screen.
            </p>

            <x-slot name="footerActions">
                <x-filament::button>
                    Save changes
                </x-filament::button>

                <x-filament::button color="gray">
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Empty state with actions --}}
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

    {{-- Button outlined colors --}}
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

    {{-- Dropdown with colors --}}
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

    {{-- Section variants --}}
    <div id="sectionCollapsible" class="p-8 max-w-2xl">
        <x-filament::section collapsible>
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="description">
                View and manage the user's profile information.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage user profile information, including name, email address, and account settings.</p>
        </x-filament::section>
    </div>

    <div id="sectionCollapsed" class="p-8 max-w-2xl">
        <x-filament::section
            collapsible
            collapsed
        >
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="description">
                View and manage the user's profile information.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">This content is hidden when collapsed.</p>
        </x-filament::section>
    </div>

    <div id="sectionAside" class="p-8 max-w-4xl">
        <x-filament::section aside>
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="description">
                This is all the information we hold about the user.
            </x-slot>

            <div class="space-y-4">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" placeholder="Dan Harrin" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="email" placeholder="dan@filamentphp.com" />
                </x-filament::input.wrapper>
            </div>
        </x-filament::section>
    </div>

    {{-- Tabs vertical --}}
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

    {{-- Icon Button variants --}}
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

    {{-- Button badge --}}
    <div id="buttonBadge" class="p-8 flex items-center gap-4 max-w-xl">
        <x-filament::button :badge="3">
            Notifications
        </x-filament::button>
        <x-filament::button :badge="5" badge-color="danger">
            Errors
        </x-filament::button>
    </div>

    {{-- Link badge --}}
    <div id="linkBadge" class="p-8 flex items-center gap-6 max-w-xl">
        <x-filament::link href="#" :badge="3">
            Notifications
        </x-filament::link>
        <x-filament::link href="#" :badge="12" badge-color="success">
            Completed
        </x-filament::link>
    </div>

    {{-- Section with icon --}}
    <div id="sectionIcon" class="p-8 max-w-2xl">
        <x-filament::section icon="heroicon-o-user">
            <x-slot name="heading">
                User details
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage user profile information, including name, email address, and account settings.</p>
        </x-filament::section>
    </div>

    <div id="sectionIconColor" class="p-8 max-w-2xl">
        <x-filament::section icon="heroicon-o-user" icon-color="info">
            <x-slot name="heading">
                User details
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage user profile information, including name, email address, and account settings.</p>
        </x-filament::section>
    </div>

    {{-- Section with afterHeader --}}
    <div id="sectionAfterHeader" class="p-8 max-w-2xl">
        <x-filament::section>
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="afterHeader">
                <x-filament::input.wrapper>
                    <x-filament::input.select>
                        <option value="1">Dan Harrin</option>
                        <option value="2">Ryan Chandler</option>
                        <option value="3">Zep Fietje</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">View and manage user profile information, including name, email address, and account settings.</p>
        </x-filament::section>
    </div>

    {{-- Section content-before --}}
    <div id="sectionContentBefore" class="p-8 max-w-4xl">
        <x-filament::section aside content-before>
            <x-slot name="heading">
                User details
            </x-slot>

            <x-slot name="description">
                This is all the information we hold about the user.
            </x-slot>

            <div class="space-y-4">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" placeholder="Dan Harrin" />
                </x-filament::input.wrapper>

                <x-filament::input.wrapper>
                    <x-filament::input type="email" placeholder="dan@filamentphp.com" />
                </x-filament::input.wrapper>
            </div>
        </x-filament::section>
    </div>

    {{-- Empty state icon color --}}
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

    {{-- Empty state icon sizes --}}
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

    {{-- Empty state without container --}}
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

    {{-- Section icon sizes --}}
    <div id="sectionIconSizes" class="p-8 max-w-2xl space-y-4">
        <x-filament::section icon="heroicon-m-user" icon-size="sm">
            <x-slot name="heading">
                User details
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">This section has a small icon.</p>
        </x-filament::section>

        <x-filament::section icon="heroicon-m-user" icon-size="md">
            <x-slot name="heading">
                User details
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">This section has a medium icon.</p>
        </x-filament::section>
    </div>

    {{-- Dropdown icon colors --}}
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

    {{-- Empty state with description --}}
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

    {{-- Dropdown with icons and badges --}}
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

    {{-- Dropdown with images --}}
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

    {{-- Dropdown with custom width --}}
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

    {{-- Dropdown with max-height --}}
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

    {{-- Tabs with icon position after --}}
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

    {{-- Tabs with icons (dedicated) --}}
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

    {{-- Modal with heading and description --}}
    <div id="modalHeading" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 350px;">
        <x-filament::modal id="demo-modal-heading">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Edit your profile
            </x-slot>

            <x-slot name="description">
                Update your account details and preferences below.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">Your profile information will be visible to other users in your organization.</p>

            <x-slot name="footerActions">
                <x-filament::button>
                    Save changes
                </x-filament::button>

                <x-filament::button color="gray">
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with footer --}}
    <div id="modalFooter" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 400px;">
        <x-filament::modal id="demo-modal-footer">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Edit profile
            </x-slot>

            <x-slot name="description">
                Update your account details below.
            </x-slot>

            <p class="text-sm text-gray-500 dark:text-gray-400">Your profile information will be visible to other users in your organization. Please ensure all details are accurate and up to date.</p>

            <x-slot name="footer">
                <div class="flex items-center justify-between w-full">
                    <x-filament::link href="#" color="gray" size="sm">
                        Need help?
                    </x-filament::link>

                    <div class="flex gap-x-3">
                        <x-filament::button color="gray">
                            Cancel
                        </x-filament::button>

                        <x-filament::button>
                            Save changes
                        </x-filament::button>
                    </div>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with center alignment --}}
    <div id="modalAlignment" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 400px;">
        <x-filament::modal id="demo-modal-alignment" alignment="center">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Payment successful
            </x-slot>

            <x-slot name="description">
                Your payment of $49.99 has been processed successfully. A confirmation email has been sent to your inbox.
            </x-slot>

            <x-slot name="footerActions">
                <x-filament::button>
                    View receipt
                </x-filament::button>

                <x-filament::button color="gray">
                    Close
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with wide width --}}
    <div id="modalWidth" class="p-8 flex items-center justify-center" style="min-height: 500px;">
        <x-filament::modal id="demo-modal-width" width="5xl">
            <x-slot name="trigger">
                <x-filament::button>
                    Open wide modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Activity log
            </x-slot>

            <x-slot name="description">
                Review recent changes made to this resource.
            </x-slot>

            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                <table class="w-full table-fixed text-sm text-start">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="px-4 py-2 text-start font-medium text-gray-600 dark:text-gray-400">User</th>
                            <th class="px-4 py-2 text-start font-medium text-gray-600 dark:text-gray-400">Action</th>
                            <th class="px-4 py-2 text-start font-medium text-gray-600 dark:text-gray-400">Resource</th>
                            <th class="px-4 py-2 text-start font-medium text-gray-600 dark:text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        <tr>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">Dan Harrin</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Updated status</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Post #142</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Mar 15, 2026</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">Ryan Chandler</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Created record</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Post #143</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Mar 14, 2026</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-gray-950 dark:text-white">Zep Fietje</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Deleted record</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Post #139</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">Mar 13, 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-slot name="footerActions">
                <x-filament::button color="gray">
                    Close
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with sticky header --}}
    <div id="modalStickyHeader" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 500px;">
        <x-filament::modal id="demo-modal-sticky-header" sticky-header width="lg">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Terms and conditions
            </x-slot>

            <x-slot name="description">
                Please read the following terms carefully.
            </x-slot>

            <div class="space-y-4 text-sm text-gray-500 dark:text-gray-400">
                <p><strong class="text-gray-950 dark:text-white">1. Acceptance of Terms</strong></p>
                <p>By accessing and using this service, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by these terms, please do not use this service.</p>

                <p><strong class="text-gray-950 dark:text-white">2. Use License</strong></p>
                <p>Permission is granted to temporarily use this service for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title.</p>

                <p><strong class="text-gray-950 dark:text-white">3. Disclaimer</strong></p>
                <p>The materials on this service are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability.</p>

                <p><strong class="text-gray-950 dark:text-white">4. Limitations</strong></p>
                <p>In no event shall we or our suppliers be liable for any damages arising out of the use or inability to use the materials on this service, even if we have been notified of the possibility of such damage.</p>

                <p><strong class="text-gray-950 dark:text-white">5. Revisions and Errata</strong></p>
                <p>The materials appearing on this service may include technical, typographical, or photographic errors. We do not warrant that any of the materials are accurate, complete, or current.</p>
            </div>

            <x-slot name="footerActions">
                <x-filament::button>
                    Accept
                </x-filament::button>

                <x-filament::button color="gray">
                    Decline
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Modal with sticky footer --}}
    <div id="modalStickyFooter" class="p-8 flex items-center justify-center max-w-xl" style="min-height: 500px;">
        <x-filament::modal id="demo-modal-sticky-footer" sticky-footer width="lg">
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>

            <x-slot name="heading">
                Privacy policy
            </x-slot>

            <div class="space-y-4 text-sm text-gray-500 dark:text-gray-400">
                <p><strong class="text-gray-950 dark:text-white">1. Information Collection</strong></p>
                <p>We collect information from you when you register on our site, place an order, subscribe to our newsletter, or fill out a form. When ordering or registering, you may be asked to enter your name, email address, or mailing address.</p>

                <p><strong class="text-gray-950 dark:text-white">2. Information Use</strong></p>
                <p>Any of the information we collect from you may be used to personalize your experience, improve our website, improve customer service, and process transactions.</p>

                <p><strong class="text-gray-950 dark:text-white">3. Information Protection</strong></p>
                <p>We implement a variety of security measures to maintain the safety of your personal information when you place an order or enter, submit, or access your personal information.</p>

                <p><strong class="text-gray-950 dark:text-white">4. Cookie Usage</strong></p>
                <p>We use cookies to understand and save your preferences for future visits, keep track of advertisements, and compile aggregate data about site traffic and interaction.</p>

                <p><strong class="text-gray-950 dark:text-white">5. Third-Party Disclosure</strong></p>
                <p>We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information unless we provide users with advance notice.</p>
            </div>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-x-3">
                    <x-filament::button color="gray">
                        Close
                    </x-filament::button>

                    <x-filament::button>
                        I agree
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Link weights --}}
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

    {{-- Link sizes --}}
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

    {{-- Avatar --}}
    <div id="avatarSimple" class="p-8 flex items-center gap-4" style="width: 230px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41837763?v=4"
            alt="Ryan Chandler"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/44533235?v=4"
            alt="Zep Fietje"
        />
    </div>

    <div id="avatarSquare" class="p-8 flex items-center gap-4" style="width: 230px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            :circular="false"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41837763?v=4"
            alt="Ryan Chandler"
            :circular="false"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/44533235?v=4"
            alt="Zep Fietje"
            :circular="false"
        />
    </div>

    <div id="avatarSizes" class="p-8 flex items-end gap-4" style="width: 250px">
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="sm"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="md"
        />
        <x-filament::avatar
            src="https://avatars.githubusercontent.com/u/41773797?v=4"
            alt="Dan Harrin"
            size="lg"
        />
    </div>
</div>
