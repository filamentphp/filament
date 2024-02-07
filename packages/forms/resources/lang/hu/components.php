<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Duplikálás',
            ],

            'add' => [
                'label' => 'Új :label',
            ],

            'add_between' => [
                'label' => 'Beillesztés blokkok közé',
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
                'label' => 'Becsukás',
            ],

            'expand' => [
                'label' => 'Kibontás',
            ],

            'collapse_all' => [
                'label' => 'Összes becsukása',
            ],

            'expand_all' => [
                'label' => 'Összes kibontása',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Kijelölés megszüntetése',
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
                    'label' => 'Mégsem',
                ],

                'drag_crop' => [
                    'label' => 'Méretrevágási mód',
                ],

                'drag_move' => [
                    'label' => 'Mozgatási mód',
                ],

                'flip_horizontal' => [
                    'label' => 'Kép vízszintes tükrözése',
                ],

                'flip_vertical' => [
                    'label' => 'Kép függőleges tükrözése',
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
                    'label' => 'Visszaállítás',
                ],

                'rotate_left' => [
                    'label' => 'Kép elforgatása balra',
                ],

                'rotate_right' => [
                    'label' => 'Kép elforgatása jobbra',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Képarány beállítása :ratio értékre',
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
                    'label' => 'Elforgatás',
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
                    'label' => 'Egyéni',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Az SVG fájlok szerkesztése nem ajánlott, mivel minőségromláshoz vezethet az átméretezés során.\n Biztosan szeretnéd folytatni?',
                    'disabled' => 'Az SVG fájlok szerkesztése nem engedélyezett, mivel minőségromláshoz vezethet az átméretezés során.',
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
                'label' => 'Sor mozgatása',
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
            'heading' => 'Címsor',
            'italic' => 'Dőlt',
            'link' => 'Hivatkozás',
            'ordered_list' => 'Számozott lista',
            'redo' => 'Visszaállítás',
            'strike' => 'Áthúzott',
            'table' => 'Táblázat',
            'undo' => 'Visszavonás',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Igen',
            'false' => 'Nem',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Új :label',
            ],

            'add_between' => [
                'label' => 'Beillesztés blokkok közé',
            ],

            'delete' => [
                'label' => 'Törlés',
            ],

            'clone' => [
                'label' => 'Duplikálás',
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
                'label' => 'Becsukás',
            ],

            'expand' => [
                'label' => 'Kibontás',
            ],

            'collapse_all' => [
                'label' => 'Összes becsukása',
            ],

            'expand_all' => [
                'label' => 'Összes kibontása',
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
            'redo' => 'Visszaállítás',
            'strike' => 'Áthúzott',
            'underline' => 'Alázhúzott',
            'undo' => 'Visszavonás',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Új elem hozzáadása',

                    'actions' => [

                        'create' => [
                            'label' => 'Hozzáadás',
                        ],

                        'create_another' => [
                            'label' => 'Mentés és új hozzáadása',
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

        'placeholder' => 'Válassz ki egy elemet',

        'searching_message' => 'Keresés...',

        'search_prompt' => 'Kezdj el írni a kereséshez...',

    ],

    'tags_input' => [
        'placeholder' => 'Címke hozzáadása',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Jelszó elrejtése',
            ],

            'show_password' => [
                'label' => 'Jelszó megjelenítése',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Igen',
            'false' => 'Nem',
        ],

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
