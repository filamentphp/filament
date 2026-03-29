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
