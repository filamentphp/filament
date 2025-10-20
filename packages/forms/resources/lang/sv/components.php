<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klona',
            ],

            'add' => [

                'label' => 'Lägg till i :label',

                'modal' => [

                    'heading' => 'Lägg till i :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Lägg till',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Infoga mellan block',

                'modal' => [

                    'heading' => 'Lägg till i :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Lägg till',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Radera',
            ],

            'edit' => [

                'label' => 'Redigera',

                'modal' => [

                    'heading' => 'Redigera block',

                    'actions' => [

                        'save' => [
                            'label' => 'Spara ändringar',
                        ],

                    ],

                ],

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

        'tools' => [
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

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Välj',

                'actions' => [

                    'select' => [
                        'label' => 'Välj',
                    ],

                ],

            ],

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

        'actions' => [

            'attach_files' => [

                'label' => 'Ladda upp fil',

                'modal' => [

                    'heading' => 'Ladda upp fil',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fil',
                                'existing' => 'Ersätt fil',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt-text',
                                'existing' => 'Ändra alt-text',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Infoga',
                        ],

                        'save' => [
                            'label' => 'Spara',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Redigera',

                'modal' => [

                    'heading' => 'Länk',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Öppna i ny flik',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Kunde inte matcha mallvariabler.',

        'tools' => [
            'align_center' => 'Centrera',
            'align_end' => 'Justera slutet',
            'align_justify' => 'Marginaljustera',
            'align_start' => 'Justera början',
            'attach_files' => 'Lägg till filer',
            'blockquote' => 'Citat',
            'bold' => 'Fet',
            'bullet_list' => 'Punktlista',
            'clear_formatting' => 'Rensa formatering',
            'code' => 'Kod',
            'code_block' => 'Kodblock',
            'custom_blocks' => 'Block',
            'details' => 'Detaljer',
            'h1' => 'Titel',
            'h2' => 'Rubrik',
            'h3' => 'Underrubrik',
            'highlight' => 'Markera',
            'horizontal_rule' => 'Horisontell linje',
            'italic' => 'Kursiv',
            'lead' => 'Ingress',
            'link' => 'Länk',
            'merge_tags' => 'Mallvariabel',
            'ordered_list' => 'Nummerlista',
            'redo' => 'Gör om',
            'small' => 'Liten text',
            'strike' => 'Genomstruken',
            'subscript' => 'Nedsänkt',
            'superscript' => 'Upphöjd',
            'table' => 'Tabell',
            'table_delete' => 'Radera tabell',
            'table_add_column_before' => 'Lägg till kolumn före',
            'table_add_column_after' => 'Lägg till kolumn efter',
            'table_delete_column' => 'Radera kolumn',
            'table_add_row_before' => 'Lägg till rad ovan',
            'table_add_row_after' => 'Lägg till rad nedan',
            'table_delete_row' => 'Radera rad',
            'table_merge_cells' => 'Sammanfoga celler',
            'table_split_cell' => 'Dela cell',
            'table_toggle_header_row' => 'Växla rubrikrad',
            'underline' => 'Understruken',
            'undo' => 'Ångra',
        ],

        'uploading_file_message' => 'Laddar upp fil...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Skapa',

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

                'label' => 'Redigera',

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

            'copy' => [
                'label' => 'Kopiera',
                'message' => 'Kopierat',
            ],

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

];
