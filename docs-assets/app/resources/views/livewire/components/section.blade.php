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
