<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kloniraj',
            ],

            'add' => [
                'label' => 'Dodaj na :label',

                'modal' => [

                    'heading' => 'Dodaj na :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Dodaj',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'Umetni između blokova',

                'modal' => [

                    'heading' => 'Dodaj na :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Dodaj',
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
                            'label' => 'Sačuvaj izmene',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Pomakni',
            ],

            'move_down' => [
                'label' => 'Pomakni dole',
            ],

            'move_up' => [
                'label' => 'Pomakni gore',
            ],

            'collapse' => [
                'label' => 'Skupi',
            ],

            'expand' => [
                'label' => 'Proširi',
            ],

            'collapse_all' => [
                'label' => 'Skupi sve',
            ],

            'expand_all' => [
                'label' => 'Proširi sve',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Označi sve',
            ],

            'select_all' => [
                'label' => 'Označi sve',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Odustani',
                ],

                'drag_crop' => [
                    'label' => 'Povlačenje za obrezivanje',
                ],

                'drag_move' => [
                    'label' => 'Povlačenje za premeštanje',
                ],

                'flip_horizontal' => [
                    'label' => 'Obrni sliku horizontalno',
                ],

                'flip_vertical' => [
                    'label' => 'Obrni sliku vertikalno',
                ],

                'move_down' => [
                    'label' => 'Pomakni sliku dole',
                ],

                'move_left' => [
                    'label' => 'Pomakni sliku levo',
                ],

                'move_right' => [
                    'label' => 'Pomakni sliku desno',
                ],

                'move_up' => [
                    'label' => 'Pomakni sliku gore',
                ],

                'reset' => [
                    'label' => 'Poništi',
                ],

                'rotate_left' => [
                    'label' => 'Okreni sliku ulevo',
                ],

                'rotate_right' => [
                    'label' => 'Okreni sliku udesno',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Postavi odnos širine i visine na :ratio',
                ],

                'save' => [
                    'label' => 'Sačuvaj',
                ],

                'zoom_100' => [
                    'label' => 'Uvećaj sliku na 100%',
                ],

                'zoom_in' => [
                    'label' => 'Povećaj',
                ],

                'zoom_out' => [
                    'label' => 'Smanji',
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

                'label' => 'Odnos širine i visine',

                'no_fixed' => [
                    'label' => 'Slobodno',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Obrađivanje SVG datoteka nije preporučljivo i može dovesti do gubitka kvaliteta kada se skalira.\nJeste li sigurni da želite nastaviti?',
                    'disabled' => 'Obrađivanje SVG datoteka je onemogućeno jer može dovesti do gubitka kvaliteta kada se skalira.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Dodaj red',
            ],

            'delete' => [
                'label' => 'Izbriši red',
            ],

            'reorder' => [
                'label' => 'Promeni redosled reda',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Ključ',
            ],

            'value' => [
                'label' => 'Vrednost',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Datoteke moraju biti tipa: :values.',

        'file_attachments_max_size_message' => 'Datoteka ne sme biti veća od :max kb.',

        'tools' => [
            'attach_files' => 'Dodaj datoteke',
            'blockquote' => 'Citat',
            'bold' => 'Podebljano',
            'bullet_list' => 'Lista',
            'code_block' => 'Blok koda',
            'heading' => 'Zaglavlje',
            'italic' => 'Nakošeno',
            'link' => 'Veza',
            'ordered_list' => 'Numerisana lista',
            'redo' => 'Ponovi',
            'strike' => 'Precrtano',
            'table' => 'Tablica',
            'undo' => 'Poništi',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Izbornik',

                'actions' => [

                    'select' => [
                        'label' => 'Izbornik',
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
                'label' => 'Dodaj na :label',
            ],

            'add_between' => [
                'label' => 'Umetni između',
            ],

            'delete' => [
                'label' => 'Izbriši',
            ],

            'clone' => [
                'label' => 'Kloniraj',
            ],

            'reorder' => [
                'label' => 'Pomakni',
            ],

            'move_down' => [
                'label' => 'Pomakni dole',
            ],

            'move_up' => [
                'label' => 'Pomakni gore',
            ],

            'collapse' => [
                'label' => 'Skupi',
            ],

            'expand' => [
                'label' => 'Proširi',
            ],

            'collapse_all' => [
                'label' => 'Skupi sve',
            ],

            'expand_all' => [
                'label' => 'Proširi sve',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Pošalji datoteku',

                'modal' => [

                    'heading' => 'Slanje datoteke',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Datoteka',
                                'existing' => 'Zameni datoteku',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alternativni tekst',
                                'existing' => 'Izmena alternativnog teksta',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Umetni',
                        ],

                        'save' => [
                            'label' => 'Sačuvaj',
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

                            'label' => 'Predefinisano',

                            'placeholder' => 'Ništa',

                            'options' => [
                                'two' => 'Jedan',
                                'three' => 'Tri',
                                'four' => 'Četiri',
                                'five' => 'Pet',
                                'two_start_third' => 'Dva (početak trećina)',
                                'two_end_third' => 'Dva (kraj trećina)',
                                'two_start_fourth' => 'Dva (početak četvrtina)',
                                'two_end_fourth' => 'Dva (kraj četvrtina)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Kolone',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Za veličine ekrana',

                            'options' => [
                                'default' => 'Sve',
                                'sm' => 'Male',
                                'md' => 'Srednje',
                                'lg' => 'Velike',
                                'xl' => 'Ekstra velike',
                                '2xl' => '2x ekstra velike',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Dve asimetrične kolone',
                        ],

                        'start_span' => [
                            'label' => 'Početak raspona',
                        ],

                        'end_span' => [
                            'label' => 'Kraj raspona',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Izmeni',

                'modal' => [

                    'heading' => 'Veza',

                    'form' => [

                        'url' => [
                            'label' => 'Internet adresa',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Otvori u novom tabu',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Boja teksta',

                'modal' => [

                    'heading' => 'Boja teksta',

                    'form' => [

                        'color' => [
                            'label' => 'Boja',
                        ],

                        'custom_color' => [
                            'label' => 'Proizvoljna boja',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Datoteke moraju biti tipa: :values.',

        'file_attachments_max_size_message' => 'Datoteka ne sme biti veća od :max kb.',

        'no_merge_tag_search_results_message' => 'Bez rezultata spojenih oznaka.',

        'tools' => [
            'align_center' => 'Centrirano',
            'align_end' => 'Desno poravnanje',
            'align_justify' => 'Obostrano poravnanje',
            'align_start' => 'Levo poravnanje',
            'attach_files' => 'Dodaj datoteke',
            'blockquote' => 'Citat',
            'bold' => 'Podebljano',
            'bullet_list' => 'Lista',
            'clear_formatting' => 'Ukloni formatiranje',
            'code' => 'Kod',
            'code_block' => 'Blok koda',
            'custom_blocks' => 'Prilagođen blok',
            'details' => 'Detalji',
            'h1' => 'Naslov',
            'h2' => 'Zaglavlje',
            'h3' => 'Podnaslov',
            'grid' => 'Mreža',
            'grid_delete' => 'Izbriši mrežu',
            'highlight' => 'Označi',
            'horizontal_rule' => 'Horizontalna linija',
            'italic' => 'Nakošeno',
            'lead' => 'Uvodni tekst',
            'link' => 'Veza',
            'merge_tags' => 'Spojene oznake',
            'ordered_list' => 'Numerisana lista',
            'redo' => 'Ponovi',
            'small' => 'Mali tekst',
            'strike' => 'Precrtano',
            'subscript' => 'Indeks',
            'superscript' => 'Eksponent',
            'table' => 'Tabela',
            'table_delete' => 'Izbriši tabelu',
            'table_add_column_before' => 'Dodaj kolonu ispred',
            'table_add_column_after' => 'Dodaj kolonu posle',
            'table_delete_column' => 'Izbriši kolonu',
            'table_add_row_before' => 'Dodaj red iznad',
            'table_add_row_after' => 'Dodaj red ispod',
            'table_delete_row' => 'Izbriši red',
            'table_merge_cells' => 'Spoji ćelije',
            'table_split_cell' => 'Razdeli ćeliju',
            'table_toggle_header_row' => 'Prikaži/skrij red zaglavlja',
            'text_color' => 'Boja teksta',
            'underline' => 'Podvučeno',
            'undo' => 'Poništi',
        ],

        'uploading_file_message' => 'Otpremanje datoteke...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Napravi',

                'modal' => [

                    'heading' => 'Napravi',

                    'actions' => [

                        'create' => [
                            'label' => 'Napravi',
                        ],

                        'create_another' => [
                            'label' => 'Napravi i dodaj još jedan',
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
                            'label' => 'Sačuvaj',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Da',
            'false' => 'Ne',
        ],

        'loading_message' => 'Učitavanje...',

        'max_items_message' => 'Samo :count može biti izabrano.',

        'no_search_results_message' => 'Nema opcija koje odgovaraju pretrazi.',

        'placeholder' => 'Izaberi opciju',

        'searching_message' => 'Pretraživanje...',

        'search_prompt' => 'Počni kucati za pretragu...',

    ],

    'tags_input' => [
        'placeholder' => 'Nova oznaka',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopiraj',
                'message' => 'Kopirano',
            ],

            'hide_password' => [
                'label' => 'Sakrij lozinku',
            ],

            'show_password' => [
                'label' => 'Prikaži lozinku',
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
