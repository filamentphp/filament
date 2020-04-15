<div>
    <div class="sm:flex sm:items-start">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-200 sm:mx-0 sm:h-10 sm:w-10">
            <x-heroicon-o-exclamation class="h-6 w-6 text-red-600" />
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium">
                {{ $title }}
            </h3>
            <div class="mt-2">
                <p class="text-sm leading-5 text-gray-500 dark:text-gray-400">
                    {{ $message }}
                </p>
            </div>
        </div>
    </div>
    <ul class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
        <li>
            <button type="button" wire:click.prevent="delete" class="btn btn-danger w-full md:w-auto">{{ __('Delete') }}</button>
        </li>
        <li class="mt-2 sm:mt-0 sm:mr-4">
            <button type="button" @click.prevent="open = false" class="btn btn-action w-full md:w-auto">{{ __('Cancel') }}</button>
        </li>
    </ul>
</div>