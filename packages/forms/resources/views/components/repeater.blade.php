<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div {{ $attributes->merge($getExtraAttributes())->class(['space-y-2 filament-forms-repeater-component']) }}>
        @if (count($containers = $getChildComponentContainers()))
            <ul
                class="space-y-2"
                wire:sortable
                wire:end="dispatchFormEvent('repeater::moveItems', '{{ $getStatePath() }}', $event.target.sortable.toArray())"
            >
                @foreach ($containers as $uuid => $item)
                    <li
                        wire:key="{{ $item->getStatePath() }}"
                        wire:sortable.item="{{ $uuid }}"
                        @class([
                            'relative p-6 bg-white shadow-sm rounded-lg border border-gray-300',
                            'dark:bg-gray-700 dark:border-gray-600' => config('forms.dark_mode'),
                        ])
                    >
                        {{ $item }}

                        @unless ($isItemDeletionDisabled() && ($isItemMovementDisabled() && ($loop->count <= 1)))
                            <div @class([
                                'absolute top-0 right-0 h-6 flex divide-x rounded-bl-lg rounded-tr-lg border-gray-300 border-b border-l overflow-hidden rtl:border-l-0 rtl:border-r rtl:right-auto rtl:left-0 rtl:rounded-bl-none rtl:rounded-br-lg rtl:rounded-tr-none rtl:rounded-tl-lg',
                                'dark:border-gray-600 dark:divide-gray-600' => config('forms.dark_mode'),
                            ])>
                                @unless ($isItemMovementDisabled())
                                    <button
                                        wire:sortable.handle
                                        wire:keydown.prevent.arrow-up="dispatchFormEvent('repeater::moveItemUp', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        wire:keydown.prevent.arrow-down="dispatchFormEvent('repeater::moveItemDown', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        @class([
                                            'flex items-center justify-center w-6 text-gray-800 cursor-grab hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600',
                                            'dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:text-primary-600' => config('forms.dark_mode'),
                                        ])
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.move_item_down.label') }}
                                        </span>

                                        <div class="flex flex-col">
                                            <x-heroicon-o-dots-horizontal class="w-4 h-4" />
                                            <x-heroicon-o-dots-horizontal class="w-4 h-4 -mt-[0.6875rem]" />
                                        </div>
                                    </button>
                                @endunless

                                @unless ($isItemDeletionDisabled())
                                    <button
                                        wire:click="dispatchFormEvent('repeater::deleteItem', '{{ $getStatePath() }}', '{{ $uuid }}')"
                                        type="button"
                                        @class([
                                            'flex items-center justify-center w-6 text-danger-600 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-white focus:ring-primary-600 focus:text-danger-600 focus:bg-primary-50 focus:border-primary-600',
                                            'dark:hover:bg-gray-600' => config('forms.dark_mode'),
                                        ])
                                    >
                                        <span class="sr-only">
                                            {{ __('forms::components.repeater.buttons.delete_item.label') }}
                                        </span>

                                        <x-heroicon-s-trash class="w-4 h-4" />
                                    </button>
                                @endunless
                            </div>
                        @endunless
                    </li>
                @endforeach
            </ul>
        @endif

        @if ((blank($getMaxItems()) || ($getMaxItems() > $getItemsCount())) && (! $isItemCreationDisabled()))
            <button
                wire:click="dispatchFormEvent('repeater::createItem', '{{ $getStatePath() }}')"
                type="button"
                @class([
                    'w-full h-9 px-4 inline-flex space-x-1 rtl:space-x-reverse items-center justify-center font-medium tracking-tight rounded-lg text-gray-800 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600',
                    'dark:bg-gray-800 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 dark:focus:ring-offset-0' => config('forms.dark_mode'),
                ])
            >
                <x-heroicon-s-plus class="w-5 h-5" />

                <span>
                    {{ $getCreateItemButtonLabel() }}
                </span>
            </button>
        @endif
    </div>
</x-dynamic-component>
