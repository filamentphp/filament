<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clone',
            ],

            'add' => [

                'label' => 'Add to :label',

                'modal' => [

                    'heading' => 'Add to :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Add',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Insert between blocks',

                'modal' => [

                    'heading' => 'Add to :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Add',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Delete',
            ],

            'edit' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Edit block',

                    'actions' => [

                        'save' => [
                            'label' => 'Save changes',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Move',
            ],

            'move_down' => [
                'label' => 'Move down',
            ],

            'move_up' => [
                'label' => 'Move up',
            ],

            'collapse' => [
                'label' => 'Collapse',
            ],

            'expand' => [
                'label' => 'Expand',
            ],

            'collapse_all' => [
                'label' => 'Collapse all',
            ],

            'expand_all' => [
                'label' => 'Expand all',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

            'select_all' => [
                'label' => 'Select all',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Cancel',
                ],

                'drag_crop' => [
                    'label' => 'Drag mode "crop"',
                ],

                'drag_move' => [
                    'label' => 'Drag mode "move"',
                ],

                'flip_horizontal' => [
                    'label' => 'Flip image horizontally',
                ],

                'flip_vertical' => [
                    'label' => 'Flip image vertically',
                ],

                'move_down' => [
                    'label' => 'Move image down',
                ],

                'move_left' => [
                    'label' => 'Move image to left',
                ],

                'move_right' => [
                    'label' => 'Move image to right',
                ],

                'move_up' => [
                    'label' => 'Move image up',
                ],

                'reset' => [
                    'label' => 'Reset',
                ],

                'rotate_left' => [
                    'label' => 'Rotate image to left',
                ],

                'rotate_right' => [
                    'label' => 'Rotate image to right',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Set aspect ratio to :ratio',
                ],

                'save' => [
                    'label' => 'Save',
                ],

                'zoom_100' => [
                    'label' => 'Zoom image to 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoom in',
                ],

                'zoom_out' => [
                    'label' => 'Zoom out',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Height',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotation',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Width',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'Aspect ratios',

                'no_fixed' => [
                    'label' => 'Free',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Editing SVG files is not recommended as it can result in quality loss when scaling.\n Are you sure you want to continue?',
                    'disabled' => 'Editing SVG files is disabled as it can result in quality loss when scaling.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Add row',
            ],

            'delete' => [
                'label' => 'Delete row',
            ],

            'reorder' => [
                'label' => 'Reorder row',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Key',
            ],

            'value' => [
                'label' => 'Value',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Attach files',
            'blockquote' => 'Blockquote',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'code_block' => 'Code block',
            'heading' => 'Heading',
            'italic' => 'Italic',
            'link' => 'Link',
            'ordered_list' => 'Numbered list',
            'redo' => 'Redo',
            'strike' => 'Strikethrough',
            'table' => 'Table',
            'undo' => 'Undo',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Select',

                'actions' => [

                    'select' => [
                        'label' => 'Select',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Yes',
            'false' => 'No',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Add to :label',
            ],

            'add_between' => [
                'label' => 'Insert between',
            ],

            'delete' => [
                'label' => 'Delete',
            ],

            'clone' => [
                'label' => 'Clone',
            ],

            'reorder' => [
                'label' => 'Move',
            ],

            'move_down' => [
                'label' => 'Move down',
            ],

            'move_up' => [
                'label' => 'Move up',
            ],

            'collapse' => [
                'label' => 'Collapse',
            ],

            'expand' => [
                'label' => 'Expand',
            ],

            'collapse_all' => [
                'label' => 'Collapse all',
            ],

            'expand_all' => [
                'label' => 'Expand all',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Upload file',

                'modal' => [

                    'heading' => 'Upload file',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'File',
                                'existing' => 'Replace file',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt text',
                                'existing' => 'Change alt text',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Insert',
                        ],

                        'save' => [
                            'label' => 'Save',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Link',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Open in new tab',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'No merge tag results.',

        'tools' => [
            'align_center' => 'Align center',
            'align_end' => 'Align end',
            'align_justify' => 'Align justify',
            'align_start' => 'Align start',
            'attach_files' => 'Attach files',
            'blockquote' => 'Blockquote',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'clear_formatting' => 'Clear formatting',
            'code' => 'Code',
            'code_block' => 'Code block',
            'custom_blocks' => 'Blocks',
            'details' => 'Details',
            'h1' => 'Title',
            'h2' => 'Heading',
            'h3' => 'Subheading',
            'highlight' => 'Highlight',
            'horizontal_rule' => 'Horizontal rule',
            'italic' => 'Italic',
            'lead' => 'Lead text',
            'link' => 'Link',
            'merge_tags' => 'Merge tags',
            'ordered_list' => 'Numbered list',
            'redo' => 'Redo',
            'small' => 'Small text',
            'strike' => 'Strikethrough',
            'subscript' => 'Subscript',
            'superscript' => 'Superscript',
            'table' => 'Table',
            'table_delete' => 'Delete table',
            'table_add_column_before' => 'Add column before',
            'table_add_column_after' => 'Add column after',
            'table_delete_column' => 'Delete column',
            'table_add_row_before' => 'Add row above',
            'table_add_row_after' => 'Add row below',
            'table_delete_row' => 'Delete row',
            'table_merge_cells' => 'Merge cells',
            'table_split_cell' => 'Split cell',
            'table_toggle_header_row' => 'Toggle header row',
            'underline' => 'Underline',
            'undo' => 'Undo',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Create',

                'modal' => [

                    'heading' => 'Create',

                    'actions' => [

                        'create' => [
                            'label' => 'Create',
                        ],

                        'create_another' => [
                            'label' => 'Create & create another',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'Edit',

                    'actions' => [

                        'save' => [
                            'label' => 'Save',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Yes',
            'false' => 'No',
        ],

        'loading_message' => 'Loading...',

        'max_items_message' => 'Only :count can be selected.',

        'no_search_results_message' => 'No options match your search.',

        'placeholder' => 'Select an option',

        'searching_message' => 'Searching...',

        'search_prompt' => 'Start typing to search...',

    ],

    'tags_input' => [
        'placeholder' => 'New tag',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Hide password',
            ],

            'show_password' => [
                'label' => 'Show password',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Yes',
            'false' => 'No',
        ],

    ],

];
