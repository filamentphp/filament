<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kloon',
            ],

            'add' => [

                'label' => 'Voeg by :label',

                'modal' => [

                    'heading' => 'Voeg by :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Voeg by',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Plaas tussen blokke',

                'modal' => [

                    'heading' => 'Voeg by :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Voeg by',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Vee uit',
            ],

            'edit' => [

                'label' => 'Wysig',

                'modal' => [

                    'heading' => 'Wysig blok',

                    'actions' => [

                        'save' => [
                            'label' => 'Stoor veranderinge',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Beweeg',
            ],

            'move_down' => [
                'label' => 'Beweeg af',
            ],

            'move_up' => [
                'label' => 'Beweeg op',
            ],

            'collapse' => [
                'label' => 'Ineenstort',
            ],

            'expand' => [
                'label' => 'Brei uit',
            ],

            'collapse_all' => [
                'label' => 'Vou alles in',
            ],

            'expand_all' => [
                'label' => 'Brei alles uit',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Ontkies almal',
            ],

            'select_all' => [
                'label' => 'Kies alles',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Kanselleer',
                ],

                'drag_crop' => [
                    'label' => 'Sleepmodus "crop"',
                ],

                'drag_move' => [
                    'label' => 'Sleepmodus "skuif"',
                ],

                'flip_horizontal' => [
                    'label' => 'Draai prent horisontaal om',
                ],

                'flip_vertical' => [
                    'label' => 'Draai prent vertikaal om',
                ],

                'move_down' => [
                    'label' => 'Skuif prent af',
                ],

                'move_left' => [
                    'label' => 'Skuif prent na links',
                ],

                'move_right' => [
                    'label' => 'Skuif prent na regs',
                ],

                'move_up' => [
                    'label' => 'Skuif prent op',
                ],

                'reset' => [
                    'label' => 'Stel terug',
                ],

                'rotate_left' => [
                    'label' => 'Draai prent na links',
                ],

                'rotate_right' => [
                    'label' => 'Draai prent na regs',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Stel aspekverhouding na :ratio',
                ],

                'save' => [
                    'label' => 'Save',
                ],

                'zoom_100' => [
                    'label' => 'Zoem prent tot 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoem in',
                ],

                'zoom_out' => [
                    'label' => 'Zoem uit',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Hoogte',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotasie',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Breedte',
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

                'label' => 'Aspekverhoudings',

                'no_fixed' => [
                    'label' => 'Gratis',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Dit word nie aanbeveel om SVG-lêers te wysig nie, aangesien dit kwaliteitverlies kan veroorsaak wanneer skaal word.\n Is jy seker jy wil voortgaan?',
                    'disabled' => 'Die wysiging van SVG-lêers is gedeaktiveer aangesien dit kwaliteitverlies kan veroorsaak wanneer skaal word.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Voeg ry by',
            ],

            'delete' => [
                'label' => 'Vee ry uit',
            ],

            'reorder' => [
                'label' => 'Herrangskik ry',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Sleutel',
            ],

            'value' => [
                'label' => 'Waarde',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Heg lêers aan',
            'blockquote' => 'Blokaanhaling',
            'bold' => 'Vet',
            'bullet_list' => 'Bullet lys',
            'code_block' => 'Kode blok',
            'heading' => 'Opskrif',
            'italic' => 'Kursief',
            'link' => 'Skakel',
            'ordered_list' => 'Genommerde lys',
            'redo' => 'Herhaal',
            'strike' => 'Deurstreep',
            'table' => 'Tafel',
            'undo' => 'Ontdoen',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nee',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Voeg by :label',
            ],

            'add_between' => [
                'label' => 'Plaas tussen',
            ],

            'delete' => [
                'label' => 'Vee uit',
            ],

            'clone' => [
                'label' => 'Kloon',
            ],

            'reorder' => [
                'label' => 'Beweeg',
            ],

            'move_down' => [
                'label' => 'Beweeg af',
            ],

            'move_up' => [
                'label' => 'Beweeg op',
            ],

            'collapse' => [
                'label' => 'Ineenstort',
            ],

            'expand' => [
                'label' => 'Brei uit',
            ],

            'collapse_all' => [
                'label' => 'Vou alles in',
            ],

            'expand_all' => [
                'label' => 'Brei alles uit',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Skakel',
                    'unlink' => 'Ontkoppel',
                ],

                'label' => 'URL',

                'placeholder' => 'Voer \'n URL in',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Heg lêers aan',
            'blockquote' => 'Blokaanhaling',
            'bold' => 'Vet',
            'bullet_list' => 'Bullet lys',
            'code_block' => 'Kode blok',
            'h1' => 'Titel',
            'h2' => 'Opskrif',
            'h3' => 'Subopskrif',
            'italic' => 'Kursief',
            'link' => 'Skakel',
            'ordered_list' => 'Genommerde lys',
            'redo' => 'Herhaal',
            'strike' => 'Deurstreep',
            'underline' => 'Onderstreep',
            'undo' => 'Ontdoen',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Skep',

                'modal' => [

                    'heading' => 'Skep',

                    'actions' => [

                        'create' => [
                            'label' => 'Skep',
                        ],

                        'create_another' => [
                            'label' => 'Skep en skep nog een',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Wysig',

                'modal' => [

                    'heading' => 'Wysig',

                    'actions' => [

                        'save' => [
                            'label' => 'Stoor',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nee',
        ],

        'loading_message' => 'Laai tans...',

        'max_items_message' => 'Slegs :count kan gekies word.',

        'no_search_results_message' => 'Geen opsies pas by jou soektog nie.',

        'placeholder' => 'Kies \'n opsie',

        'searching_message' => 'Soek tans …',

        'search_prompt' => 'Begin tik om te soek...',

    ],

    'tags_input' => [
        'placeholder' => 'Nuwe tag',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Versteek wagwoord',
            ],

            'show_password' => [
                'label' => 'Wys wagwoord',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nee',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Terug',
            ],

            'next_step' => [
                'label' => 'Volgende',
            ],

        ],

    ],

];
