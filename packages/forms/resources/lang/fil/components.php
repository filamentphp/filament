<?php

return [
    'builder' => [
        'actions' => [
            'clone' => [
                'label' => 'I-clone',
            ],
            'add' => [
                'label' => 'Idagdag sa :label',
                'modal' => [
                    'heading' => 'Idagdag sa :label',
                    'actions' => [
                        'add' => [
                            'label' => 'Idagdag',
                        ],
                    ],
                ],
            ],
            'add_between' => [
                'label' => 'Ipasok sa pagitan ng mga block',
                'modal' => [
                    'heading' => 'Idagdag sa :label',
                    'actions' => [
                        'add' => [
                            'label' => 'Idagdag',
                        ],
                    ],
                ],
            ],
            'delete' => [
                'label' => 'I-delete',
            ],
            'edit' => [
                'label' => 'I-edit',
                'modal' => [
                    'heading' => 'I-edit ang block',
                    'actions' => [
                        'save' => [
                            'label' => 'I-save ang mga pagbabago',
                        ],
                    ],
                ],
            ],
            'reorder' => [
                'label' => 'Ilipat',
            ],
            'move_down' => [
                'label' => 'Ilipat pababa',
            ],
            'move_up' => [
                'label' => 'Ilipat pataas',
            ],
            'collapse' => [
                'label' => 'I-collapse',
            ],
            'expand' => [
                'label' => 'I-expand',
            ],
            'collapse_all' => [
                'label' => 'I-collapse lahat',
            ],
            'expand_all' => [
                'label' => 'I-expand lahat',
            ],
        ],
    ],
    'checkbox_list' => [
        'actions' => [
            'deselect_all' => [
                'label' => 'Alisin lahat ng napili',
            ],
            'select_all' => [
                'label' => 'Piliin lahat',
            ],
        ],
    ],
    'color_picker' => [
        'panel_label' => 'Pampili ng kulay',
    ],
    'date_time_picker' => [
        'month_select' => [
            'label' => 'Buwan',
        ],
        'year_input' => [
            'label' => 'Taon',
        ],
        'hour_input' => [
            'label' => 'Oras',
        ],
        'minute_input' => [
            'label' => 'Minuto',
        ],
        'second_input' => [
            'label' => 'Segundo',
        ],
    ],
    'file_upload' => [
        'actions' => [
            'download' => [
                'label' => 'I-download',
            ],
            'open' => [
                'label' => 'Buksan sa bagong tab',
            ],
        ],
        'editor' => [
            'label' => 'Image editor',
            'actions' => [
                'cancel' => [
                    'label' => 'Kanselahin',
                ],
                'drag_crop' => [
                    'label' => 'Drag mode na "crop"',
                ],
                'drag_move' => [
                    'label' => 'Drag mode na "move"',
                ],
                'flip_horizontal' => [
                    'label' => 'I-flip nang pahalang ang larawan',
                ],
                'flip_vertical' => [
                    'label' => 'I-flip nang patayo ang larawan',
                ],
                'move_down' => [
                    'label' => 'Ilipat pababa ang larawan',
                ],
                'move_left' => [
                    'label' => 'Ilipat pakaliwa ang larawan',
                ],
                'move_right' => [
                    'label' => 'Ilipat pakanan ang larawan',
                ],
                'move_up' => [
                    'label' => 'Ilipat pataas ang larawan',
                ],
                'reset' => [
                    'label' => 'I-reset',
                ],
                'rotate_left' => [
                    'label' => 'I-rotate pakaliwa ang larawan',
                ],
                'rotate_right' => [
                    'label' => 'I-rotate pakanan ang larawan',
                ],
                'set_aspect_ratio' => [
                    'label' => 'Itakda ang aspect ratio sa :ratio',
                ],
                'save' => [
                    'label' => 'I-save',
                ],
                'zoom_100' => [
                    'label' => 'I-zoom ang larawan sa 100%',
                ],
                'zoom_in' => [
                    'label' => 'I-zoom in',
                ],
                'zoom_out' => [
                    'label' => 'I-zoom out',
                ],
            ],
            'fields' => [
                'height' => [
                    'label' => 'Taas',
                    'unit' => 'px',
                ],
                'rotation' => [
                    'label' => 'Pag-ikot',
                    'unit' => 'deg',
                ],
                'width' => [
                    'label' => 'Lapad',
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
                'label' => 'Mga aspect ratio',
                'no_fixed' => [
                    'label' => 'Malaya',
                ],
            ],
            'svg' => [
                'messages' => [
                    'confirmation' => 'Hindi inirerekomenda ang pag-edit ng mga SVG file dahil puwedeng bumaba ang kalidad kapag binabago ang laki.\\n Sigurado ka bang gusto mong magpatuloy?',
                    'disabled' => 'Naka-disable ang pag-edit ng mga SVG file dahil puwedeng bumaba ang kalidad kapag binabago ang laki.',
                ],
            ],
        ],
    ],
    'key_value' => [
        'actions' => [
            'add' => [
                'label' => 'Magdagdag ng row',
            ],
            'delete' => [
                'label' => 'I-delete ang row',
            ],
            'reorder' => [
                'label' => 'Ayusin ang row',
            ],
        ],
        'columns' => [
            'actions' => [
                'label' => 'Mga action',
            ],
            'reorder' => [
                'label' => 'Ayusin',
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
        'file_attachments_accepted_file_types_message' => 'Dapat ganito ang uri ng mga in-upload na file: :values.',
        'file_attachments_max_size_message' => 'Hindi dapat lumampas sa :max kilobytes ang mga in-upload na file.',
        'tools' => [
            'attach_files' => 'Mag-attach ng mga file',
            'blockquote' => 'Blockquote',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'code_block' => 'Code block',
            'heading' => 'Heading',
            'italic' => 'Italic',
            'link' => 'Link',
            'ordered_list' => 'Numbered list',
            'redo' => 'Ulitin',
            'strike' => 'Strikethrough',
            'table' => 'Table',
            'undo' => 'I-undo',
        ],
    ],
    'modal_table_select' => [
        'actions' => [
            'select' => [
                'label' => 'Piliin',
                'actions' => [
                    'select' => [
                        'label' => 'Piliin',
                    ],
                ],
            ],
        ],
    ],
    'radio' => [
        'boolean' => [
            'true' => 'Oo',
            'false' => 'Hindi',
        ],
    ],
    'repeater' => [
        'columns' => [
            'actions' => [
                'label' => 'Mga action',
            ],
            'reorder' => [
                'label' => 'Ayusin',
            ],
        ],
        'actions' => [
            'add' => [
                'label' => 'Idagdag sa :label',
            ],
            'add_between' => [
                'label' => 'Ipasok sa pagitan',
            ],
            'delete' => [
                'label' => 'I-delete',
            ],
            'clone' => [
                'label' => 'I-clone',
            ],
            'reorder' => [
                'label' => 'Ilipat',
            ],
            'move_down' => [
                'label' => 'Ilipat pababa',
            ],
            'move_up' => [
                'label' => 'Ilipat pataas',
            ],
            'collapse' => [
                'label' => 'I-collapse',
            ],
            'expand' => [
                'label' => 'I-expand',
            ],
            'collapse_all' => [
                'label' => 'I-collapse lahat',
            ],
            'expand_all' => [
                'label' => 'I-expand lahat',
            ],
        ],
    ],
    'rich_editor' => [
        'actions' => [
            'attach_files' => [
                'label' => 'Mag-upload ng file',
                'modal' => [
                    'heading' => 'Mag-upload ng file',
                    'form' => [
                        'file' => [
                            'label' => [
                                'new' => 'File',
                                'existing' => 'Palitan ang file',
                            ],
                        ],
                        'alt' => [
                            'label' => [
                                'new' => 'Alt text',
                                'existing' => 'Baguhin ang alt text',
                            ],
                        ],
                    ],
                ],
            ],
            'custom_block' => [
                'modal' => [
                    'actions' => [
                        'insert' => [
                            'label' => 'Ipasok',
                        ],
                        'save' => [
                            'label' => 'I-save',
                        ],
                    ],
                ],
            ],
            'grid' => [
                'label' => 'Grid',
                'modal' => [
                    'heading' => 'Grid',
                    'form' => [
                        'preset' => [
                            'label' => 'Preset',
                            'placeholder' => 'Wala',
                            'options' => [
                                'two' => 'Dalawa',
                                'three' => 'Tatlo',
                                'four' => 'Apat',
                                'five' => 'Lima',
                                'two_start_third' => 'Dalawa (Ikatlo sa simula)',
                                'two_end_third' => 'Dalawa (Ikatlo sa dulo)',
                                'two_start_fourth' => 'Dalawa (Ikaapat sa simula)',
                                'two_end_fourth' => 'Dalawa (Ikaapat sa dulo)',
                            ],
                        ],
                        'columns' => [
                            'label' => 'Mga column',
                        ],
                        'from_breakpoint' => [
                            'label' => 'Mula sa breakpoint',
                            'options' => [
                                'default' => 'Lahat',
                                'sm' => 'Maliit',
                                'md' => 'Katamtaman',
                                'lg' => 'Malaki',
                                'xl' => 'Napakalaki',
                                '2xl' => 'Dalawang beses na napakalaki',
                            ],
                        ],
                        'is_asymmetric' => [
                            'label' => 'Dalawang hindi pantay na column',
                        ],
                        'start_span' => [
                            'label' => 'Panimulang span',
                        ],
                        'end_span' => [
                            'label' => 'Panghuling span',
                        ],
                    ],
                ],
            ],
            'link' => [
                'label' => 'Link',
                'modal' => [
                    'heading' => 'Link',
                    'form' => [
                        'url' => [
                            'label' => 'URL',
                        ],
                        'should_open_in_new_tab' => [
                            'label' => 'Buksan sa bagong tab',
                        ],
                    ],
                ],
            ],
            'text_color' => [
                'label' => 'Kulay ng text',
                'modal' => [
                    'heading' => 'Kulay ng text',
                    'form' => [
                        'color' => [
                            'label' => 'Kulay',
                            'options' => [
                                'slate' => 'Slate',
                                'gray' => 'Gray',
                                'zinc' => 'Zinc',
                                'neutral' => 'Neutral',
                                'stone' => 'Stone',
                                'mauve' => 'Mauve',
                                'olive' => 'Olive',
                                'mist' => 'Mist',
                                'taupe' => 'Taupe',
                                'red' => 'Pula',
                                'orange' => 'Orange',
                                'amber' => 'Amber',
                                'yellow' => 'Dilaw',
                                'lime' => 'Lime',
                                'green' => 'Berde',
                                'emerald' => 'Emerald',
                                'teal' => 'Teal',
                                'cyan' => 'Cyan',
                                'sky' => 'Sky',
                                'blue' => 'Asul',
                                'indigo' => 'Indigo',
                                'violet' => 'Violet',
                                'purple' => 'Lila',
                                'fuchsia' => 'Fuchsia',
                                'pink' => 'Pink',
                                'rose' => 'Rose',
                            ],
                        ],
                        'custom_color' => [
                            'label' => 'Custom na kulay',
                        ],
                    ],
                ],
            ],
        ],
        'file_attachments_accepted_file_types_message' => 'Dapat ganito ang uri ng mga in-upload na file: :values.',
        'file_attachments_max_size_message' => 'Hindi dapat lumampas sa :max kilobytes ang mga in-upload na file.',
        'no_merge_tag_search_results_message' => 'Walang nahanap na merge tag.',
        'mentions' => [
            'no_options_message' => 'Walang available na option.',
            'no_search_results_message' => 'Walang resultang tugma sa search mo.',
            'search_prompt' => 'Mag-type para mag-search...',
            'searching_message' => 'Naghahanap...',
        ],
        'toolbar' => [
            'label' => 'Editor toolbar',
        ],
        'tools' => [
            'align_center' => 'I-align sa gitna',
            'align_end' => 'I-align sa dulo',
            'align_justify' => 'I-justify',
            'align_start' => 'I-align sa simula',
            'attach_files' => 'Mag-attach ng mga file',
            'blockquote' => 'Blockquote',
            'bold' => 'Bold',
            'bullet_list' => 'Bullet list',
            'clear_formatting' => 'Alisin ang formatting',
            'code' => 'Code',
            'code_block' => 'Code block',
            'custom_blocks' => 'Mga block',
            'details' => 'Mga detalye',
            'h1' => 'Pamagat',
            'h2' => 'Heading 2',
            'h3' => 'Heading 3',
            'h4' => 'Heading 4',
            'h5' => 'Heading 5',
            'h6' => 'Heading 6',
            'grid' => 'Grid',
            'grid_delete' => 'I-delete ang grid',
            'highlight' => 'I-highlight',
            'horizontal_rule' => 'Pahalang na linya',
            'italic' => 'Italic',
            'lead' => 'Lead text',
            'link' => 'Link',
            'merge_tags' => 'Mga merge tag',
            'ordered_list' => 'Numbered list',
            'paragraph' => 'Talata',
            'redo' => 'Ulitin',
            'small' => 'Maliit na text',
            'strike' => 'Strikethrough',
            'subscript' => 'Subscript',
            'superscript' => 'Superscript',
            'table' => 'Table',
            'table_delete' => 'I-delete ang table',
            'table_add_column_before' => 'Magdagdag ng column bago nito',
            'table_add_column_after' => 'Magdagdag ng column pagkatapos nito',
            'table_delete_column' => 'I-delete ang column',
            'table_add_row_before' => 'Magdagdag ng row sa itaas',
            'table_add_row_after' => 'Magdagdag ng row sa ibaba',
            'table_delete_row' => 'I-delete ang row',
            'table_merge_cells' => 'Pagsamahin ang mga cell',
            'table_split_cell' => 'Hatiin ang cell',
            'table_toggle_header_row' => 'I-toggle ang header row',
            'table_toggle_header_cell' => 'I-toggle ang header cell',
            'text_color' => 'Kulay ng text',
            'underline' => 'Salungguhitan',
            'undo' => 'I-undo',
        ],
        'uploading_file_message' => 'Ina-upload ang file...',
    ],
    'select' => [
        'actions' => [
            'clear' => [
                'label' => 'I-clear',
            ],
            'create_option' => [
                'label' => 'Gumawa',
                'modal' => [
                    'heading' => 'Gumawa',
                    'actions' => [
                        'create' => [
                            'label' => 'Gumawa',
                        ],
                        'create_another' => [
                            'label' => 'Gumawa at gumawa pa ng isa',
                        ],
                    ],
                ],
            ],
            'edit_option' => [
                'label' => 'I-edit',
                'modal' => [
                    'heading' => 'I-edit',
                    'actions' => [
                        'save' => [
                            'label' => 'I-save',
                        ],
                    ],
                ],
            ],
            'remove_option' => [
                'label' => 'Alisin ang :label',
            ],
        ],
        'boolean' => [
            'true' => 'Oo',
            'false' => 'Hindi',
        ],
        'loading_message' => 'Naglo-load...',
        'max_items_message' => ':count lang ang puwedeng piliin.',
        'no_options_message' => 'Walang available na option.',
        'no_search_results_message' => 'Walang option na tugma sa search mo.',
        'placeholder' => 'Pumili ng option',
        'searching_message' => 'Naghahanap...',
        'search_label' => 'Maghanap',
        'search_prompt' => 'Mag-type para mag-search...',
    ],
    'tags_input' => [
        'actions' => [
            'delete' => [
                'label' => 'I-delete',
            ],
        ],
        'placeholder' => 'Bagong tag',
        'tag_added' => 'Idinagdag: :tag',
        'tag_removed' => 'Inalis: :tag',
    ],
    'text_input' => [
        'actions' => [
            'copy' => [
                'label' => 'Kopyahin',
                'message' => 'Nakopya',
            ],
            'hide_password' => [
                'label' => 'Itago ang password',
            ],
            'show_password' => [
                'label' => 'Ipakita ang password',
            ],
        ],
    ],
    'toggle_buttons' => [
        'boolean' => [
            'true' => 'Oo',
            'false' => 'Hindi',
        ],
    ],
];
