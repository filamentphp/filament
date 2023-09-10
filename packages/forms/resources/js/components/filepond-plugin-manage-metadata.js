const registerManageMetadataComponent = (item, el, labelButtonEditMetadata, onManageMetadata) => {
    const info = el.querySelector('.filepond--file-info-main'),
        editIcon = getEditIcon(labelButtonEditMetadata);

    info.prepend(editIcon);
    editIcon.addEventListener("click", () => onManageMetadata(item));
}

/**
 * Generates the download icon
 */
const getEditIcon = (labelButtonEditMetadata) => {
    let icon = document.createElement('span');
    icon.className = 'filepond--edit-icon';
    icon.title = labelButtonEditMetadata;
    return icon;
}

/**
 * Manage Metadata Plugin
 */
const plugin = fpAPI => {

    const { addFilter, utils } = fpAPI;
    const { Type, createRoute } = utils;

    // called for each view that is created right after the 'create' method
    addFilter('CREATE_VIEW', viewAPI => {

        // get reference to created view
        const { is, view, query } = viewAPI;

        // only hook up to item view
        if (!is('file')) {
            return;
        }

        // create the manage metadata plugin
        const didLoadItem = ({ root, props }) => {
            const { id } = props;
            const item = query('GET_ITEM', id);

            if (!item || item.archived) {
                return;
            }

            const enableManageMetadata = root.query('GET_ENABLE_MANAGE_METADATA');
            if (!enableManageMetadata) {
                return; // not enabled, so hide this plugin
            }

            const labelButtonManageMetadata = root.query('GET_LABEL_BUTTON_MANAGE_METADATA');
            const onManageMetadata = root.query('GET_ON_MANAGE_METADATA');

            if (onManageMetadata) {
                registerManageMetadataComponent(item, root.element, labelButtonManageMetadata, onManageMetadata);
            } else {
                console.error("No 'onManageMetadata' callback method specified for FilePondPluginManageMetadata! See https://github.com/nielsboogaard/filepond-plugin-manage-metadata for more instructions.");
            }
        };

        // start writing
        view.registerWriter(
            createRoute({
                DID_INIT_ITEM: didLoadItem
            }, ({ root, props }) => {
                const { id } = props;
                const item = query('GET_ITEM', id);

                // don't do anything while hidden
                if (root.rect.element.hidden) return;
            })
        );
    });

    // expose plugin
    return {
        options: {
            enableManageMetadata: [true, Type.BOOLEAN],
            labelButtonManageMetadata: ['Edit metadata', Type.STRING],
            onManageMetadata: [null, Type.FUNCTION],
        }
    };
};

// fire pluginloaded event if running in browser, this allows registering the plugin when using async script tags
const isBrowser = typeof window !== 'undefined' && typeof window.document !== 'undefined';
if (isBrowser) {
    document.dispatchEvent(new CustomEvent('FilePond:pluginloaded', { detail: plugin }));
}

export default plugin;