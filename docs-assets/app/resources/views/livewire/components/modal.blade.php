<x-filament::modal id="demo-modal">
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

<x-filament::modal id="demo-modal-icon" icon="heroicon-o-exclamation-triangle" icon-color="danger">
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

<x-filament::modal id="demo-modal-slide-over" slide-over>
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

<x-filament::modal id="demo-modal-heading">
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

<x-filament::modal id="demo-modal-footer">
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

<x-filament::modal id="demo-modal-alignment" alignment="center">
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

<x-filament::modal id="demo-modal-width" width="5xl">
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

<x-filament::modal id="demo-modal-sticky-header" sticky-header width="lg">
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

<x-filament::modal id="demo-modal-sticky-footer" sticky-footer width="lg">
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
