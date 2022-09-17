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
    <div
        x-data="richEditorFormComponent({
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
        })"
        x-on:trix-change="state = $event.target.value"
        x-on:trix-attachment-add="
            if (! $event.attachment.file) return

            let attachment = $event.attachment

            $wire.upload(`componentFileAttachments.{{ $getStatePath() }}`, attachment.file, () => {
                $wire.getComponentFileAttachmentUrl('{{ $getStatePath() }}').then((url) => {
                    attachment.setAttributes({
                        url: url,
                        href: url,
                    })
                })
            })
        "
        x-on:trix-file-accept="
            if ({{ $hasToolbarButton('attachFiles') ? 'true' : 'false' }}) return

            $event.preventDefault()
        "
        {{ $attributes->merge($getExtraAttributes())->class(['filament-forms-rich-editor-component space-y-2']) }}
        {{ $getExtraAlpineAttributeBag() }}
    >
        @unless ($isDisabled())
            <input id="trix-value-{{ $getId() }}" type="hidden" />

            <trix-toolbar
                id="trix-toolbar-{{ $getId() }}"
                @class([
                    'hidden' => ! count($getToolbarButtons()),
                ])
            >
                <div class="flex justify-between space-x-4 rtl:space-x-reverse overflow-x-auto items-stretch overflow-y-hidden">
                    <div class="flex items-stretch space-x-4 rtl:space-x-reverse focus:outline-none">
                        @if ($hasToolbarButton(['bold', 'italic', 'strike', 'link']))
                            <div data-trix-button-group="text-tools" class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('bold'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="bold"
                                        data-trix-key="b"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.bold') }}"
                                        tabindex="-1"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.bold') }}"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bold" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M321.1 242.4C340.1 220.1 352 191.6 352 160c0-70.59-57.42-128-128-128L32 32.01c-17.67 0-32 14.31-32 32s14.33 32 32 32h16v320H32c-17.67 0-32 14.31-32 32s14.33 32 32 32h224c70.58 0 128-57.41 128-128C384 305.3 358.6 264.8 321.1 242.4zM112 96.01H224c35.3 0 64 28.72 64 64s-28.7 64-64 64H112V96.01zM256 416H112v-128H256c35.3 0 64 28.71 64 63.1S291.3 416 256 416z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('italic'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="italic"
                                        data-trix-key="i"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.italic') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="italic" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M384 64.01c0 17.69-14.31 32-32 32h-58.67l-133.3 320H224c17.69 0 32 14.31 32 32s-14.31 32-32 32H32c-17.69 0-32-14.31-32-32s14.31-32 32-32h58.67l133.3-320H160c-17.69 0-32-14.31-32-32s14.31-32 32-32h192C369.7 32.01 384 46.33 384 64.01z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('strike'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="strike"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.strike') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="strikethrough" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M332.2 319.9c17.22 12.17 22.33 26.51 18.61 48.21c-3.031 17.59-10.88 29.34-24.72 36.99c-35.44 19.75-108.5 11.96-186-19.68c-16.34-6.686-35.03 1.156-41.72 17.53s1.188 35.05 17.53 41.71c31.75 12.93 95.69 35.37 157.6 35.37c29.62 0 58.81-5.156 83.72-18.96c30.81-17.09 50.44-45.46 56.72-82.11c3.998-23.27 2.168-42.58-3.488-59.05H332.2zM488 239.9l-176.5-.0309c-15.85-5.613-31.83-10.34-46.7-14.62c-85.47-24.62-110.9-39.05-103.7-81.33c2.5-14.53 10.16-25.96 22.72-34.03c20.47-13.15 64.06-23.84 155.4 .3438c17.09 4.531 34.59-5.654 39.13-22.74c4.531-17.09-5.656-34.59-22.75-39.12c-91.31-24.18-160.7-21.62-206.3 7.654C121.8 73.72 103.6 101.1 98.09 133.1C89.26 184.5 107.9 217.3 137.2 239.9L24 239.9c-13.25 0-24 10.75-24 23.1c0 13.25 10.75 23.1 24 23.1h464c13.25 0 24-10.75 24-23.1C512 250.7 501.3 239.9 488 239.9z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('link'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="href"
                                        data-trix-action="link"
                                        data-trix-key="k"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.link') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="link" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M598.6 41.41C570.1 13.8 534.8 0 498.6 0s-72.36 13.8-99.96 41.41l-43.36 43.36c15.11 8.012 29.47 17.58 41.91 30.02c3.146 3.146 5.898 6.518 8.742 9.838l37.96-37.96C458.5 72.05 477.1 64 498.6 64c20.67 0 40.1 8.047 54.71 22.66c14.61 14.61 22.66 34.04 22.66 54.71s-8.049 40.1-22.66 54.71l-133.3 133.3C405.5 343.1 386 352 365.4 352s-40.1-8.048-54.71-22.66C296 314.7 287.1 295.3 287.1 274.6s8.047-40.1 22.66-54.71L314.2 216.4C312.1 212.5 309.9 208.5 306.7 205.3C298.1 196.7 286.8 192 274.6 192c-11.93 0-23.1 4.664-31.61 12.97c-30.71 53.96-23.63 123.6 22.39 169.6C293 402.2 329.2 416 365.4 416c36.18 0 72.36-13.8 99.96-41.41L598.6 241.3c28.45-28.45 42.24-66.01 41.37-103.3C639.1 102.1 625.4 68.16 598.6 41.41zM234 387.4L196.1 425.3C181.5 439.1 162 448 141.4 448c-20.67 0-40.1-8.047-54.71-22.66c-14.61-14.61-22.66-34.04-22.66-54.71s8.049-40.1 22.66-54.71l133.3-133.3C234.5 168 253.1 160 274.6 160s40.1 8.048 54.71 22.66c14.62 14.61 22.66 34.04 22.66 54.71s-8.047 40.1-22.66 54.71L325.8 295.6c2.094 3.939 4.219 7.895 7.465 11.15C341.9 315.3 353.3 320 365.4 320c11.93 0 23.1-4.664 31.61-12.97c30.71-53.96 23.63-123.6-22.39-169.6C346.1 109.8 310.8 96 274.6 96C238.4 96 202.3 109.8 174.7 137.4L41.41 270.7c-27.6 27.6-41.41 63.78-41.41 99.96c-.0001 36.18 13.8 72.36 41.41 99.97C69.01 498.2 105.2 512 141.4 512c36.18 0 72.36-13.8 99.96-41.41l43.36-43.36c-15.11-8.012-29.47-17.58-41.91-30.02C239.6 394.1 236.9 390.7 234 387.4z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['h1', 'h2', 'h3']))
                            <div data-trix-button-group="heading-tools" class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('h1'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="heading1"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.h1') }}"
                                        tabindex="-1"
                                    >
                                        {{ __('forms::components.rich_editor.toolbar_buttons.h1') }}
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('h2'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="heading"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.h2') }}"
                                        tabindex="-1"
                                    >
                                        {{ __('forms::components.rich_editor.toolbar_buttons.h2') }}
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('h3'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="subHeading"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.h3') }}"
                                        tabindex="-1"
                                    >
                                        {{ __('forms::components.rich_editor.toolbar_buttons.h3') }}
                                    </x-forms::rich-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton(['blockquote', 'codeBlock', 'bulletList', 'orderedList']))
                            <div data-trix-button-group="block-tools" class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                @if ($hasToolbarButton('blockquote'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="quote"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.blockquote') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="quote-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M96 224C84.72 224 74.05 226.3 64 229.9V224c0-35.3 28.7-64 64-64c17.67 0 32-14.33 32-32S145.7 96 128 96C57.42 96 0 153.4 0 224v96c0 53.02 42.98 96 96 96s96-42.98 96-96S149 224 96 224zM352 224c-11.28 0-21.95 2.305-32 5.879V224c0-35.3 28.7-64 64-64c17.67 0 32-14.33 32-32s-14.33-32-32-32c-70.58 0-128 57.42-128 128v96c0 53.02 42.98 96 96 96s96-42.98 96-96S405 224 352 224z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('codeBlock'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="code"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.code_block') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="code" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M416 31.94C416 21.75 408.1 0 384.1 0c-13.98 0-26.87 9.072-30.89 23.18l-128 448c-.8404 2.935-1.241 5.892-1.241 8.801C223.1 490.3 232 512 256 512c13.92 0 26.73-9.157 30.75-23.22l128-448C415.6 37.81 416 34.85 416 31.94zM176 143.1c0-18.28-14.95-32-32-32c-8.188 0-16.38 3.125-22.62 9.376l-112 112C3.125 239.6 0 247.8 0 255.1S3.125 272.4 9.375 278.6l112 112C127.6 396.9 135.8 399.1 144 399.1c17.05 0 32-13.73 32-32c0-8.188-3.125-16.38-9.375-22.63L77.25 255.1l89.38-89.38C172.9 160.3 176 152.2 176 143.1zM640 255.1c0-8.188-3.125-16.38-9.375-22.63l-112-112C512.4 115.1 504.2 111.1 496 111.1c-17.05 0-32 13.73-32 32c0 8.188 3.125 16.38 9.375 22.63l89.38 89.38l-89.38 89.38C467.1 351.6 464 359.8 464 367.1c0 18.28 14.95 32 32 32c8.188 0 16.38-3.125 22.62-9.376l112-112C636.9 272.4 640 264.2 640 255.1z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('bulletList'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="bullet"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.bullet_list') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ul" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M16 96C16 69.49 37.49 48 64 48C90.51 48 112 69.49 112 96C112 122.5 90.51 144 64 144C37.49 144 16 122.5 16 96zM480 64C497.7 64 512 78.33 512 96C512 113.7 497.7 128 480 128H192C174.3 128 160 113.7 160 96C160 78.33 174.3 64 192 64H480zM480 224C497.7 224 512 238.3 512 256C512 273.7 497.7 288 480 288H192C174.3 288 160 273.7 160 256C160 238.3 174.3 224 192 224H480zM480 384C497.7 384 512 398.3 512 416C512 433.7 497.7 448 480 448H192C174.3 448 160 433.7 160 416C160 398.3 174.3 384 192 384H480zM16 416C16 389.5 37.49 368 64 368C90.51 368 112 389.5 112 416C112 442.5 90.51 464 64 464C37.49 464 16 442.5 16 416zM112 256C112 282.5 90.51 304 64 304C37.49 304 16 282.5 16 256C16 229.5 37.49 208 64 208C90.51 208 112 229.5 112 256z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif

                                @if ($hasToolbarButton('orderedList'))
                                    <x-forms::rich-editor.toolbar-button
                                        data-trix-attribute="number"
                                        title="{{ __('forms::components.rich_editor.toolbar_buttons.ordered_list') }}"
                                        tabindex="-1"
                                    >
                                        <svg @class([
                                            'h-4',
                                            'dark:fill-current' => config('forms.dark_mode'),
                                        ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="list-ol" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M55.1 56.04C55.1 42.78 66.74 32.04 79.1 32.04H111.1C125.3 32.04 135.1 42.78 135.1 56.04V176H151.1C165.3 176 175.1 186.8 175.1 200C175.1 213.3 165.3 224 151.1 224H71.1C58.74 224 47.1 213.3 47.1 200C47.1 186.8 58.74 176 71.1 176H87.1V80.04H79.1C66.74 80.04 55.1 69.29 55.1 56.04V56.04zM118.7 341.2C112.1 333.8 100.4 334.3 94.65 342.4L83.53 357.9C75.83 368.7 60.84 371.2 50.05 363.5C39.26 355.8 36.77 340.8 44.47 330.1L55.59 314.5C79.33 281.2 127.9 278.8 154.8 309.6C176.1 333.1 175.6 370.5 153.7 394.3L118.8 432H152C165.3 432 176 442.7 176 456C176 469.3 165.3 480 152 480H64C54.47 480 45.84 474.4 42.02 465.6C38.19 456.9 39.9 446.7 46.36 439.7L118.4 361.7C123.7 355.9 123.8 347.1 118.7 341.2L118.7 341.2zM512 64C529.7 64 544 78.33 544 96C544 113.7 529.7 128 512 128H256C238.3 128 224 113.7 224 96C224 78.33 238.3 64 256 64H512zM512 224C529.7 224 544 238.3 544 256C544 273.7 529.7 288 512 288H256C238.3 288 224 273.7 224 256C224 238.3 238.3 224 256 224H512zM512 384C529.7 384 544 398.3 544 416C544 433.7 529.7 448 512 448H256C238.3 448 224 433.7 224 416C224 398.3 238.3 384 256 384H512z"></path></svg>
                                    </x-forms::rich-editor.toolbar-button>
                                @endif
                            </div>
                        @endif

                        @if ($hasToolbarButton('attachFiles'))
                            <div data-trix-button-group="file-tools" class="flex items-stretch space-x-1 rtl:space-x-reverse">
                                <x-forms::rich-editor.toolbar-button
                                    data-trix-action="attachFiles"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.attach_files') }}"
                                    tabindex="-1"
                                >
                                    <svg @class([
                                        'h-4',
                                        'dark:fill-current' => config('forms.dark_mode'),
                                    ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M447.1 32h-484C28.64 32-.0091 60.65-.0091 96v320c0 35.35 28.65 64 63.1 64h384c35.35 0 64-28.65 64-64V96C511.1 60.65 483.3 32 447.1 32zM111.1 96c26.51 0 48 21.49 48 48S138.5 192 111.1 192s-48-21.49-48-48S85.48 96 111.1 96zM446.1 407.6C443.3 412.8 437.9 416 432 416H82.01c-6.021 0-11.53-3.379-14.26-8.75c-2.73-5.367-2.215-11.81 1.334-16.68l70-96C142.1 290.4 146.9 288 152 288s9.916 2.441 12.93 6.574l32.46 44.51l93.3-139.1C293.7 194.7 298.7 192 304 192s10.35 2.672 13.31 7.125l128 192C448.6 396 448.9 402.3 446.1 407.6z"></path></svg>
                                </x-forms::rich-editor.toolbar-button>
                            </div>
                        @endif
                    </div>

                    @if ($hasToolbarButton(['undo', 'redo']))
                        <div
                            data-trix-button-group="history-tools"
                            class="flex items-center space-x-1 rtl:space-x-reverse"
                        >
                            @if ($hasToolbarButton('undo'))
                                <x-forms::rich-editor.toolbar-button
                                    data-trix-action="undo"
                                    data-trix-key="z"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.undo') }}"
                                    tabindex="-1"
                                >
                                    <svg @class([
                                        'h-4',
                                        'dark:fill-current' => config('forms.dark_mode'),
                                    ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="rotate-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M480 256c0 123.4-100.5 223.9-223.9 223.9c-48.84 0-95.17-15.58-134.2-44.86c-14.12-10.59-16.97-30.66-6.375-44.81c10.59-14.12 30.62-16.94 44.81-6.375c27.84 20.91 61 31.94 95.88 31.94C344.3 415.8 416 344.1 416 256s-71.69-159.8-159.8-159.8c-37.46 0-73.09 13.49-101.3 36.64l45.12 45.14c17.01 17.02 4.955 46.1-19.1 46.1H35.17C24.58 224.1 16 215.5 16 204.9V59.04c0-24.04 29.07-36.08 46.07-19.07l47.6 47.63C149.9 52.71 201.5 32.11 256.1 32.11C379.5 32.11 480 132.6 480 256z"></path></svg>
                                </x-forms::rich-editor.toolbar-button>
                            @endif

                            @if ($hasToolbarButton('redo'))
                                <x-forms::rich-editor.toolbar-button
                                    data-trix-action="redo"
                                    data-trix-key="shift+z"
                                    title="{{ __('forms::components.rich_editor.toolbar_buttons.redo') }}"
                                    tabindex="-1"
                                >
                                    <svg @class([
                                        'h-4',
                                        'dark:fill-current' => config('forms.dark_mode'),
                                    ]) aria-hidden="true" focusable="false" data-prefix="fas" data-icon="rotate-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M468.9 32.11c13.87 0 27.18 10.77 27.18 27.04v145.9c0 10.59-8.584 19.17-19.17 19.17h-145.7c-16.28 0-27.06-13.32-27.06-27.2c0-6.634 2.461-13.4 7.96-18.9l45.12-45.14c-28.22-23.14-63.85-36.64-101.3-36.64c-88.09 0-159.8 71.69-159.8 159.8S167.8 415.9 255.9 415.9c73.14 0 89.44-38.31 115.1-38.31c18.48 0 31.97 15.04 31.97 31.96c0 35.04-81.59 70.41-147 70.41c-123.4 0-223.9-100.5-223.9-223.9S132.6 32.44 256 32.44c54.6 0 106.2 20.39 146.4 55.26l47.6-47.63C455.5 34.57 462.3 32.11 468.9 32.11z"></path></svg>
                                </x-forms::rich-editor.toolbar-button>
                            @endif
                        </div>
                    @endif
                </div>

                <div data-trix-dialogs class="trix-dialogs" x-cloak>
                    <div
                        data-trix-dialog="href"
                        data-trix-dialog-attribute="href"
                        class="trix-dialog trix-dialog--link"
                    >
                        <div class="trix-dialog__link-fields">
                            <input
                                name="href"
                                placeholder="{{ __('forms::components.rich_editor.dialogs.link.placeholder') }}" aria-label="{{ __('forms::components.rich_editor.dialogs.link.label') }}" required data-trix-input
                                disabled
                                type="url"
                                class="trix-input trix-input--dialog"
                            >

                            <div class="trix-button-group">
                                <input
                                    value="{{ __('forms::components.rich_editor.dialogs.link.buttons.link') }}"
                                    data-trix-method="setAttribute"
                                    type="button"
                                    class="trix-button trix-button--dialog"
                                >

                                <input
                                    value="{{ __('forms::components.rich_editor.dialogs.link.buttons.unlink') }}"
                                    data-trix-method="removeAttribute"
                                    type="button"
                                    class="trix-button trix-button--dialog"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </trix-toolbar>

            <trix-editor
                wire:ignore
                {!! $isAutofocused() ? 'autofocus' : null !!}
                id="{{ $getId() }}"
                input="trix-value-{{ $getId() }}"
                placeholder="{{ $getPlaceholder() }}"
                toolbar="trix-toolbar-{{ $getId() }}"
                x-ref="trix"
                dusk="filament.forms.{{ $getStatePath() }}"
                {{ $getExtraInputAttributeBag()->class([
                    'bg-white block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 prose max-w-none break-words',
                    'dark:prose-invert dark:bg-gray-700 dark:focus:border-primary-500' => config('forms.dark_mode'),
                ]) }}
                x-bind:class="{
                    'border-gray-300': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                    'dark:border-gray-600': ! (@js($getStatePath()) in $wire.__instance.serverMemo.errors) && @js(config('forms.dark_mode')),
                    'border-danger-600 ring-danger-600': (@js($getStatePath()) in $wire.__instance.serverMemo.errors),
                }"
            ></trix-editor>
        @else
            <div x-html="state" @class([
                'prose block w-full max-w-none rounded-lg border border-gray-300 bg-white p-3 opacity-70 shadow-sm',
                'dark:prose-invert dark:border-gray-600 dark:bg-gray-700' => config('forms.dark_mode'),
            ])></div>
        @endunless
    </div>
</x-dynamic-component>
