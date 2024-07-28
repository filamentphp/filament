<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Duplikovať',
            ],

            'add' => [

                'label' => 'Pridať do :label',

                'modal' => [

                    'heading' => 'Pridať do :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Pridať',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Pridať medzi bloky',

                'modal' => [

                    'heading' => 'Pridať do :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Pridať',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Odstrániť',
            ],

            'edit' => [

                'label' => 'Upraviť',

                'modal' => [

                    'heading' => 'Upraviť blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Uložiť zmeny',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Presunúť',
            ],

            'move_down' => [
                'label' => 'Presunúť dole',
            ],

            'move_up' => [
                'label' => 'Presunúť hore',
            ],

            'collapse' => [
                'label' => 'Zabaliť',
            ],

            'expand' => [
                'label' => 'Rozbaliť',
            ],

            'collapse_all' => [
                'label' => 'Zbaliť všetko',
            ],

            'expand_all' => [
                'label' => 'Rozbaliť všetko',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Odznačiť všetko',
            ],

            'select_all' => [
                'label' => 'Označiť všetko',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Zrušiť',
                ],

                'drag_crop' => [
                    'label' => 'Ťahaním orezať (crop)',
                ],

                'drag_move' => [
                    'label' => 'Ťahaním posunúť (move)',
                ],

                'flip_horizontal' => [
                    'label' => 'Prevrátiť obrázok vodorovne',
                ],

                'flip_vertical' => [
                    'label' => 'Otočiť obrázok vertikálne',
                ],

                'move_down' => [
                    'label' => 'Presunúť obrázok dole',
                ],

                'move_left' => [
                    'label' => 'Presunúť obrázok doľava',
                ],

                'move_right' => [
                    'label' => 'Presunúť obrázok doprava',
                ],

                'move_up' => [
                    'label' => 'Presunúť obrázok hore',
                ],

                'reset' => [
                    'label' => 'Resetovať',
                ],

                'rotate_left' => [
                    'label' => 'Otočiť obrázok doľava',
                ],

                'rotate_right' => [
                    'label' => 'Otočiť obrázok doprava',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Nastaviť pomer strán na :ratio',
                ],

                'save' => [
                    'label' => 'Uložiť',
                ],

                'zoom_100' => [
                    'label' => 'Priblížiť obrázok na 100%',
                ],

                'zoom_in' => [
                    'label' => 'Priblížiť',
                ],

                'zoom_out' => [
                    'label' => 'Oddiaľiť',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Výška',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotácia',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Šírka',
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

                'label' => 'Pomery strán',

                'no_fixed' => [
                    'label' => 'Voľne',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Úprava SVG obrázkov nie je odporúčaná, pretože zmena veľkosti môže znížiť kvalitu.\n Naozaj chcete pokračovať?',
                    'disabled' => 'Úprava SVG obrázkov nie je dostupná, pretože zmena veľkosti môže znížiť kvalitu.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Pridať riadok',
            ],

            'delete' => [
                'label' => 'Odstrániť riadok',
            ],

            'reorder' => [
                'label' => 'Presunúť riadok',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Kľúč',
            ],

            'value' => [
                'label' => 'Hodnota',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Pripojiť súbory',
            'blockquote' => 'Citát',
            'bold' => 'Tučné písmo',
            'bullet_list' => 'Odrážkový zoznam',
            'code_block' => 'Blok kódu',
            'heading' => 'Nadpis',
            'italic' => 'Kurzíva',
            'link' => 'Odkaz',
            'ordered_list' => 'Číslovaný zoznam',
            'redo' => 'Prerobenie',
            'strike' => 'Prečiarknutie',
            'table' => 'Tabuľka',
            'undo' => 'Späť',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Áno',
            'false' => 'Nie',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Pridať do :label',
            ],

            'add_between' => [
                'label' => 'Vložiť medzi',
            ],

            'delete' => [
                'label' => 'Odstrániť',
            ],

            'clone' => [
                'label' => 'Duplikovať',
            ],

            'reorder' => [
                'label' => 'Presunúť',
            ],

            'move_down' => [
                'label' => 'Presunúť dole',
            ],

            'move_up' => [
                'label' => 'Presunúť hore',
            ],

            'collapse' => [
                'label' => 'Zabaliť',
            ],

            'expand' => [
                'label' => 'Rozbaliť',
            ],

            'collapse_all' => [
                'label' => 'Zabaliť všetko',
            ],

            'expand_all' => [
                'label' => 'Rozbaliť všetko',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Pridať odkaz',
                    'unlink' => 'Zrušiť odkaz',
                ],

                'label' => 'URL adresa',

                'placeholder' => 'Zadajte adresu URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Pripojiť súbory',
            'blockquote' => 'Citát',
            'bold' => 'Tučné písmo',
            'bullet_list' => 'Odrážkový zoznam',
            'code_block' => 'Blok kódu',
            'h1' => 'Názov',
            'h2' => 'Nadpis',
            'h3' => 'Podnadpis',
            'italic' => 'Kurzíva',
            'link' => 'Odkaz',
            'ordered_list' => 'Číslovaný zoznam',
            'redo' => 'Prerobiť',
            'strike' => 'Prečiarknutie',
            'underline' => 'Podčiarknutie',
            'undo' => 'Späť',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Vytvoriť',

                    'actions' => [

                        'create' => [
                            'label' => 'Vytvoriť',
                        ],

                        'create_another' => [
                            'label' => 'Vytvoriť & vytvoriť ďalšie',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Upraviť',

                    'actions' => [

                        'save' => [
                            'label' => 'Uložiť',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Áno',
            'false' => 'Nie',
        ],

        'loading_message' => 'Načítam...',

        'max_items_message' => 'Maximálny počet pre výber je: :count.',

        'no_search_results_message' => 'Žiadne možnosti neodpovedajú vášmu hľadaniu.',

        'placeholder' => 'Vyberte možnosť',

        'searching_message' => 'Hľadám...',

        'search_prompt' => 'Začnite písať...',

    ],

    'tags_input' => [
        'placeholder' => 'Nová značka',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Skryť heslo',
            ],

            'show_password' => [
                'label' => 'Zobraziť heslo',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Áno',
            'false' => 'Nie',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Spať',
            ],

            'next_step' => [
                'label' => 'Ďalej',
            ],

        ],

    ],

];
