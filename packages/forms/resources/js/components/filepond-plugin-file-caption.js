/* filepond-plugin-file-caption.js */
import { FileStatus } from 'filepond';

/**
 * FilePond file caption plugin
 *
 * FilePondPluginImageCaption 1.0.3
 * Author: clementmas
 * Licensed under MIT, https://opensource.org/licenses/MIT
 */
export default function ({ addFilter, utils }) {
    const { Type, createRoute, createView } = utils;

    // Called when a new file is added
    addFilter('CREATE_VIEW', function (viewAPI) {
        const { is, view, query } = viewAPI;

        // Make sure the option `addImageCaption` is enabled
        if (!query('GET_ADD_FILE_CAPTION')) return;

        // Skip invalid file types
        if (!is('file')) return;

        function onItemAdded({ root, props: { id } }) {
            const item = query('GET_ITEM', id);

            // Item could theoretically have been removed in the mean time
            if (!item || item.archived) return;

            const value = item.getMetadata('caption');

            const isInvalid = item.status === FileStatus.LOAD_ERROR;

            // Append file caption input
            root.ref.filePreview = view.appendChildView(
                view.createChildView(
                    createView(addCaptionInputField(item, value, isInvalid)),
                    { id },
                ),
            );

            // Disable file action buttons tabindex (cancel, revert, etc.)
            // to easily tab from one caption input to another
            view.element
                .querySelectorAll('button')
                .forEach((button) => button.setAttribute('tabindex', -1));
        }

        view.registerWriter(
            createRoute({
                DID_INIT_ITEM: onItemAdded,
            }),
        );
    });

    // Plugin config options
    return {
        options: {
            // Enable or disable file captions
            addFileCaption: [false, Type.BOOLEAN],

            // Input placeholder
            fileCaptionPlaceholder: [null, Type.STRING],

            // Input max length
            fileCaptionMaxLength: [null, Type.INT],

            fileCaption: [null, Type.STRING],

            fileCaptionUsing: [null, Type.FUNCTION],
        },
    };
}

// Create DOM input
function addCaptionInputField(file, value, isInvalid) {
    return {
        name: 'file-caption-input',
        tag: 'input',
        ignoreRect: true,
        create: function create({ root }) {
            // Input name
            root.element.setAttribute('name', 'captions[]');

            // Value
            if (value) {
                root.element.value = value;
            }

            // Placeholder
            const placeholder = root.query('GET_FILE_CAPTION_PLACEHOLDER');
            if (placeholder) {
                root.element.setAttribute('placeholder', placeholder);
            }

            // Max length
            const maxLength = root.query('GET_FILE_CAPTION_MAX_LENGTH');
            if (maxLength) {
                root.element.setAttribute('maxlength', maxLength);
            }

            // Autocomplete off
            root.element.setAttribute('autocomplete', 'off');

            // Visually hide the element if the file is invalid but keep the input
            // to make sure the "captions[]" index will stay in sync with the FilePond photos
            if (isInvalid) {
                root.element.classList.add('file-caption-input-invalid');
            }

            // Prevent Enter key from submitting form
            root.element.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    const fileCaptionUsing = root.query('GET_FILE_CAPTION_USING');
                    if (fileCaptionUsing) {
                        file.setMetadata('caption', e.currentTarget.value, true);
                        fileCaptionUsing(file, e.currentTarget.value)
                    }
                    e.preventDefault();
                }
            });
        },
    };
}