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
                'label' => 'Burahin',
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
                'label' => 'Alisin ang lahat ng pili',
            ],

            'select_all' => [
                'label' => 'Piliin lahat',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'Tagapili ng kulay',

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

            'label' => 'Editor ng larawan',

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
                    'label' => 'I-flip nang pahiga ang larawan',
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
                    'label' => 'Mag-zoom in',
                ],

                'zoom_out' => [
                    'label' => 'Mag-zoom out',
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
                    'confirmation' => 'Hindi inirerekomenda ang pag-edit ng mga SVG file dahil maaaring bumaba ang kalidad kapag ini-scale.\n Sigurado ka bang gusto mong magpatuloy?',
                    'disabled' => 'Naka-disable ang pag-edit ng mga SVG file dahil maaaring bumaba ang kalidad kapag ini-scale.',
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
                'label' => 'Burahin ang row',
            ],

            'reorder' => [
                'label' => 'Ayusin ang pagkakasunod ng row',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'Mga action',
            ],

            'reorder' => [
                'label' => 'Ayusin ang pagkakasunod',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Dapat ganitong uri ang mga na-upload na file: :values.',

        'file_attachments_max_size_message' => 'Hindi dapat lumampas sa :max kilobytes ang mga na-upload na file.',

        'tools' => [
            'attach_files' => 'Mag-attach ng mga file',
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
                'label' => 'Ayusin ang pagkakasunod',
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
                'label' => 'Burahin',
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
                                'existing' => 'Palitan ang file',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'existing' => 'Palitan ang alt text',
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

                'modal' => [

                    'form' => [

                        'preset' => [

                            'placeholder' => 'Wala',

                            'options' => [
                                'two' => 'Dalawa',
                                'three' => 'Tatlo',
                                'four' => 'Apat',
                                'five' => 'Lima',
                                'two_start_third' => 'Dalawa (start third)',
                                'two_end_third' => 'Dalawa (end third)',
                                'two_start_fourth' => 'Dalawa (start fourth)',
                                'two_end_fourth' => 'Dalawa (end fourth)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Mga column',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Mula sa breakpoint',

                            'options' => [
                                'default' => 'Lahat',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Dalawang asymmetric na column',
                        ],

                    ],

                ],

            ],

            'link' => [

                'modal' => [

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
                                'red' => 'Red',
                                'orange' => 'Orange',
                                'amber' => 'Amber',
                                'yellow' => 'Yellow',
                                'lime' => 'Lime',
                                'green' => 'Green',
                                'emerald' => 'Emerald',
                                'teal' => 'Teal',
                                'cyan' => 'Cyan',
                                'sky' => 'Sky',
                                'blue' => 'Blue',
                                'indigo' => 'Indigo',
                                'violet' => 'Violet',
                                'purple' => 'Purple',
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

        'file_attachments_accepted_file_types_message' => 'Dapat ganitong uri ang mga na-upload na file: :values.',

        'file_attachments_max_size_message' => 'Hindi dapat lumampas sa :max kilobytes ang mga na-upload na file.',

        'no_merge_tag_search_results_message' => 'Walang resulta para sa merge tag.',

        'mentions' => [
            'no_options_message' => 'Walang available na option.',
            'no_search_results_message' => 'Walang resultang tumutugma sa paghahanap mo.',
            'search_prompt' => 'Magsimulang mag-type para maghanap...',
            'searching_message' => 'Naghahanap...',
        ],

        'toolbar' => [
            'label' => 'Toolbar ng editor',
        ],

        'tools' => [
            'align_center' => 'I-align sa gitna',
            'align_end' => 'I-align sa dulo',
            'align_justify' => 'I-align justify',
            'align_start' => 'I-align sa simula',
            'attach_files' => 'Mag-attach ng mga file',
            'clear_formatting' => 'Alisin ang formatting',
            'custom_blocks' => 'Mga block',
            'details' => 'Mga detalye',
            'h1' => 'Pamagat',
            'grid_delete' => 'Burahin ang grid',
            'merge_tags' => 'Mga merge tag',
            'paragraph' => 'Talata',
            'small' => 'Maliit na text',
            'table_delete' => 'Burahin ang table',
            'table_add_column_before' => 'Magdagdag ng column bago nito',
            'table_add_column_after' => 'Magdagdag ng column pagkatapos nito',
            'table_delete_column' => 'Burahin ang column',
            'table_add_row_before' => 'Magdagdag ng row sa itaas',
            'table_add_row_after' => 'Magdagdag ng row sa ibaba',
            'table_delete_row' => 'Burahin ang row',
            'table_merge_cells' => 'Pagsamahin ang mga cell',
            'table_split_cell' => 'Hatiin ang cell',
            'table_toggle_header_row' => 'I-toggle ang header row',
            'table_toggle_header_cell' => 'I-toggle ang header cell',
            'text_color' => 'Kulay ng text',
        ],

        'uploading_file_message' => 'Ina-upload ang file...',

    ],

    'select' => [

        'actions' => [

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

        ],

        'boolean' => [
            'true' => 'Oo',
            'false' => 'Hindi',
        ],

        'loading_message' => 'Naglo-load...',

        'max_items_message' => ':count lang ang puwedeng piliin.',

        'no_options_message' => 'Walang available na option.',

        'no_search_results_message' => 'Walang option na tumutugma sa paghahanap mo.',

        'placeholder' => 'Pumili ng option',

        'searching_message' => 'Naghahanap...',

        'search_prompt' => 'Magsimulang mag-type para maghanap...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Burahin',
            ],

        ],

        'placeholder' => 'Bagong tag',

        'tag_added' => 'Naidagdag: :tag',

        'tag_removed' => 'Naalis: :tag',

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
