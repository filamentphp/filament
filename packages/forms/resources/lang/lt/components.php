<?php

return [

    'builder' => [

        'reordering' => [
            'item' => 'Blokas',
            'position' => ':label, pozicija :position iš :count',
            'moved' => ':label perkeltas į poziciją :position iš :count',
        ],

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

        'block_picker' => [

            'no_search_results_message' => 'Nėra blokų, atitinkančių paiešką.',

            'search_prompt' => 'Ieškoti blokų',

        ],

    ],

    'checkbox_list' => [

        'required_description' => 'Pasirinkite bent vieną parinktį.',

        'actions' => [

            'deselect_all' => [
                'label' => 'Atžymėti viską',
            ],

            'select_all' => [
                'label' => 'Pasirinkti viską',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'Spalvos parinkiklis',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => 'Mėnuo',
        ],

        'year_input' => [
            'label' => 'Metai',
        ],

        'hour_input' => [
            'label' => 'Valanda',
        ],

        'minute_input' => [
            'label' => 'Minutė',
        ],

        'second_input' => [
            'label' => 'Sekundė',
        ],

    ],

    'file_upload' => [

        'placeholder' => 'Failai neįkelti',

        'actions' => [

            'download' => [
                'label' => 'Atsisiųsti',
            ],

            'open' => [
                'label' => 'Atidaryti naujame skirtuke',
            ],

        ],

        'editor' => [

            'label' => 'Vaizdo redaktorius',

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
                    'label' => 'Pasukti nuotrauką į dešinę',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Nustatyti kraštinių santykį :ratio',
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

    'js_field' => [
        'failed_to_load' => 'Šio lauko įkelti nepavyko. Įkelkite puslapį iš naujo ir bandykite dar kartą.',
    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Pridėti eilutę',
            ],

            'delete' => [
                'label' => 'Ištrinti eilutę',
            ],

            'move_down' => [
                'label' => 'Perkelti žemyn',
            ],

            'move_up' => [
                'label' => 'Perkelti aukštyn',
            ],

            'reorder' => [
                'label' => 'Pertvarkyti eilutę',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'Veiksmai',
            ],

            'reorder' => [
                'label' => 'Pertvarkyti',
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

        'reordering' => [
            'row' => 'Eilutė',
            'position' => ':label, pozicija :position iš :count',
            'moved' => ':label perkeltas į poziciją :position iš :count',
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

                'label' => 'Pasirinkti',

                'actions' => [

                    'select' => [
                        'label' => 'Pasirinkti',
                    ],

                ],

            ],

        ],

    ],

    'repeater' => [

        'reordering' => [
            'item' => 'Elementas',
            'position' => ':label, pozicija :position iš :count',
            'moved' => ':label perkeltas į poziciją :position iš :count',
        ],

        'columns' => [

            'actions' => [
                'label' => 'Veiksmai',
            ],

            'reorder' => [
                'label' => 'Pertvarkyti',
            ],

        ],

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

            'close_panel' => [
                'label' => 'Uždaryti skydelį',
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

                            'options' => [
                                'slate' => 'Šiferio',
                                'gray' => 'Pilka',
                                'zinc' => 'Cinko',
                                'neutral' => 'Neutrali',
                                'stone' => 'Akmens',
                                'mauve' => 'Alyvinė',
                                'olive' => 'Alyvuogių',
                                'mist' => 'Miglos',
                                'taupe' => 'Pilkai ruda',
                                'red' => 'Raudona',
                                'orange' => 'Oranžinė',
                                'amber' => 'Gintarinė',
                                'yellow' => 'Geltona',
                                'lime' => 'Laimo',
                                'green' => 'Žalia',
                                'emerald' => 'Smaragdinė',
                                'teal' => 'Žalsvai mėlyna',
                                'cyan' => 'Žydra',
                                'sky' => 'Dangaus mėlyna',
                                'blue' => 'Mėlyna',
                                'indigo' => 'Indigo',
                                'violet' => 'Violetinė',
                                'purple' => 'Purpurinė',
                                'fuchsia' => 'Fuksija',
                                'pink' => 'Rausva',
                                'rose' => 'Rožinė',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Pasirinktinė spalva',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => 'Ištrinti bloką',
                ],

                'edit' => [
                    'label' => 'Redaguoti bloką',
                ],

            ],

            'no_search_results_message' => 'Nėra blokų, atitinkančių paiešką.',

            'search_label' => 'Ieškoti blokų',

            'search_prompt' => 'Ieškoti blokų',

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

        'toolbar' => [
            'label' => 'Redaktoriaus įrankių juosta',
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
            'h4' => 'Antraštė 4',
            'h5' => 'Antraštė 5',
            'h6' => 'Antraštė 6',
            'grid' => 'Tinklelis',
            'grid_delete' => 'Ištrinti tinklelį',
            'highlight' => 'Paryškinimas',
            'horizontal_rule' => 'Horizontali linija',
            'italic' => 'Kursyvas',
            'lead' => 'Pagrindinis tekstas',
            'link' => 'Nuoroda',
            'merge_tags' => 'Sujungimo žymos',
            'ordered_list' => 'Sunumeruotas sąrašas',
            'paragraph' => 'Pastraipa',
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

            'clear' => [
                'label' => 'Išvalyti pasirinkimą',
            ],

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

            'remove_option' => [
                'label' => 'Pašalinti :label',
            ],

        ],

        'boolean' => [
            'true' => 'Taip',
            'false' => 'Ne',
        ],

        'error_message' => 'Parinkčių įkelti nepavyko. Bandykite dar kartą.',

        'loading_message' => 'Įkeliama...',

        'max_items_message' => 'Galima pasirinkti tik :count.',

        'no_options_message' => 'Nėra galimų parinkčių.',

        'no_search_results_message' => 'Nėra paieškos rezultatų.',

        'placeholder' => 'Pasirinkite parinktį',

        'required_description' => 'Pasirinkite parinktį.',

        'searching_message' => 'Ieškoma...',

        'search_label' => 'Paieška',

        'search_prompt' => 'Norėdami ieškoti pradėkite rašyti...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Ištrinti',
            ],

        ],

        'placeholder' => 'Nauja žyma',

        'tag_added' => 'Pridėta: :tag',

        'tag_removed' => 'Pašalinta: :tag',

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

        'required_description' => 'Pasirinkite bent vieną parinktį.',

        'boolean' => [
            'true' => 'Taip',
            'false' => 'Ne',
        ],

    ],

];
