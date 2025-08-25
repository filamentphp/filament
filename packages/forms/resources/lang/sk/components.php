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

        'tools' => [
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

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Vybrať',

                'actions' => [

                    'select' => [
                        'label' => 'Vybrať',
                    ],

                ],

            ],

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

        'actions' => [

            'attach_files' => [

                'label' => 'Nahrať súbor',

                'modal' => [

                    'heading' => 'Nahrať súbor',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Súbor',
                                'existing' => 'Nahradiť súbor',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alternatívny text',
                                'existing' => 'Zmeniť alternatívny text',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Vložiť',
                        ],

                        'save' => [
                            'label' => 'Uložiť',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Upraviť',

                'modal' => [

                    'heading' => 'Odkaz',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Otvoriť v novej záložke',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Nenašli sa žiadne výsledky pre značky zlúčenia.',

        'tools' => [
            'align_center' => 'Zarovnať na stred',
            'align_end' => 'Zarovnať vpravo',
            'align_justify' => 'Zarovnať do bloku',
            'align_start' => 'Zarovnať vľavo',
            'attach_files' => 'Pripojiť súbory',
            'blockquote' => 'Citát',
            'bold' => 'Tučné písmo',
            'bullet_list' => 'Odrážkový zoznam',
            'clear_formatting' => 'Vymazať formátovanie',
            'code' => 'Kód',
            'code_block' => 'Blok kódu',
            'custom_blocks' => 'Bloky',
            'details' => 'Detaily',
            'h1' => 'Názov',
            'h2' => 'Nadpis',
            'h3' => 'Podnadpis',
            'highlight' => 'Zvýrazniť',
            'horizontal_rule' => 'Vodorovná čiara',
            'italic' => 'Kurzíva',
            'lead' => 'Úvodný text',
            'link' => 'Odkaz',
            'merge_tags' => 'Zlúčiť značky',
            'ordered_list' => 'Číslovaný zoznam',
            'redo' => 'Prerobiť',
            'small' => 'Malý text',
            'strike' => 'Prečiarknutie',
            'subscript' => 'Dolný index',
            'superscript' => 'Horný index',
            'table' => 'Tabuľka',
            'table_delete' => 'Odstrániť tabuľku',
            'table_add_column_before' => 'Pridať stĺpec pred',
            'table_add_column_after' => 'Pridať stĺpec za',
            'table_delete_column' => 'Odstrániť stĺpec',
            'table_add_row_before' => 'Pridať riadok nad',
            'table_add_row_after' => 'Pridať riadok pod',
            'table_delete_row' => 'Odstrániť riadok',
            'table_merge_cells' => 'Zlúčiť bunky',
            'table_split_cell' => 'Rozdeliť bunku',
            'table_toggle_header_row' => 'Prepnúť riadok hlavičky',
            'underline' => 'Podčiarknutie',
            'undo' => 'Späť',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Vytvoriť',

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

                'label' => 'Upraviť',

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

            'copy' => [
                'label' => 'Kopírovať',
                'message' => 'Skopírované',
            ],

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

];
