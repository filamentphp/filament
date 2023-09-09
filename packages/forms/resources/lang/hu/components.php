<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Másolás',
            ],

            'add' => [
                'label' => 'Új :label',
            ],

            'add_between' => [
                'label' => 'Beillesztés közé',
            ],

            'delete' => [
                'label' => 'Törlés',
            ],

            'reorder' => [
                'label' => 'Mozgatás',
            ],

            'move_down' => [
                'label' => 'Mozgatás lefelé',
            ],

            'move_up' => [
                'label' => 'Mozgatás felfelé',
            ],

            'collapse' => [
                'label' => 'Becsuk',
            ],

            'expand' => [
                'label' => 'Kibont',
            ],

            'collapse_all' => [
                'label' => 'Becsuk mindent',
            ],

            'expand_all' => [
                'label' => 'Kibont mindent',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Törölje az összes jelölést',
            ],

            'select_all' => [
                'label' => 'Összes kijelölése',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Mégse',
                ],

                'drag_crop' => [
                    'label' => 'Kijelölés mód',
                ],

                'drag_move' => [
                    'label' => 'Mozgatás mód',
                ],

                'flip_horizontal' => [
                    'label' => 'A kép vízszintes tükrözése',
                ],

                'flip_vertical' => [
                    'label' => 'A kép függőleges tükrözése',
                ],

                'move_down' => [
                    'label' => 'Lefele mozgatás',
                ],

                'move_left' => [
                    'label' => 'Balra mozgatás',
                ],

                'move_right' => [
                    'label' => 'Jobbra mozgatás',
                ],

                'move_up' => [
                    'label' => 'Felfele mozgatás',
                ],

                'reset' => [
                    'label' => 'Újra tölt',
                ],

                'rotate_left' => [
                    'label' => 'Kép elforgatása balra',
                ],

                'rotate_right' => [
                    'label' => 'Kép elforgatása jobbra',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Állítsa be a képarányt :ratio értékre',
                ],

                'save' => [
                    'label' => 'Mentés',
                ],

                'zoom_100' => [
                    'label' => 'Kép nagyítása 100%-ra',
                ],

                'zoom_in' => [
                    'label' => 'Nagyítás',
                ],

                'zoom_out' => [
                    'label' => 'Kicsinyítés',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Magasság',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Forgatás',
                    'unit' => 'fok',
                ],

                'width' => [
                    'label' => 'Szélesség',
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

                'label' => 'Képarányok',

                'no_fixed' => [
                    'label' => 'Szabad',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Sor hozzáadása',
            ],

            'delete' => [
                'label' => 'Sor törlése',
            ],

            'reorder' => [
                'label' => 'Sor újrarendezése',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Kulcs',
            ],

            'value' => [
                'label' => 'Érték',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Fájlok csatolása',
            'blockquote' => 'Idézet',
            'bold' => 'Félkövér',
            'bullet_list' => 'Felsorolás',
            'code_block' => 'Kódblokk',
            'heading' => 'Cím',
            'italic' => 'Dőlt',
            'link' => 'Hivatkozás',
            'ordered_list' => 'Számozott lista',
            'redo' => 'Előre',
            'strike' => 'Áthúzott',
            'table' => 'Táblázat',
            'undo' => 'Vissza',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Új :label',
            ],

            'delete' => [
                'label' => 'Törlés',
            ],

            'clone' => [
                'label' => 'Másolás',
            ],

            'reorder' => [
                'label' => 'Mozgatás',
            ],

            'move_down' => [
                'label' => 'Mozgatás lefelé',
            ],

            'move_up' => [
                'label' => 'Mozgatás felfelé',
            ],

            'collapse' => [
                'label' => 'Becsuk',
            ],

            'expand' => [
                'label' => 'Kibont',
            ],

            'collapse_all' => [
                'label' => 'Becsuk mindent',
            ],

            'expand_all' => [
                'label' => 'Kibont mindent',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Hivatkozás',
                    'unlink' => 'Hivatkozás törlése',
                ],

                'label' => 'URL',

                'placeholder' => 'URL cím',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Fájlok csatolása',
            'blockquote' => 'Idézet',
            'bold' => 'Félkövér',
            'bullet_list' => 'Felsorolás',
            'code_block' => 'Kódblokk',
            'h1' => 'Címsor 1',
            'h2' => 'Címsor 2',
            'h3' => 'Címsor 3',
            'italic' => 'Dőlt',
            'link' => 'Hivatkozás',
            'ordered_list' => 'Számozott lista',
            'redo' => 'Előre',
            'strike' => 'Áthúzott',
            'underline' => 'Alázhúzott',
            'undo' => 'Vissza',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Új opció hozzáadása',

                    'actions' => [

                        'create' => [
                            'label' => 'Hozzáadás',
                        ],

                        'create_another' => [
                            'label' => 'Hozzáadás és másik hozzáadása',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Szerkesztés',

                    'actions' => [

                        'save' => [
                            'label' => 'Mentés',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Igen',
            'false' => 'Nem',
        ],

        'loading_message' => 'Kérlek várj...',

        'max_items_message' => 'Csak :count elem választható ki.',

        'no_search_results_message' => 'Nincs találat',

        'placeholder' => 'Válassz...',

        'searching_message' => 'Keresés...',

        'search_prompt' => 'Kezdj el írni a kereséshez...',

    ],

    'tags_input' => [
        'placeholder' => 'Címke hozzáadása',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Előző lépés',
            ],

            'next_step' => [
                'label' => 'Következő lépés',
            ],

        ],

    ],

];
