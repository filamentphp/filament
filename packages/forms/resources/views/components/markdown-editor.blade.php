<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $id = $getId();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    <div
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('markdown-editor', 'filament/forms') }}"
        x-data="markdownEditorFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $statePath . '\')') }},
            tab: '{{ $isDisabled ? 'preview' : 'edit' }}',
        })"
        wire:ignore
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class(['filament-forms-markdown-editor-component'])
        }}
    >
        <div class="space-y-2">
            @unless ($isDisabled)
                <div class="flex justify-between space-x-4 rtl:space-x-reverse overflow-x-auto items-stretch overflow-y-hidden">
                    <markdown-toolbar
                        for="{{ $id }}"
                        x-bind:class="{ 'pointer-events-none opacity-70': tab === 'preview' }"
                        class="flex items-stretch space-x-4 outline-none rtl:space-x-reverse"
                    >
                        @if ($hasToolbarButton(['bold', 'italic', 'strike', 'link']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('bold'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.bold') }}"
                                    >
                                        <md-bold>
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="bold"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 384 512"
                                            >
                                                <path fill="currentColor" d="M321.1 242.4C340.1 220.1 352 191.6 352 160c0-70.59-57.42-128-128-128L32 32.01c-17.67 0-32 14.31-32 32s14.33 32 32 32h16v320H32c-17.67 0-32 14.31-32 32s14.33 32 32 32h224c70.58 0 128-57.41 128-128C384 305.3 358.6 264.8 321.1 242.4zM112 96.01H224c35.3 0 64 28.72 64 64s-28.7 64-64 64H112V96.01zM256 416H112v-128H256c35.3 0 64 28.71 64 63.1S291.3 416 256 416z"></path>
                                            </svg>
                                        </md-bold>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('italic'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.italic') }}"
                                    >
                                        <md-italic>
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="italic"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 384 512"
                                            >
                                                <path fill="currentColor" d="M384 64.01c0 17.69-14.31 32-32 32h-58.67l-133.3 320H224c17.69 0 32 14.31 32 32s-14.31 32-32 32H32c-17.69 0-32-14.31-32-32s14.31-32 32-32h58.67l133.3-320H160c-17.69 0-32-14.31-32-32s14.31-32 32-32h192C369.7 32.01 384 46.33 384 64.01z"></path>
                                            </svg>
                                        </md-italic>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('strike'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.strike') }}"
                                    >
                                        <md-strikethrough>
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="strikethrough"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512"
                                            >
                                                <path fill="currentColor" d="M332.2 319.9c17.22 12.17 22.33 26.51 18.61 48.21c-3.031 17.59-10.88 29.34-24.72 36.99c-35.44 19.75-108.5 11.96-186-19.68c-16.34-6.686-35.03 1.156-41.72 17.53s1.188 35.05 17.53 41.71c31.75 12.93 95.69 35.37 157.6 35.37c29.62 0 58.81-5.156 83.72-18.96c30.81-17.09 50.44-45.46 56.72-82.11c3.998-23.27 2.168-42.58-3.488-59.05H332.2zM488 239.9l-176.5-.0309c-15.85-5.613-31.83-10.34-46.7-14.62c-85.47-24.62-110.9-39.05-103.7-81.33c2.5-14.53 10.16-25.96 22.72-34.03c20.47-13.15 64.06-23.84 155.4 .3438c17.09 4.531 34.59-5.654 39.13-22.74c4.531-17.09-5.656-34.59-22.75-39.12c-91.31-24.18-160.7-21.62-206.3 7.654C121.8 73.72 103.6 101.1 98.09 133.1C89.26 184.5 107.9 217.3 137.2 239.9L24 239.9c-13.25 0-24 10.75-24 23.1c0 13.25 10.75 23.1 24 23.1h464c13.25 0 24-10.75 24-23.1C512 250.7 501.3 239.9 488 239.9z"></path>
                                            </svg>
                                        </md-strikethrough>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('link'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.link') }}"
                                    >
                                        <md-link class="w-full h-full">
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="link"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 640 512"
                                            >
                                                <path fill="currentColor" d="M598.6 41.41C570.1 13.8 534.8 0 498.6 0s-72.36 13.8-99.96 41.41l-43.36 43.36c15.11 8.012 29.47 17.58 41.91 30.02c3.146 3.146 5.898 6.518 8.742 9.838l37.96-37.96C458.5 72.05 477.1 64 498.6 64c20.67 0 40.1 8.047 54.71 22.66c14.61 14.61 22.66 34.04 22.66 54.71s-8.049 40.1-22.66 54.71l-133.3 133.3C405.5 343.1 386 352 365.4 352s-40.1-8.048-54.71-22.66C296 314.7 287.1 295.3 287.1 274.6s8.047-40.1 22.66-54.71L314.2 216.4C312.1 212.5 309.9 208.5 306.7 205.3C298.1 196.7 286.8 192 274.6 192c-11.93 0-23.1 4.664-31.61 12.97c-30.71 53.96-23.63 123.6 22.39 169.6C293 402.2 329.2 416 365.4 416c36.18 0 72.36-13.8 99.96-41.41L598.6 241.3c28.45-28.45 42.24-66.01 41.37-103.3C639.1 102.1 625.4 68.16 598.6 41.41zM234 387.4L196.1 425.3C181.5 439.1 162 448 141.4 448c-20.67 0-40.1-8.047-54.71-22.66c-14.61-14.61-22.66-34.04-22.66-54.71s8.049-40.1 22.66-54.71l133.3-133.3C234.5 168 253.1 160 274.6 160s40.1 8.048 54.71 22.66c14.62 14.61 22.66 34.04 22.66 54.71s-8.047 40.1-22.66 54.71L325.8 295.6c2.094 3.939 4.219 7.895 7.465 11.15C341.9 315.3 353.3 320 365.4 320c11.93 0 23.1-4.664 31.61-12.97c30.71-53.96 23.63-123.6-22.39-169.6C346.1 109.8 310.8 96 274.6 96C238.4 96 202.3 109.8 174.7 137.4L41.41 270.7c-27.6 27.6-41.41 63.78-41.41 99.96c-.0001 36.18 13.8 72.36 41.41 99.97C69.01 498.2 105.2 512 141.4 512c36.18 0 72.36-13.8 99.96-41.41l43.36-43.36c-15.11-8.012-29.47-17.58-41.91-30.02C239.6 394.1 236.9 390.7 234 387.4z"></path>
                                            </svg>
                                        </md-link>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['codeBlock', 'bulletList', 'orderedList']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('codeBlock'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.code_block') }}"
                                    >
                                        <md-code class="w-full h-full">
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="code"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 640 512"
                                            >
                                                <path fill="currentColor" d="M416 31.94C416 21.75 408.1 0 384.1 0c-13.98 0-26.87 9.072-30.89 23.18l-128 448c-.8404 2.935-1.241 5.892-1.241 8.801C223.1 490.3 232 512 256 512c13.92 0 26.73-9.157 30.75-23.22l128-448C415.6 37.81 416 34.85 416 31.94zM176 143.1c0-18.28-14.95-32-32-32c-8.188 0-16.38 3.125-22.62 9.376l-112 112C3.125 239.6 0 247.8 0 255.1S3.125 272.4 9.375 278.6l112 112C127.6 396.9 135.8 399.1 144 399.1c17.05 0 32-13.73 32-32c0-8.188-3.125-16.38-9.375-22.63L77.25 255.1l89.38-89.38C172.9 160.3 176 152.2 176 143.1zM640 255.1c0-8.188-3.125-16.38-9.375-22.63l-112-112C512.4 115.1 504.2 111.1 496 111.1c-17.05 0-32 13.73-32 32c0 8.188 3.125 16.38 9.375 22.63l89.38 89.38l-89.38 89.38C467.1 351.6 464 359.8 464 367.1c0 18.28 14.95 32 32 32c8.188 0 16.38-3.125 22.62-9.376l112-112C636.9 272.4 640 264.2 640 255.1z"></path>
                                            </svg>
                                        </md-code>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('bulletList'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.bullet_list') }}"
                                    >
                                        <md-unordered-list class="w-full h-full">
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="list-ul"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512"
                                            >
                                                <path fill="currentColor" d="M16 96C16 69.49 37.49 48 64 48C90.51 48 112 69.49 112 96C112 122.5 90.51 144 64 144C37.49 144 16 122.5 16 96zM480 64C497.7 64 512 78.33 512 96C512 113.7 497.7 128 480 128H192C174.3 128 160 113.7 160 96C160 78.33 174.3 64 192 64H480zM480 224C497.7 224 512 238.3 512 256C512 273.7 497.7 288 480 288H192C174.3 288 160 273.7 160 256C160 238.3 174.3 224 192 224H480zM480 384C497.7 384 512 398.3 512 416C512 433.7 497.7 448 480 448H192C174.3 448 160 433.7 160 416C160 398.3 174.3 384 192 384H480zM16 416C16 389.5 37.49 368 64 368C90.51 368 112 389.5 112 416C112 442.5 90.51 464 64 464C37.49 464 16 442.5 16 416zM112 256C112 282.5 90.51 304 64 304C37.49 304 16 282.5 16 256C16 229.5 37.49 208 64 208C90.51 208 112 229.5 112 256z"></path>
                                            </svg>
                                        </md-unordered-list>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('orderedList'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.ordered_list') }}"
                                    >
                                        <md-ordered-list class="w-full h-full">
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="list-ol"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 576 512"
                                            >
                                                <path fill="currentColor" d="M55.1 56.04C55.1 42.78 66.74 32.04 79.1 32.04H111.1C125.3 32.04 135.1 42.78 135.1 56.04V176H151.1C165.3 176 175.1 186.8 175.1 200C175.1 213.3 165.3 224 151.1 224H71.1C58.74 224 47.1 213.3 47.1 200C47.1 186.8 58.74 176 71.1 176H87.1V80.04H79.1C66.74 80.04 55.1 69.29 55.1 56.04V56.04zM118.7 341.2C112.1 333.8 100.4 334.3 94.65 342.4L83.53 357.9C75.83 368.7 60.84 371.2 50.05 363.5C39.26 355.8 36.77 340.8 44.47 330.1L55.59 314.5C79.33 281.2 127.9 278.8 154.8 309.6C176.1 333.1 175.6 370.5 153.7 394.3L118.8 432H152C165.3 432 176 442.7 176 456C176 469.3 165.3 480 152 480H64C54.47 480 45.84 474.4 42.02 465.6C38.19 456.9 39.9 446.7 46.36 439.7L118.4 361.7C123.7 355.9 123.8 347.1 118.7 341.2L118.7 341.2zM512 64C529.7 64 544 78.33 544 96C544 113.7 529.7 128 512 128H256C238.3 128 224 113.7 224 96C224 78.33 238.3 64 256 64H512zM512 224C529.7 224 544 238.3 544 256C544 273.7 529.7 288 512 288H256C238.3 288 224 273.7 224 256C224 238.3 238.3 224 256 224H512zM512 384C529.7 384 544 398.3 544 416C544 433.7 529.7 448 512 448H256C238.3 448 224 433.7 224 416C224 398.3 238.3 384 256 384H512z"></path>
                                            </svg>
                                        </md-ordered-list>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['attachFiles']))
                            <div class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('attachFiles'))
                                    <x-filament-forms::markdown-editor.toolbar-button
                                        title="{{ __('filament-forms::components.markdown_editor.toolbar_buttons.attach_files') }}"
                                    >
                                        <md-image class="w-full h-full" x-ref="imageTrigger">
                                            <svg
                                                class="h-4 dark:fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                data-prefix="fas"
                                                data-icon="image"
                                                role="img"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512"
                                            >
                                                <path fill="currentColor" d="M447.1 32h-484C28.64 32-.0091 60.65-.0091 96v320c0 35.35 28.65 64 63.1 64h384c35.35 0 64-28.65 64-64V96C511.1 60.65 483.3 32 447.1 32zM111.1 96c26.51 0 48 21.49 48 48S138.5 192 111.1 192s-48-21.49-48-48S85.48 96 111.1 96zM446.1 407.6C443.3 412.8 437.9 416 432 416H82.01c-6.021 0-11.53-3.379-14.26-8.75c-2.73-5.367-2.215-11.81 1.334-16.68l70-96C142.1 290.4 146.9 288 152 288s9.916 2.441 12.93 6.574l32.46 44.51l93.3-139.1C293.7 194.7 298.7 192 304 192s10.35 2.672 13.31 7.125l128 192C448.6 396 448.9 402.3 446.1 407.6z"></path>
                                            </svg>
                                        </md-image>
                                    </x-filament-forms::markdown-editor.toolbar-button>
                                @endif
                            </div>
                        @endif
                    </markdown-toolbar>

                    @if ($hasToolbarButton(['edit', 'preview']) && (! $isDisabled))
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            @if ($hasToolbarButton('edit'))
                                <button
                                    type="button"
                                    x-on:click.prevent="tab = 'edit'"
                                    x-bind:class="{ 'text-gray-400 dark:text-gray-400': tab !== 'edit' }"
                                    class="text-sm hover:underline"
                                >
                                    {{ __('filament-forms::components.markdown_editor.toolbar_buttons.edit') }}
                                </button>
                            @endif

                            @if ($hasToolbarButton('preview'))
                                <button
                                    type="button"
                                    x-on:click.prevent="tab = 'preview'"
                                    class="text-sm hover:underline text-gray-400 dark:text-gray-400"
                                    x-bind:class="{ 'text-gray-400 dark:text-gray-400': tab !== 'preview' }"
                                >
                                    {{ __('filament-forms::components.markdown_editor.toolbar_buttons.preview') }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endunless

            <div x-show="tab === 'edit'" class="relative w-full h-full" style="min-height: 150px;">
                <file-attachment directory>
                    <textarea
                        @if ($isAutofocused()) autofocus @endif
                        id="{{ $id }}"
                        @if ($placeholder = $getPlaceholder()) placeholder="{{ $placeholder }}" @endif
                        x-model="state"
                        dusk="filament.forms.{{ $statePath }}"
                        x-on:keyup.enter="checkForAutoInsertion"
                        x-on:file-attachment-accepted.window="
                            if ($event?.srcElement.querySelector('textarea')?.id === '{{ $getId() }}') {
                                attachment = $event.detail?.attachments?.[0]

                                if (! attachment || ! attachment.file) return

                                $wire.upload(`componentFileAttachments.{{ $statePath }}`, attachment.file, () => {
                                    $wire.getComponentFileAttachmentUrl('{{ $statePath }}').then((url) => {
                                        if (! url) {
                                            return
                                        }

                                        $refs.imageTrigger.click()

                                        const urlStart = $refs.textarea.selectionStart + 2
                                        const urlEnd = urlStart + 3

                                        state = [
                                            $refs.textarea.value.substring(0, urlStart),
                                            url,
                                            $refs.textarea.value.substring(urlEnd)
                                        ].join('')

                                        $refs.textarea.selectionStart = urlStart - 2
                                        $refs.textarea.selectionEnd = urlStart - 2

                                        render()
                                    })
                                })
                            }
                        "
                        x-ref="textarea"
                        @if ($isRequired() && (! $isConcealed())) required @endif
                        class="z-1 absolute top-0 left-0 block h-full min-h-full w-full resize-none overflow-y-hidden whitespace-pre-wrap rounded-lg bg-transparent font-mono text-sm tracking-normal caret-black shadow-sm transition duration-75 outline-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 rtl:whitespace-normal dark:caret-white dark:focus:border-primary-500"
                        x-bind:class="{
                            'border-gray-300 dark:border-gray-600': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                            'border-danger-600 ring-1 ring-inset ring-danger-600 dark:border-danger-400 dark:ring-danger-400': @js($statePath) in $wire.__instance.serverMemo.errors,
                        }"
                    ></textarea>
                </file-attachment>

                <div
                    x-ref="overlay"
                    x-html="overlay"
                    style="min-height: 150px;"
                    class="w-full h-full rounded-lg px-3 py-2 border border-transparent font-mono tracking-normal bg-white text-sm text-gray-900 break-words whitespace-pre-wrap rtl:whitespace-normal dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                ></div>
            </div>

            <div
                class="prose max-w-none block w-full h-full min-h-[150px] px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm break-words focus:border-primary-300 dark:prose-invert dark:border-gray-600 dark:bg-gray-700"
                x-show="tab === 'preview'"
                x-html="preview"
                x-cloak
            ></div>
        </div>
    </div>
</x-dynamic-component>
