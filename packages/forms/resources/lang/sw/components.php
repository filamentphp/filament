<?php

return [

    'builder' => [
        'actions' => [
            'clone' => [
                'label' => 'Iga',
            ],

            'add' => [
                'label' => 'Ongeza kwenye :label',

                'modal' => [
                    'heading' => 'Ongeza kwa :label',

                    'actions' => [
                        'add' => [
                            'label' => 'Ongeza',
                        ],
                    ],
                ],
            ],

            'add_between' => [
                'label' => 'Ingiza',

                'modal' => [
                    'heading' => 'Ongeza kwa :label',

                    'actions' => [
                        'add' => [
                            'label' => 'Ongeza',
                        ],
                    ],
                ],
            ],

            'delete' => [
                'label' => 'Futa',
            ],

            'edit' => [
                'label' => 'Hariri',

                'modal' => [
                    'heading' => 'Hariri kipande',

                    'actions' => [
                        'save' => [
                            'label' => 'Hifadhi mabadiliko',
                        ],
                    ],
                ],
            ],

            'reorder' => [
                'label' => 'Hamisha',
            ],

            'move_down' => [
                'label' => 'Sogeza chini',
            ],

            'move_up' => [
                'label' => 'Sogeza juu',
            ],

            'collapse' => [
                'label' => 'Kunja',
            ],

            'expand' => [
                'label' => 'Kunjua',
            ],

            'collapse_all' => [
                'label' => 'Kunja zote',
            ],

            'expand_all' => [
                'label' => 'Kunjua zote',
            ],
        ],
    ],

    'checkbox_list' => [
        'actions' => [
            'deselect_all' => [
                'label' => 'Ondoa uchaguzi wote',
            ],

            'select_all' => [
                'label' => 'Chagua vyote',
            ],
        ],
    ],

    'color_picker' => [
        'panel_label' => 'Kichagua rangi',
    ],

    'date_time_picker' => [
        'month_select' => [
            'label' => 'Mwezi',
        ],

        'year_input' => [
            'label' => 'Mwaka',
        ],

        'hour_input' => [
            'label' => 'Saa',
        ],

        'minute_input' => [
            'label' => 'Dakika',
        ],

        'second_input' => [
            'label' => 'Sekunde',
        ],
    ],

    'file_upload' => [
        'actions' => [
            'download' => [
                'label' => 'Pakua',
            ],

            'open' => [
                'label' => 'Fungua kwenye kichupo kipya',
            ],
        ],

        'editor' => [
            'label' => 'Kihariri picha',

            'actions' => [
                'cancel' => [
                    'label' => 'Ghairi',
                ],

                'drag_crop' => [
                    'label' => 'Hali ya kuvuta "kata"',
                ],

                'drag_move' => [
                    'label' => 'Hali ya kuvuta "hamisha"',
                ],

                'flip_horizontal' => [
                    'label' => 'Geuza picha kwa usawa',
                ],

                'flip_vertical' => [
                    'label' => 'Geuza picha kwa wima',
                ],

                'move_down' => [
                    'label' => 'Hamisha picha chini',
                ],

                'move_left' => [
                    'label' => 'Hamisha picha kushoto',
                ],

                'move_right' => [
                    'label' => 'Hamisha picha kulia',
                ],

                'move_up' => [
                    'label' => 'Hamisha picha juu',
                ],

                'reset' => [
                    'label' => 'Weka upya',
                ],

                'rotate_left' => [
                    'label' => 'Zungusha picha kwenda kushoto',
                ],

                'rotate_right' => [
                    'label' => 'Zungusha picha kwenda kulia',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Weka uwiano wa pande kuwa :ratio',
                ],

                'save' => [
                    'label' => 'Hifadhi',
                ],

                'zoom_100' => [
                    'label' => 'Weka ukubwa wa picha hadi 100%',
                ],

                'zoom_in' => [
                    'label' => 'Kuza ndani',
                ],

                'zoom_out' => [
                    'label' => 'Kuza nje',
                ],
            ],

            'fields' => [
                'height' => [
                    'label' => 'Urefu',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Mzunguko',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Upana',
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
                'label' => 'Uwiano wa pande',

                'no_fixed' => [
                    'label' => 'Huria',
                ],
            ],

            'svg' => [
                'messages' => [
                    'confirmation' => 'Kuhariri faili za SVG haipendekezwi kwa sababu kunaweza kusababisha upotezaji wa ubora unapozidisha ukubwa.\\n Una uhakika unataka kuendelea?',
                    'disabled' => 'Kuhariri faili za SVG kumezimwa kwa sababu kunaweza kusababisha upotezaji wa ubora unapozidisha ukubwa.',
                ],
            ],
        ],
    ],

    'key_value' => [
        'actions' => [
            'add' => [
                'label' => 'Ongeza mstari',
            ],

            'delete' => [
                'label' => 'Futa mstari',
            ],

            'reorder' => [
                'label' => 'Pangilia mstari',
            ],
        ],

        'columns' => [
            'actions' => [
                'label' => 'Vitendo',
            ],

            'reorder' => [
                'label' => 'Panga upya',
            ],
        ],

        'fields' => [
            'key' => [
                'label' => 'Ufunguo',
            ],

            'value' => [
                'label' => 'Thamani',
            ],
        ],
    ],

    'markdown_editor' => [
        'file_attachments_accepted_file_types_message' => 'Faili zilizopakiwa lazima ziwe na aina: :values.',

        'file_attachments_max_size_message' => 'Faili zilizopakiwa hazipaswi kuzidi kilobaiti :max.',

        'tools' => [
            'attach_files' => 'Ambatisha faili',
            'blockquote' => 'Nukuu',
            'bold' => 'Nzito',
            'bullet_list' => 'Orodha ya vitone',
            'code_block' => 'Kizuizi cha msimbo',
            'heading' => 'Kichwa',
            'italic' => 'Italiki',
            'link' => 'Kiungo',
            'ordered_list' => 'Orodha yenye nambari',
            'redo' => 'Rudia',
            'strike' => 'Piga kupitia',
            'table' => 'Jedwali',
            'undo' => 'Tengua',
        ],
    ],

    'modal_table_select' => [
        'actions' => [
            'select' => [
                'label' => 'Chagua',

                'actions' => [
                    'select' => [
                        'label' => 'Chagua',
                    ],
                ],
            ],
        ],
    ],

    'radio' => [
        'boolean' => [
            'true' => 'Ndiyo',
            'false' => 'Hapana',
        ],
    ],

    'repeater' => [
        'columns' => [
            'actions' => [
                'label' => 'Vitendo',
            ],

            'reorder' => [
                'label' => 'Panga upya',
            ],
        ],

        'actions' => [
            'add' => [
                'label' => 'Ongeza kwenye :label',
            ],

            'add_between' => [
                'label' => 'Ingiza',
            ],

            'delete' => [
                'label' => 'Futa',
            ],

            'clone' => [
                'label' => 'Iga',
            ],

            'reorder' => [
                'label' => 'Hamisha',
            ],

            'move_down' => [
                'label' => 'Sogeza chini',
            ],

            'move_up' => [
                'label' => 'Sogeza juu',
            ],

            'collapse' => [
                'label' => 'Kunja',
            ],

            'expand' => [
                'label' => 'Kunjua',
            ],

            'collapse_all' => [
                'label' => 'Kunja zote',
            ],

            'expand_all' => [
                'label' => 'Kunjua zote',
            ],
        ],
    ],

    'rich_editor' => [
        'actions' => [
            'attach_files' => [
                'label' => 'Pakia faili',

                'modal' => [
                    'heading' => 'Pakia faili',

                    'form' => [
                        'file' => [
                            'label' => [
                                'new' => 'Faili',
                                'existing' => 'Badilisha faili',
                            ],
                        ],

                        'alt' => [
                            'label' => [
                                'new' => 'Maandishi mbadala',
                                'existing' => 'Badilisha maandishi mbadala',
                            ],
                        ],
                    ],
                ],
            ],

            'custom_block' => [
                'modal' => [
                    'actions' => [
                        'insert' => [
                            'label' => 'Weka',
                        ],

                        'save' => [
                            'label' => 'Hifadhi',
                        ],
                    ],
                ],
            ],

            'grid' => [
                'label' => 'Gridi',

                'modal' => [
                    'heading' => 'Gridi',

                    'form' => [
                        'preset' => [
                            'label' => 'Mpangilio',

                            'placeholder' => 'Hakuna',

                            'options' => [
                                'two' => 'Mbili',
                                'three' => 'Tatu',
                                'four' => 'Nne',
                                'five' => 'Tano',
                                'two_start_third' => 'Two (Start Third)',
                                'two_end_third' => 'Two (End Third)',
                                'two_start_fourth' => 'Two (Start Fourth)',
                                'two_end_fourth' => 'Two (End Fourth)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Safu wima',
                        ],

                        'from_breakpoint' => [
                            'label' => 'Kutoka kwenye breakpoint',

                            'options' => [
                                'default' => 'Zote',
                                'sm' => 'Ndogo',
                                'md' => 'Wastani',
                                'lg' => 'Kubwa',
                                'xl' => 'Kubwa zaidi',
                                '2xl' => 'Kubwa mno',
                            ],
                        ],

                        'is_asymmetric' => [
                            'label' => 'Safu wima mbili zisizo sawa',
                        ],

                        'start_span' => [
                            'label' => 'Upana wa mwanzo',
                        ],

                        'end_span' => [
                            'label' => 'Upana wa mwisho',
                        ],
                    ],
                ],
            ],

            'link' => [
                'label' => 'Kiungo',

                'modal' => [
                    'heading' => 'Kiungo',

                    'form' => [
                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Fungua kwenye kichupo kipya',
                        ],
                    ],
                ],
            ],

            'text_color' => [
                'label' => 'Rangi ya maandishi',

                'modal' => [
                    'heading' => 'Rangi ya maandishi',

                    'form' => [
                        'color' => [
                            'label' => 'Rangi',

                            'options' => [
                                'slate' => 'Slate',
                                'gray' => 'Kijivu',
                                'zinc' => 'Zinc',
                                'neutral' => 'Neutral',
                                'stone' => 'Stone',
                                'mauve' => 'Mauve',
                                'olive' => 'Olive',
                                'mist' => 'Mist',
                                'taupe' => 'Taupe',
                                'red' => 'Nyekundu',
                                'orange' => 'Machungwa',
                                'amber' => 'Amber',
                                'yellow' => 'Njano',
                                'lime' => 'Lime',
                                'green' => 'Kijani',
                                'emerald' => 'Emerald',
                                'teal' => 'Teal',
                                'cyan' => 'Cyan',
                                'sky' => 'Sky',
                                'blue' => 'Bluu',
                                'indigo' => 'Indigo',
                                'violet' => 'Violet',
                                'purple' => 'Zambarau',
                                'fuchsia' => 'Fuchsia',
                                'pink' => 'Pinki',
                                'rose' => 'Rose',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Rangi maalum',
                        ],
                    ],
                ],
            ],
        ],

        'file_attachments_accepted_file_types_message' => 'Faili zilizopakiwa lazima ziwe na aina: :values.',

        'file_attachments_max_size_message' => 'Faili zilizopakiwa hazipaswi kuzidi kilobaiti :max.',

        'no_merge_tag_search_results_message' => 'Hakuna matokeo ya lebo za kuunganisha.',

        'mentions' => [
            'no_options_message' => 'Hakuna chaguo zilizopo.',
            'no_search_results_message' => 'Hakuna matokeo yanayolingana na utafutaji wako.',
            'search_prompt' => 'Anza kuandika ili kutafuta...',
            'searching_message' => 'Inatafuta...',
        ],

        'toolbar' => [
            'label' => 'Upau wa vifaa vya kihariri',
        ],

        'tools' => [
            'align_center' => 'Panga katikati',
            'align_end' => 'Panga mwisho',
            'align_justify' => 'Panga sawazisha',
            'align_start' => 'Panga mwanzo',
            'attach_files' => 'Ambatisha faili',
            'blockquote' => 'Nukuu ya kuzuia',
            'bold' => 'Nzito',
            'bullet_list' => 'Orodha ya vitone',
            'clear_formatting' => 'Ondoa uumbizaji',
            'code' => 'Msimbo',
            'code_block' => 'Kizuizi cha msimbo',
            'custom_blocks' => 'Vipande',
            'details' => 'Maelezo',
            'h1' => 'Kichwa kikuu',
            'h2' => 'Kichwa 2',
            'h3' => 'Kichwa 3',
            'h4' => 'Kichwa 4',
            'h5' => 'Kichwa 5',
            'h6' => 'Kichwa 6',
            'grid' => 'Gridi',
            'grid_delete' => 'Futa gridi',
            'highlight' => 'Angaza',
            'horizontal_rule' => 'Mstari wa mlalo',
            'italic' => 'Italiki',
            'lead' => 'Maandishi ya utangulizi',
            'link' => 'Kiungo',
            'merge_tags' => 'Lebo za kuunganisha',
            'ordered_list' => 'Orodha yenye nambari',
            'paragraph' => 'Aya',
            'redo' => 'Rudia',
            'small' => 'Maandishi madogo',
            'strike' => 'Piga kupitia',
            'subscript' => 'Maandishi ya chini',
            'superscript' => 'Maandishi ya juu',
            'table' => 'Jedwali',
            'table_delete' => 'Futa jedwali',
            'table_add_column_before' => 'Ongeza safu wima kabla',
            'table_add_column_after' => 'Ongeza safu wima baada',
            'table_delete_column' => 'Futa safu wima',
            'table_add_row_before' => 'Ongeza mstari juu',
            'table_add_row_after' => 'Ongeza mstari chini',
            'table_delete_row' => 'Futa mstari',
            'table_merge_cells' => 'Unganisha seli',
            'table_split_cell' => 'Gawanya seli',
            'table_toggle_header_row' => 'Badili mstari wa vichwa',
            'table_toggle_header_cell' => 'Badili seli ya kichwa',
            'text_color' => 'Rangi ya maandishi',
            'underline' => 'Piga mstari',
            'undo' => 'Tengua',
        ],

        'uploading_file_message' => 'Inapakia faili...',
    ],

    'select' => [
        'actions' => [
            'create_option' => [
                'label' => 'Unda',

                'modal' => [
                    'heading' => 'Tengeneza',

                    'actions' => [
                        'create' => [
                            'label' => 'Tengeneza',
                        ],

                        'create_another' => [
                            'label' => 'Unda & unda nyingine',
                        ],
                    ],
                ],
            ],

            'edit_option' => [
                'label' => 'Hariri',

                'modal' => [
                    'heading' => 'Hariri',

                    'actions' => [
                        'save' => [
                            'label' => 'Hifadhi',
                        ],
                    ],
                ],
            ],
        ],

        'boolean' => [
            'true' => 'Ndiyo',
            'false' => 'Hapana',
        ],

        'loading_message' => 'Inapakia...',

        'max_items_message' => 'Ni :count pekee ndiyo inaweza kuchaguliwa.',

        'no_options_message' => 'Hakuna chaguo zilizopo.',

        'no_search_results_message' => 'Hakuna chaguzi zinazolingana na utafutaji wako.',

        'placeholder' => 'Chagua chaguo',

        'searching_message' => 'Inatafuta...',

        'search_prompt' => 'Anza kuandika ili kutafuta...',
    ],

    'tags_input' => [
        'actions' => [
            'delete' => [
                'label' => 'Futa',
            ],
        ],

        'placeholder' => 'Lebo mpya',

        'tag_added' => 'Imeongezwa: :tag',

        'tag_removed' => 'Imeondolewa: :tag',
    ],

    'text_input' => [
        'actions' => [
            'copy' => [
                'label' => 'Nakili',
                'message' => 'Imenakiliwa',
            ],

            'hide_password' => [
                'label' => 'Ficha nenosiri',
            ],

            'show_password' => [
                'label' => 'Onyesha nenosiri',
            ],
        ],
    ],

    'toggle_buttons' => [
        'boolean' => [
            'true' => 'Ndiyo',
            'false' => 'Hapana',
        ],
    ],

];
