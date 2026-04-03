<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonatu',
            ],

            'add' => [

                'label' => 'Gehitu :label',

                'modal' => [

                    'heading' => 'Gehitu :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Gehitu',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Txertatu blokeen artean',

                'modal' => [

                    'heading' => 'Gehitu :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Gehitu',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Ezabatu',
            ],

            'edit' => [

                'label' => 'Editatu',

                'modal' => [

                    'heading' => 'Blokea editatu',

                    'actions' => [

                        'save' => [
                            'label' => 'Gorde aldaketak',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Mugitu',
            ],

            'move_down' => [
                'label' => 'Jaitsi',
            ],

            'move_up' => [
                'label' => 'Igo',
            ],

            'collapse' => [
                'label' => 'Kontrairatu',
            ],

            'expand' => [
                'label' => 'Zabaldu',
            ],

            'collapse_all' => [
                'label' => 'Kontrairatu dena',
            ],

            'expand_all' => [
                'label' => 'Zabaldu dena',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deselektatu guztia',
            ],

            'select_all' => [
                'label' => 'Aukeratu guztia',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Utzi',
                ],

                'drag_crop' => [
                    'label' => 'Arrastatu modua "moztu"',
                ],

                'drag_move' => [
                    'label' => 'Arrastatu modua "mugitu"',
                ],

                'flip_horizontal' => [
                    'label' => 'Irudia horizontalki irauli',
                ],

                'flip_vertical' => [
                    'label' => 'Irudia bertikalki irauli',
                ],

                'move_down' => [
                    'label' => 'Irudia behera mugitu',
                ],

                'move_left' => [
                    'label' => 'Irudia ezkerrera mugitu',
                ],

                'move_right' => [
                    'label' => 'Irudia eskuinera mugitu',
                ],

                'move_up' => [
                    'label' => 'Irudia gora mugitu',
                ],

                'reset' => [
                    'label' => 'Berrezarri',
                ],

                'rotate_left' => [
                    'label' => 'Irudia ezkerrera biratu',
                ],

                'rotate_right' => [
                    'label' => 'Irudia eskuinera biratu',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Aspektu-erlazioa ezarri :ratio',
                ],

                'save' => [
                    'label' => 'Gorde',
                ],

                'zoom_100' => [
                    'label' => 'Irudia %100era handitu',
                ],

                'zoom_in' => [
                    'label' => 'Handitu',
                ],

                'zoom_out' => [
                    'label' => 'Txikitu',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Altuera',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Biraketa',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Zabalera',
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

                'label' => 'Aspektu-erlazioak',

                'no_fixed' => [
                    'label' => 'Librea',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG fitxategiak editatzea ez da gomendagarria, eskala aldatzean kalitate-galera eragin dezakeelako.\n Ziur zaude jarraitu nahi duzula?',
                    'disabled' => 'SVG fitxategiak editatzea desgaituta dago, eskala aldatzean kalitate-galera eragin dezakeelako.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Gehitu errenkada',
            ],

            'delete' => [
                'label' => 'Ezabatu errenkada',
            ],

            'reorder' => [
                'label' => 'Birordenatu errenkada',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Giltza',
            ],

            'value' => [
                'label' => 'Balioa',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Igotako fitxategiek mota hauetakoak izan behar dute: :values.',

        'file_attachments_max_size_message' => 'Igotako fitxategiek ezin dute :max kilobyte baino gehiago izan.',

        'tools' => [
            'attach_files' => 'Erantsi agiriak',
            'blockquote' => 'Aipuak',
            'bold' => 'Lodia',
            'bullet_list' => 'Buletinak',
            'code_block' => 'Kode-blokea',
            'heading' => 'Goiburua',
            'italic' => 'Etzana',
            'link' => 'Esteka',
            'ordered_list' => 'Zerrenda zenbakiduna',
            'redo' => 'Berregin',
            'strike' => 'Marratua',
            'table' => 'Taula',
            'undo' => 'Desegin',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Hautatu',

                'actions' => [

                    'select' => [
                        'label' => 'Hautatu',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Bai',
            'false' => 'Ez',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Gehitu :label',
            ],

            'add_between' => [
                'label' => 'Txertatu tartean',
            ],

            'delete' => [
                'label' => 'Ezabatu',
            ],

            'clone' => [
                'label' => 'Klonatu',
            ],

            'reorder' => [
                'label' => 'Mugitu',
            ],

            'move_down' => [
                'label' => 'Jaitsi',
            ],

            'move_up' => [
                'label' => 'Igo',
            ],

            'collapse' => [
                'label' => 'Kontrairatu',
            ],

            'expand' => [
                'label' => 'Zabaldu',
            ],

            'collapse_all' => [
                'label' => 'Kontrairatu dena',
            ],

            'expand_all' => [
                'label' => 'Zabaldu dena',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Fitxategia igo',

                'modal' => [

                    'heading' => 'Fitxategia igo',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fitxategia',
                                'existing' => 'Fitxategia ordezkatu',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Testu alternatiboa',
                                'existing' => 'Testu alternatiboa aldatu',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Txertatu',
                        ],

                        'save' => [
                            'label' => 'Gorde',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Sareta',

                'modal' => [

                    'heading' => 'Sareta',

                    'form' => [

                        'preset' => [

                            'label' => 'Aurrezarpena',

                            'placeholder' => 'Bat ere ez',

                            'options' => [
                                'two' => 'Bi',
                                'three' => 'Hiru',
                                'four' => 'Lau',
                                'five' => 'Bost',
                                'two_start_third' => 'Bi (hasierako herena)',
                                'two_end_third' => 'Bi (amaierako herena)',
                                'two_start_fourth' => 'Bi (hasierako laurdena)',
                                'two_end_fourth' => 'Bi (amaierako laurdena)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Zutabeak',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Puntutik aurrera',

                            'options' => [
                                'default' => 'Denak',
                                'sm' => 'Txikia',
                                'md' => 'Ertaina',
                                'lg' => 'Handia',
                                'xl' => 'Oso handia',
                                '2xl' => 'Bi aldiz oso handia',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Bi zutabe asimetrikoak',
                        ],

                        'start_span' => [
                            'label' => 'Hasierako hedadura',
                        ],

                        'end_span' => [
                            'label' => 'Amaierako hedadura',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Esteka',

                'modal' => [

                    'heading' => 'Esteka',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Leiho berrian ireki',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Testuaren kolorea',

                'modal' => [

                    'heading' => 'Testuaren kolorea',

                    'form' => [

                        'color' => [
                            'label' => 'Kolorea',
                        ],

                        'custom_color' => [
                            'label' => 'Kolore pertsonalizatua',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Igotako fitxategiek mota hauetakoak izan behar dute: :values.',

        'file_attachments_max_size_message' => 'Igotako fitxategiek ezin dute :max kilobyte baino gehiago izan.',

        'no_merge_tag_search_results_message' => 'Ez da batu-etiketa emaitzarik aurkitu.',

        'mentions' => [
            'no_options_message' => 'Ez dago aukerarik.',
            'no_search_results_message' => 'Ez da bat datorren bilaketarekin.',
            'search_prompt' => 'Idatzi bilatzeko...',
            'searching_message' => 'Bilatzen...',
        ],

        'tools' => [
            'align_center' => 'Zentratu',
            'align_end' => 'Amaierara lerrokatu',
            'align_justify' => 'Justifikatu',
            'align_start' => 'Hasierara lerrokatu',
            'attach_files' => 'Erantsi agiriak',
            'blockquote' => 'Aipuak',
            'bold' => 'Lodia',
            'bullet_list' => 'Buletinak',
            'clear_formatting' => 'Formatua garbitu',
            'code' => 'Kodea',
            'code_block' => 'Kode-blokea',
            'custom_blocks' => 'Blokeak',
            'details' => 'Xehetasunak',
            'h1' => 'Izenburua',
            'h2' => '2. goiburua',
            'h3' => '3. goiburua',
            'h4' => '4. goiburua',
            'h5' => '5. goiburua',
            'h6' => '6. goiburua',
            'grid' => 'Sareta',
            'grid_delete' => 'Sareta ezabatu',
            'highlight' => 'Nabarmendu',
            'horizontal_rule' => 'Marra horizontala',
            'italic' => 'Etzana',
            'lead' => 'Testu nagusia',
            'link' => 'Esteka',
            'merge_tags' => 'Batu-etiketak',
            'ordered_list' => 'Zerrenda zenbakiduna',
            'paragraph' => 'Paragrafoa',
            'redo' => 'Berregin',
            'small' => 'Testu txikia',
            'strike' => 'Marratua',
            'subscript' => 'Azpi-indizea',
            'superscript' => 'Goi-indizea',
            'table' => 'Taula',
            'table_delete' => 'Taula ezabatu',
            'table_add_column_before' => 'Zutabea gehitu aurretik',
            'table_add_column_after' => 'Zutabea gehitu ondoren',
            'table_delete_column' => 'Zutabea ezabatu',
            'table_add_row_before' => 'Errenkada gehitu gainean',
            'table_add_row_after' => 'Errenkada gehitu azpian',
            'table_delete_row' => 'Errenkada ezabatu',
            'table_merge_cells' => 'Gelaxkak batu',
            'table_split_cell' => 'Gelaxka zatitu',
            'table_toggle_header_row' => 'Goiburu-errenkada txandakatu',
            'table_toggle_header_cell' => 'Goiburu-gelaxka txandakatu',
            'text_color' => 'Testuaren kolorea',
            'underline' => 'Azpimarratua',
            'undo' => 'Desegin',
        ],

        'uploading_file_message' => 'Fitxategia igotzen...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Sortu',

                'modal' => [

                    'heading' => 'Sortu',

                    'actions' => [

                        'create' => [
                            'label' => 'Sortu',
                        ],

                        'create_another' => [
                            'label' => 'Sortu eta beste bat sortu',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Editatu',

                'modal' => [

                    'heading' => 'Editatu',

                    'actions' => [

                        'save' => [
                            'label' => 'Gorde',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Bai',
            'false' => 'Ez',
        ],

        'loading_message' => 'Kargatzen...',

        'max_items_message' => 'Soilik :count hauta daiteke.',

        'no_options_message' => 'Ez dago aukerarik.',

        'no_search_results_message' => 'Ez da bat datorren bilaketarekin.',

        'placeholder' => 'Aukeratu aukera bat',

        'searching_message' => 'Bilatzen...',

        'search_prompt' => 'Idatzi bilatzeko...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Ezabatu',
            ],

        ],

        'placeholder' => 'Etiketa berria',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopiatu',
                'message' => 'Kopiatuta',
            ],

            'hide_password' => [
                'label' => 'Pasahitza ezkutatu',
            ],

            'show_password' => [
                'label' => 'Pasahitza erakutsi',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Bai',
            'false' => 'Ez',
        ],

    ],

];
