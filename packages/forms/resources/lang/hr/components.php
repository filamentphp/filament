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
                'label' => 'Obriši',
            ],

            'edit' => [

                'label' => 'Uredi',

                'modal' => [

                    'heading' => 'Uredi blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Spremi promjene',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Pomakni',
            ],

            'move_down' => [
                'label' => 'Pomakni dolje',
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
                'label' => 'Odznači sve',
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
                    'label' => 'Povlačenje za pomicanje',
                ],

                'flip_horizontal' => [
                    'label' => 'Obrni sliku horizontalno',
                ],

                'flip_vertical' => [
                    'label' => 'Obrni sliku vertikalno',
                ],

                'move_down' => [
                    'label' => 'Pomakni sliku dolje',
                ],

                'move_left' => [
                    'label' => 'Pomakni sliku lijevo',
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
                    'label' => 'Zaokreni sliku ulijevo',
                ],

                'rotate_right' => [
                    'label' => 'Zaokreni sliku udesno',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Postavi omjer širine i visine na :ratio',
                ],

                'save' => [
                    'label' => 'Spremi',
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

                'label' => 'Omjeri širine i visine',

                'no_fixed' => [
                    'label' => 'Slobodno',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Obrađivanje SVG datoteka nije preporučljivo i može dovesti do gubitka kvalitete kada se skalira.\n Jesi li siguran da želiš nastaviti?',
                    'disabled' => 'Onemogućena je obrada SVG datoteka jer može dovesti do gubitka kvalitete kada se skalira.',
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
                'label' => 'Obriši red',
            ],

            'reorder' => [
                'label' => 'Promijeni redoslijed reda',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Ključ',
            ],

            'value' => [
                'label' => 'Vrijednost',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Dodaj datoteke',
            'blockquote' => 'Blok citat',
            'bold' => 'Podebljano',
            'bullet_list' => 'Lista',
            'code_block' => 'Blok kôda',
            'heading' => 'Zaglavlje',
            'italic' => 'Kurziv',
            'link' => 'Poveznica',
            'ordered_list' => 'Numerirana lista',
            'redo' => 'Ponovi',
            'strike' => 'Precrtano',
            'table' => 'Tablica',
            'undo' => 'Poništi',
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
                'label' => 'Obriši',
            ],

            'clone' => [
                'label' => 'Kloniraj',
            ],

            'reorder' => [
                'label' => 'Pomakni',
            ],

            'move_down' => [
                'label' => 'Pomakni dolje',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Poveznica',
                    'unlink' => 'Ukloni poveznicu',
                ],

                'label' => 'URL',

                'placeholder' => 'Unesi URL',

            ],

        ],

        'tools' => [
            'attach_files' => 'Dodaj datoteke',
            'blockquote' => 'Blok citat',
            'bold' => 'Podebljano',
            'bullet_list' => 'Lista',
            'code_block' => 'Blok kôda',
            'h1' => 'Naslov',
            'h2' => 'Zaglavlje',
            'h3' => 'Podnaslov',
            'italic' => 'Kurziv',
            'link' => 'Poveznica',
            'ordered_list' => 'Numerirana lista',
            'redo' => 'Ponovi',
            'strike' => 'Precrtano',
            'underline' => 'Podcrtano',
            'undo' => 'Poništi',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

                'modal' => [

                    'heading' => 'Uredi',

                    'actions' => [

                        'save' => [
                            'label' => 'Spremi',
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

        'max_items_message' => 'Samo :count može biti odabrano.',

        'no_search_results_message' => 'Nema opcija koje odgovaraju tvojem pretraživanju.',

        'placeholder' => 'Odaberi opciju',

        'searching_message' => 'Pretraživanje...',

        'search_prompt' => 'Počni tipkati za pretragu...',

    ],

    'tags_input' => [
        'placeholder' => 'Nova oznaka',
    ],

    'text_input' => [

        'actions' => [

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
