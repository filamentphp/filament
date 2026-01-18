<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kloniraj',
            ],

            'add' => [

                'label' => 'Dodadi na :label',

                'modal' => [

                    'heading' => 'Dodadi na :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Dodadi',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Vmetni pomеѓu blokovi',

                'modal' => [

                    'heading' => 'Dodadi na :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Dodadi',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Izbriši',
            ],

            'edit' => [

                'label' => 'Uredi',

                'modal' => [

                    'heading' => 'Uredi blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Začuvaj promeni',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Premesti',
            ],

            'move_down' => [
                'label' => 'Premesti nadolu',
            ],

            'move_up' => [
                'label' => 'Premesti nagore',
            ],

            'collapse' => [
                'label' => 'Soberi',
            ],

            'expand' => [
                'label' => 'Proširi',
            ],

            'collapse_all' => [
                'label' => 'Soberi site',
            ],

            'expand_all' => [
                'label' => 'Proširi site',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Odberi site',
            ],

            'select_all' => [
                'label' => 'Izberi site',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Otkaži',
                ],

                'drag_crop' => [
                    'label' => 'Vleči režim "iseči"',
                ],

                'drag_move' => [
                    'label' => 'Vleči režim "premesti"',
                ],

                'flip_horizontal' => [
                    'label' => 'Prevrti slika horizontalno',
                ],

                'flip_vertical' => [
                    'label' => 'Prevrti slika vertikalno',
                ],

                'move_down' => [
                    'label' => 'Premesti slika nadolu',
                ],

                'move_left' => [
                    'label' => 'Premesti slika nalevo',
                ],

                'move_right' => [
                    'label' => 'Premesti slika nadesno',
                ],

                'move_up' => [
                    'label' => 'Premesti slika nagore',
                ],

                'reset' => [
                    'label' => 'Resetiraj',
                ],

                'rotate_left' => [
                    'label' => 'Rotiraj slika nalevo',
                ],

                'rotate_right' => [
                    'label' => 'Rotiraj slika nadesno',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Postavi soodnos na :ratio',
                ],

                'save' => [
                    'label' => 'Začuvaj',
                ],

                'zoom_100' => [
                    'label' => 'Zumiraj slika na 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zumiraj vnatre',
                ],

                'zoom_out' => [
                    'label' => 'Zumiraj nadvor',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Visina',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotacija',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Širina',
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

                'label' => 'Soodnosi',

                'no_fixed' => [
                    'label' => 'Slobodno',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Ureduvanjeto na SVG datoteki ne se preporаčuva bideјќi može da rezultira so zguba na kvalitet pri skaliranje.\n Dali ste sigurni deka sakate da prodolžite?',
                    'disabled' => 'Ureduvanjeto na SVG datoteki e onevozmoženo bideјќi može da rezultira so zguba na kvalitet pri skaliranje.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Dodadi red',
            ],

            'delete' => [
                'label' => 'Izbriši red',
            ],

            'reorder' => [
                'label' => 'Preuredi red',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Kluč',
            ],

            'value' => [
                'label' => 'Vrednost',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Prilkačenite datoteki mora da bidat od tip: :values.',

        'file_attachments_max_size_message' => 'Prilkačenite datoteki ne smeat da bidat pogolemi od :max kilobajti.',

        'tools' => [
            'attach_files' => 'Prilkači datoteki',
            'blockquote' => 'Blok citat',
            'bold' => 'Zadebeleno',
            'bullet_list' => 'Spisok so točki',
            'code_block' => 'Blok na kod',
            'heading' => 'Naslov',
            'italic' => 'Kurziv',
            'link' => 'Link',
            'ordered_list' => 'Numeriran spisok',
            'redo' => 'Povtori',
            'strike' => 'Precrаtano',
            'table' => 'Tаbela',
            'undo' => 'Otkaži',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Izberi',

                'actions' => [

                    'select' => [
                        'label' => 'Izberi',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Da',
            'false' => 'Ne',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Dodadi na :label',
            ],

            'add_between' => [
                'label' => 'Vmetni pomеѓu',
            ],

            'delete' => [
                'label' => 'Izbriši',
            ],

            'clone' => [
                'label' => 'Kloniraj',
            ],

            'reorder' => [
                'label' => 'Premesti',
            ],

            'move_down' => [
                'label' => 'Premesti nadolu',
            ],

            'move_up' => [
                'label' => 'Premesti nagore',
            ],

            'collapse' => [
                'label' => 'Soberi',
            ],

            'expand' => [
                'label' => 'Proširi',
            ],

            'collapse_all' => [
                'label' => 'Soberi site',
            ],

            'expand_all' => [
                'label' => 'Proširi site',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Prilkači datoteka',

                'modal' => [

                    'heading' => 'Prilkači datoteka',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Datoteka',
                                'existing' => 'Zameni datoteka',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt tekst',
                                'existing' => 'Smeni alt tekst',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Vmetni',
                        ],

                        'save' => [
                            'label' => 'Začuvaj',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Mreža',

                'modal' => [

                    'heading' => 'Mreža',

                    'form' => [

                        'preset' => [

                            'label' => 'Predodredeno',

                            'placeholder' => 'Nema',

                            'options' => [
                                'two' => 'Dve',
                                'three' => 'Tri',
                                'four' => 'Četiri',
                                'five' => 'Pet',
                                'two_start_third' => 'Dve (Započni Treto)',
                                'two_end_third' => 'Dve (Završi Treto)',
                                'two_start_fourth' => 'Dve (Započni Četvrto)',
                                'two_end_fourth' => 'Dve (Završi Četvrto)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Koloni',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Od točka na prekin',

                            'options' => [
                                'default' => 'Site',
                                'sm' => 'Mal',
                                'md' => 'Sreden',
                                'lg' => 'Golem',
                                'xl' => 'Mnogu golem',
                                '2xl' => 'Dve mnogu golemi',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Dve asimetrični koloni',
                        ],

                        'start_span' => [
                            'label' => 'Započni opseg',
                        ],

                        'end_span' => [
                            'label' => 'Završi opseg',
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
                            'label' => 'Otvori vo nov tab',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Boja na tekst',

                'modal' => [

                    'heading' => 'Boja na tekst',

                    'form' => [

                        'color' => [
                            'label' => 'Boja',
                        ],

                        'custom_color' => [
                            'label' => 'Prilagodena boja',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Prilkačenite datoteki mora da bidat od tip: :values.',

        'file_attachments_max_size_message' => 'Prilkačenite datoteki ne smeat da bidat pogolemi od :max kilobajti.',

        'no_merge_tag_search_results_message' => 'Nema rezultati za spojuvanje na tagovi.',

        'mentions' => [
            'no_options_message' => 'Nema dostapni opcii.',
            'no_search_results_message' => 'Nema rezultati koi se sovpаѓaat so vašeto prebaruvanje.',
            'search_prompt' => 'Započnete da pišuvate za prebaruvanje...',
            'searching_message' => 'Se prebaruva...',
        ],

        'tools' => [
            'align_center' => 'Porаmni po centar',
            'align_end' => 'Porаmni na kraj',
            'align_justify' => 'Porаmni obostrаno',
            'align_start' => 'Porаmni na počеtok',
            'attach_files' => 'Prilkači datoteki',
            'blockquote' => 'Blok citat',
            'bold' => 'Zadebeleno',
            'bullet_list' => 'Spisok so točki',
            'clear_formatting' => 'Isčisti formatiranje',
            'code' => 'Kod',
            'code_block' => 'Blok na kod',
            'custom_blocks' => 'Blokovi',
            'details' => 'Detali',
            'h1' => 'Naslov',
            'h2' => 'Zаglаvije',
            'h3' => 'Podzаglаvije',
            'grid' => 'Mreža',
            'grid_delete' => 'Izbriši mreža',
            'highlight' => 'Istаkni',
            'horizontal_rule' => 'Horizontalnа liniја',
            'italic' => 'Kurziv',
            'lead' => 'Vodečki tekst',
            'link' => 'Link',
            'merge_tags' => 'Spoj tagovi',
            'ordered_list' => 'Numeriran spisok',
            'redo' => 'Povtori',
            'small' => 'Mal tekst',
            'strike' => 'Precrаtano',
            'subscript' => 'Podznаk',
            'superscript' => 'Nаdznаk',
            'table' => 'Tаbela',
            'table_delete' => 'Izbriši tаbela',
            'table_add_column_before' => 'Dodadi kolona pred',
            'table_add_column_after' => 'Dodadi kolona posle',
            'table_delete_column' => 'Izbriši kolona',
            'table_add_row_before' => 'Dodadi red pogore',
            'table_add_row_after' => 'Dodadi red podole',
            'table_delete_row' => 'Izbriši red',
            'table_merge_cells' => 'Spoj kjelii',
            'table_split_cell' => 'Podeli kjeliја',
            'table_toggle_header_row' => 'Prevkluči zаglаven red',
            'table_toggle_header_cell' => 'Prevkluči zаglаvnа kjeliја',
            'text_color' => 'Boja na tekst',
            'underline' => 'Podvleno',
            'undo' => 'Otkaži',
        ],

        'uploading_file_message' => 'Se prilkačuva datoteka...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Kreiraj',

                'modal' => [

                    'heading' => 'Kreiraj',

                    'actions' => [

                        'create' => [
                            'label' => 'Kreiraj',
                        ],

                        'create_another' => [
                            'label' => 'Kreiraj i kreiraj drug',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Uredi',

                'modal' => [

                    'heading' => 'Uredi',

                    'actions' => [

                        'save' => [
                            'label' => 'Začuvaj',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Da',
            'false' => 'Ne',
        ],

        'loading_message' => 'Se včituva...',

        'max_items_message' => 'Samo :count možat da bidat izabrani.',

        'no_options_message' => 'Nema dostapni opcii.',

        'no_search_results_message' => 'Nema opcii koi se sovpаѓaat so vašeto prebaruvanje.',

        'placeholder' => 'Izberi opcija',

        'searching_message' => 'Se prebaruva...',

        'search_prompt' => 'Započnete da pišuvate za prebaruvanje...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Izbriši',
            ],

        ],

        'placeholder' => 'Nov tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopiraj',
                'message' => 'Kopirano',
            ],

            'hide_password' => [
                'label' => 'Sokrij lozinka',
            ],

            'show_password' => [
                'label' => 'Prikaži lozinka',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Da',
            'false' => 'Ne',
        ],

    ],

];
