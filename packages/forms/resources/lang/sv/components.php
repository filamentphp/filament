<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klona',
            ],

            'add' => [
                'label' => 'Lägg till i :label',
            ],

            'add_between' => [
                'label' => 'Infoga mellan block',
            ],

            'delete' => [
                'label' => 'Radera',
            ],

            'reorder' => [
                'label' => 'Flytta',
            ],

            'move_down' => [
                'label' => 'Flytta ner',
            ],

            'move_up' => [
                'label' => 'Flytta upp',
            ],

            'collapse' => [
                'label' => 'Komprimera',
            ],

            'expand' => [
                'label' => 'Expandera',
            ],

            'collapse_all' => [
                'label' => 'Komprimera alla',
            ],

            'expand_all' => [
                'label' => 'Expandera alla',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Avmarkera alla',
            ],

            'select_all' => [
                'label' => 'Markera alla',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Avbryt',
                ],

                'drag_crop' => [
                    'label' => 'Dragläge "beskär"',
                ],

                'drag_move' => [
                    'label' => 'Dragläge "flytta"',
                ],

                'flip_horizontal' => [
                    'label' => 'Vänd bilden horisontellt',
                ],

                'flip_vertical' => [
                    'label' => 'Vänd bilden vertikalt',
                ],

                'move_down' => [
                    'label' => 'Flytta bilden nedåt',
                ],

                'move_left' => [
                    'label' => 'Flytta bilden åt vänster',
                ],

                'move_right' => [
                    'label' => 'Flytta bilden åt höger',
                ],

                'move_up' => [
                    'label' => 'Flytta bilden uppåt',
                ],

                'reset' => [
                    'label' => 'Återställ',
                ],

                'rotate_left' => [
                    'label' => 'Rotera bilden åt vänster',
                ],

                'rotate_right' => [
                    'label' => 'Rotera bilden åt höger',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Ändra bildformat till :ratio',
                ],

                'save' => [
                    'label' => 'Spara',
                ],

                'zoom_100' => [
                    'label' => 'Zooma bilden till 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zooma in',
                ],

                'zoom_out' => [
                    'label' => 'Zooma ut',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Höjd',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotation',
                    'unit' => 'grad',
                ],

                'width' => [
                    'label' => 'Bredd',
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

                'label' => 'Bildformat',

                'no_fixed' => [
                    'label' => 'Fritt',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Redigering av SVG-filer rekommenderas inte eftersom det tar bort bildens förmåga att skala utan kvalitetsförlust.\n Är du säker på att du vill fortsätta?',
                    'disabled' => 'Redigering av SVG-filer är inaktiverat eftersom det tar bort bildens förmåga att skala utan kvalitetsförlust.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Lägg till rad',
            ],

            'delete' => [
                'label' => 'Ta bort rad',
            ],

            'reorder' => [
                'label' => 'Ändra ordning på rad',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Namn',
            ],

            'value' => [
                'label' => 'Värde',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Lägg till filer',
            'blockquote' => 'Citat',
            'bold' => 'Fet',
            'bullet_list' => 'Punktlista',
            'code_block' => 'Kod',
            'heading' => 'Rubrik',
            'italic' => 'Kursiv',
            'link' => 'Länk',
            'ordered_list' => 'Nummerlista',
            'redo' => 'Gör om',
            'strike' => 'Genomstruken',
            'table' => 'Tabell',
            'undo' => 'Ångra',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nej',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Lägg till i :label',
            ],

            'add_between' => [
                'label' => 'Infoga mellan',
            ],

            'delete' => [
                'label' => 'Radera',
            ],

            'clone' => [
                'label' => 'Klona',
            ],

            'reorder' => [
                'label' => 'Flytta',
            ],

            'move_down' => [
                'label' => 'Flytta ner',
            ],

            'move_up' => [
                'label' => 'Flytta upp',
            ],

            'collapse' => [
                'label' => 'Komprimera',
            ],

            'expand' => [
                'label' => 'Expandera',
            ],

            'collapse_all' => [
                'label' => 'Komprimera alla',
            ],

            'expand_all' => [
                'label' => 'Expandera alla',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Länka',
                    'unlink' => 'Ta bort länk',
                ],

                'label' => 'URL',

                'placeholder' => 'Ange en URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Lägg till filer',
            'blockquote' => 'Citat',
            'bold' => 'Fet',
            'bullet_list' => 'Punktlista',
            'code_block' => 'Kod',
            'h1' => 'Titel',
            'h2' => 'Rubrik',
            'h3' => 'Underrubrik',
            'italic' => 'Kursiv',
            'link' => 'Länk',
            'ordered_list' => 'Nummerlista',
            'redo' => 'Gör om',
            'strike' => 'Genomstruken',
            'underline' => 'Understruken',
            'undo' => 'Ångra',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Skapa',

                    'actions' => [

                        'create' => [
                            'label' => 'Skapa',
                        ],

                        'create_another' => [
                            'label' => 'Skapa & skapa en till',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Redigera',

                    'actions' => [

                        'save' => [
                            'label' => 'Spara',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nej',
        ],

        'loading_message' => 'Laddar...',

        'max_items_message' => 'Kan endast välja :count st.',

        'no_search_results_message' => 'Inga alternativ matchar din sökning.',

        'placeholder' => 'Välj ett alternativ',

        'searching_message' => 'Söker...',

        'search_prompt' => 'Börja skriva för att söka...',

    ],

    'tags_input' => [
        'placeholder' => 'Ny tagg',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Dölj lösenord',
            ],

            'show_password' => [
                'label' => 'Visa lösenord',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nej',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Föregående',
            ],

            'next_step' => [
                'label' => 'Nästa',
            ],

        ],

    ],

];
