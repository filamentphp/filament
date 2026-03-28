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
