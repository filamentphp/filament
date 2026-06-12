<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonuoti',
            ],

            'add' => [
                'label' => 'Pridėti prie :label',

                'modal' => [

                    'heading' => 'Pridėti prie :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Pridėti',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'Pridėti tarp blokų',

                'modal' => [

                    'heading' => 'Pridėti prie :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Pridėti',
                        ],

                    ],

                ],
            ],

            'delete' => [
                'label' => 'Ištrinti',
            ],

            'edit' => [

                'label' => 'Redaguoti',

                'modal' => [

                    'heading' => 'Redaguoti bloką',

                    'actions' => [

                        'save' => [
                            'label' => 'Išsaugoti pakeitimus',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Pastumti',
            ],

            'move_down' => [
                'label' => 'Žemyn',
            ],

            'move_up' => [
                'label' => 'Aukštyn',
            ],

            'collapse' => [
                'label' => 'Suskleisti',
            ],

            'expand' => [
                'label' => 'Išskleisti',
            ],

            'collapse_all' => [
                'label' => 'Suskleisti viską',
            ],

            'expand_all' => [
                'label' => 'Išskleisti viską',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Atžymėti viską',
            ],

            'select_all' => [
                'label' => 'Pasirinkti viską',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Atšaukti',
                ],

                'drag_crop' => [
                    'label' => 'Apkirpti velkant',
                ],

                'drag_move' => [
                    'label' => 'Perkelti velkant',
                ],

                'flip_horizontal' => [
                    'label' => 'Apversti nuotrauką horizontaliai',
                ],

                'flip_vertical' => [
                    'label' => 'Apversti nuotrauką vertikaliai',
                ],

                'move_down' => [
                    'label' => 'Perkelti vaizdą žemyn',
                ],

                'move_left' => [
                    'label' => 'Perkelti vaizdą į kairę',
                ],

                'move_right' => [
                    'label' => 'Perkelti vaizdą į dešinę',
                ],

                'move_up' => [
                    'label' => 'Perkelti vaizdą aukštyn',
                ],

                'reset' => [
                    'label' => 'Atstatyti',
                ],

                'rotate_left' => [
                    'label' => 'Pasukti nuotrauką į kairę',
                ],

                'rotate_right' => [
                    'label' => 'Pasukti nuotrauka į dešinę',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Nustatyti kraštinių santikį į :ratio',
                ],

                'save' => [
                    'label' => 'Išsaugoti',
                ],

                'zoom_100' => [
                    'label' => 'Priartinti nuotrauką iki 100%',
                ],

                'zoom_in' => [
                    'label' => 'Priartinti',
                ],

                'zoom_out' => [
                    'label' => 'Atitraukti',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Aukštis',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotacija',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Plotis',
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

                'label' => 'Kraštinių santykiai',

                'no_fixed' => [
                    'label' => 'Laisvas',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Redaguoti SVG failų nerekomenduojama, kadangi tai gali įtakoti kokybės praradimą keičiant mastelį.\n Ar tikrai norite tęsti?',
                    'disabled' => 'SVG failų redagavimas išjungtas, kadangi tai gali įtakoti kokybės praradimą keičiant mastelį.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Pridėti eilutę',
            ],

            'delete' => [
                'label' => 'Ištrinti eilutę',
            ],

            'reorder' => [
                'label' => 'Pastumti',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Raktas',
            ],

            'value' => [
                'label' => 'Reikšmė',
            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Taip',
            'false' => 'Ne',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Įkelti failai turi būti šių tipų: :values.',

        'file_attachments_max_size_message' => 'Įkelti failai negali būti didesni nei :max kilobaitų.',

        'tools' => [
            'attach_files' => 'Pridėti failus',
            'blockquote' => 'Citatos blokas',
            'bold' => 'Paryškinta',
            'bullet_list' => 'Ženklų sąrašas',
            'code_block' => 'Kodo blokas',
            'heading' => 'Antraštė',
            'italic' => 'Kursyvu',
            'link' => 'Nuoroda',
            'ordered_list' => 'Sunumeruotas sąrašas',
            'redo' => 'Perdaryti',
            'strike' => 'Perbraukti',
            'table' => 'Lentelė',
            'undo' => 'Anuliuoti',
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

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Pridėti prie :label',
            ],

            'add_between' => [
                'label' => 'Pridėti tarp',
            ],

            'delete' => [
                'label' => 'Ištrinti',
            ],

            'clone' => [
                'label' => 'Klonuoti',
            ],

            'reorder' => [
                'label' => 'Pastumti',
            ],

            'move_down' => [
                'label' => 'Žemyn',
            ],

            'move_up' => [
                'label' => 'Aukštyn',
            ],

            'collapse' => [
                'label' => 'Suskleisti',
            ],

            'expand' => [
                'label' => 'Išskleisti',
            ],

            'collapse_all' => [
                'label' => 'Suskleisti viską',
            ],

            'expand_all' => [
                'label' => 'Išskleisti viską',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Įkelti failą',

                'modal' => [

                    'heading' => 'Įkelti failą',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Failas',
                                'existing' => 'Pakeisti failą',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt tekstas',
                                'existing' => 'Pakeisti alt tekstą',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Įterpti',
                        ],

                        'save' => [
                            'label' => 'Išsaugoti',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Tinklelis',

                'modal' => [

                    'heading' => 'Tinklelis',
                    'form' => [

                        'preset' => [

                            'label' => 'Išankstiniai nustatymai',

                            'placeholder' => 'Nėra',

                            'options' => [
                                'two' => 'Du',
                                'three' => 'Trys',
                                'four' => 'Keturi',
                                'five' => 'Penki',
                                'two_start_third' => 'Du (Pradėti trečiu)',
                                'two_end_third' => 'Du (Baigti trečiu)',
                                'two_start_fourth' => 'Du (Pradėti ketvirtu)',
                                'two_end_fourth' => 'Du (Baigti ketvirtu)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Stulpeliai',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Nuo pertraukos taško',

                            'options' => [
                                'default' => 'Visi',
                                'sm' => 'Mažas',
                                'md' => 'Vidutinis',
                                'lg' => 'Didelis',
                                'xl' => 'Labai didelis',
                                '2xl' => 'Du labai dideli',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Du asimetriški stulpeliai',
                        ],

                        'start_span' => [
                            'label' => 'Pradžios apimtis',
                        ],

                        'end_span' => [
                            'label' => 'Pabaigos apimtis',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Nuoroda',

                'modal' => [

                    'heading' => 'Nuoroda',
                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Atidaryti naujame skirtuke',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Teksto spalva',

                'modal' => [

                    'heading' => 'Teksto spalva',
                    'form' => [

                        'color' => [
                            'label' => 'Spalva',
                        ],

                        'custom_color' => [
                            'label' => 'Pasirinktinė spalva',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Įkelti failai turi būti šių tipų: :values.',

        'file_attachments_max_size_message' => 'Įkelti failai negali būti didesni nei :max kilobaitų.',

        'no_merge_tag_search_results_message' => 'Nėra sujungimo žymų rezultatų.',
        'mentions' => [
            'no_options_message' => 'Nėra galimų parinkčių.',
            'no_search_results_message' => 'Paieškos rezultatų nėra.',
            'search_prompt' => 'Pradėkite rašyti, kad ieškotumėte...',
            'searching_message' => 'Ieškoma...',
        ],

        'tools' => [
            'align_center' => 'Lygiuoti centre',
            'align_end' => 'Lygiuoti pabaigoje',
            'align_justify' => 'Lygiuoti abipus',
            'align_start' => 'Lygiuoti pradžioje',
            'attach_files' => 'Pridėti failus',
            'blockquote' => 'Citatos blokas',
            'bold' => 'Paryškinta',
            'bullet_list' => 'Ženklų sąrašas',
            'clear_formatting' => 'Išvalyti formatavimą',
            'code' => 'Kodas',
            'code_block' => 'Kodo blokas',
            'custom_blocks' => 'Blokai',
            'details' => 'Detalės',
            'h1' => 'Pavadinimas',
            'h2' => 'Antraštė',
            'h3' => 'Paantraštė',
            'grid' => 'Tinklelis',
            'grid_delete' => 'Ištrinti tinklelį',
            'highlight' => 'Paryškinimas',
            'horizontal_rule' => 'Horizontali linija',
            'italic' => 'Kursyvas',
            'lead' => 'Pagrindinis tekstas',
            'link' => 'Nuoroda',
            'merge_tags' => 'Sujungimo žymos',
            'ordered_list' => 'Sunumeruotas sąrašas',
            'redo' => 'Perdaryti',
            'small' => 'Mažas tekstas',
            'strike' => 'Perbraukta',
            'subscript' => 'Apatinis indeksas',
            'superscript' => 'Viršutinis indeksas',
            'table' => 'Lentelė',
            'table_delete' => 'Ištrinti lentelę',
            'table_add_column_before' => 'Pridėti stulpelį prieš',
            'table_add_column_after' => 'Pridėti stulpelį po',
            'table_delete_column' => 'Ištrinti stulpelį',
            'table_add_row_before' => 'Pridėti eilutę viršuje',
            'table_add_row_after' => 'Pridėti eilutę apačioje',
            'table_delete_row' => 'Ištrinti eilutę',
            'table_merge_cells' => 'Sujungti langelius',
            'table_split_cell' => 'Padalyti langelį',
            'table_toggle_header_row' => 'Perjungti antraštės eilutę',
            'table_toggle_header_cell' => 'Perjungti antraštės langelį',
            'text_color' => 'Teksto spalva',
            'underline' => 'Pabraukta',
            'undo' => 'Anuliuoti',
        ],

        'uploading_file_message' => 'Įkeliamas failas...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Sukurti',

                'modal' => [

                    'heading' => 'Sukurti',

                    'actions' => [

                        'create' => [
                            'label' => 'Sukurti',
                        ],

                        'create_another' => [
                            'label' => 'Sukurti ir sukurti kitą',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Redaguoti',

                'modal' => [

                    'heading' => 'Redaguoti',

                    'actions' => [

                        'save' => [
                            'label' => 'Išsaugoti',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Taip',
            'false' => 'Ne',
        ],

        'loading_message' => 'Kraunasi...',

        'max_items_message' => 'Pažymėti gali būti tik :count .',

        'no_options_message' => 'Nėra paieškos rezultatų.',

        'no_search_results_message' => 'Nėra paieškos rezultatų.',

        'placeholder' => 'Pasirinkite',

        'searching_message' => 'Ieškoma...',

        'search_prompt' => 'Pradėkite rašykite paieškai...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Ištrinti',
            ],

        ],

        'placeholder' => 'Nauja žyma',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopijuoti',
                'message' => 'Nukopijuota',
            ],

            'hide_password' => [
                'label' => 'Slėpti slaptažodį',
            ],

            'show_password' => [
                'label' => 'Rodyti slaptažodį',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Taip',
            'false' => 'Ne',
        ],

    ],

];
