<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klooni',
            ],

            'add' => [
                'label' => 'Lisa :label',
                'modal' => [
                    'heading' => 'Lisa :label',
                    'actions' => [
                        'add' => [
                            'label' => 'Lisa',
                        ],
                    ],
                ],
            ],

            'add_between' => [
                'label' => 'Lisa plokkide vahele',
                'modal' => [
                    'heading' => 'Lisa :label',
                    'actions' => [
                        'add' => [
                            'label' => 'Lisa',
                        ],
                    ],
                ],
            ],

            'delete' => [
                'label' => 'Kustuta',
            ],

            'edit' => [
                'label' => 'Muuda',
                'modal' => [
                    'heading' => 'Muuda plokki',
                    'actions' => [
                        'save' => [
                            'label' => 'Salvesta muudatused',
                        ],
                    ],
                ],
            ],

            'reorder' => [
                'label' => 'Liiguta',
            ],

            'move_down' => [
                'label' => 'Liiguta alla',
            ],

            'move_up' => [
                'label' => 'Liiguta üles',
            ],

            'collapse' => [
                'label' => 'Ahenda',
            ],

            'expand' => [
                'label' => 'Laienda',
            ],

            'collapse_all' => [
                'label' => 'Ahenda kõik',
            ],

            'expand_all' => [
                'label' => 'Laienda kõik',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Tühista kõik valikud',
            ],

            'select_all' => [
                'label' => 'Vali kõik',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Tühista',
                ],

                'drag_crop' => [
                    'label' => 'Lohistamisrežiim "kärpimine"',
                ],

                'drag_move' => [
                    'label' => 'Lohistamisrežiim "liigutamine"',
                ],

                'flip_horizontal' => [
                    'label' => 'Pööra pilt horisontaalselt',
                ],

                'flip_vertical' => [
                    'label' => 'Pööra pilt vertikaalselt',
                ],

                'move_down' => [
                    'label' => 'Liiguta pilt alla',
                ],

                'move_left' => [
                    'label' => 'Liiguta pilt vasakule',
                ],

                'move_right' => [
                    'label' => 'Liiguta pilt paremale',
                ],

                'move_up' => [
                    'label' => 'Liiguta pilt üles',
                ],

                'reset' => [
                    'label' => 'Lähtesta',
                ],

                'rotate_left' => [
                    'label' => 'Pööra pilt vasakule',
                ],

                'rotate_right' => [
                    'label' => 'Pööra pilt paremale',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Määra kuvasuhe :ratio',
                ],

                'save' => [
                    'label' => 'Salvesta',
                ],

                'zoom_100' => [
                    'label' => 'Suurenda pilt 100%',
                ],

                'zoom_in' => [
                    'label' => 'Suurenda',
                ],

                'zoom_out' => [
                    'label' => 'Vähenda',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Kõrgus',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Pööramine',
                    'unit' => 'kraadi',
                ],

                'width' => [
                    'label' => 'Laius',
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

                'label' => 'Kuvasuhted',

                'no_fixed' => [
                    'label' => 'Vaba',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG-failide muutmine pole soovitatav, kuna see võib põhjustada kvaliteedi kadu skaleerimisel.\nKas soovite kindlasti jätkata?',
                    'disabled' => 'SVG-failide muutmine on keelatud, kuna see võib põhjustada kvaliteedi kadu skaleerimisel.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Lisa rida',
            ],

            'delete' => [
                'label' => 'Kustuta rida',
            ],

            'reorder' => [
                'label' => 'Muuda rea järjekorda',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Võti',
            ],

            'value' => [
                'label' => 'Väärtus',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Lisa failid',
            'blockquote' => 'Tsitaat',
            'bold' => 'Paks',
            'bullet_list' => 'Täpploend',
            'code_block' => 'Koodiplokk',
            'heading' => 'Pealkiri',
            'italic' => 'Kaldkiri',
            'link' => 'Link',
            'ordered_list' => 'Numberloend',
            'redo' => 'Tee uuesti',
            'strike' => 'Läbijoonitud',
            'table' => 'Tabel',
            'undo' => 'Võta tagasi',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Jah',
            'false' => 'Ei',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Lisa :label',
            ],

            'add_between' => [
                'label' => 'Lisa vahele',
            ],

            'delete' => [
                'label' => 'Kustuta',
            ],

            'clone' => [
                'label' => 'Klooni',
            ],

            'reorder' => [
                'label' => 'Liiguta',
            ],

            'move_down' => [
                'label' => 'Liiguta alla',
            ],

            'move_up' => [
                'label' => 'Liiguta üles',
            ],

            'collapse' => [
                'label' => 'Ahenda',
            ],

            'expand' => [
                'label' => 'Laienda',
            ],

            'collapse_all' => [
                'label' => 'Ahenda kõik',
            ],

            'expand_all' => [
                'label' => 'Laienda kõik',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Lisa link',
                    'unlink' => 'Eemalda link',
                ],

                'label' => 'URL',

                'placeholder' => 'Sisesta URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Lisa failid',
            'blockquote' => 'Tsitaat',
            'bold' => 'Paks',
            'bullet_list' => 'Täpploend',
            'code_block' => 'Koodiplokk',
            'h1' => 'Pealkiri',
            'h2' => 'Alampealkiri',
            'h3' => 'Alam-alampealkiri',
            'italic' => 'Kaldkiri',
            'link' => 'Link',
            'ordered_list' => 'Numberloend',
            'redo' => 'Tee uuesti',
            'strike' => 'Läbijoonitud',
            'underline' => 'Allajoonitud',
            'undo' => 'Võta tagasi',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Loo uus',

                'modal' => [

                    'heading' => 'Loo uus',

                    'actions' => [

                        'create' => [
                            'label' => 'Loo',
                        ],

                        'create_another' => [
                            'label' => 'Loo ja loo järgmine',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Muuda',

                'modal' => [

                    'heading' => 'Muuda',

                    'actions' => [

                        'save' => [
                            'label' => 'Salvesta',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Jah',
            'false' => 'Ei',
        ],

        'loading_message' => 'Laadimine...',

        'max_items_message' => 'Saab valida ainult :count.',

        'no_search_results_message' => 'Ühtegi valikut ei leitud.',

        'placeholder' => 'Vali variant',

        'searching_message' => 'Otsimine...',

        'search_prompt' => 'Alusta kirjutamist, et otsida...',

    ],

    'tags_input' => [
        'placeholder' => 'Uus silt',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Peida parool',
            ],

            'show_password' => [
                'label' => 'Näita parooli',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Jah',
            'false' => 'Ei',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Tagasi',
            ],

            'next_step' => [
                'label' => 'Edasi',
            ],

        ],

    ],

];
