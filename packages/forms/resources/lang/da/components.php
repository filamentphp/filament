<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klon',
            ],

            'add' => [
                'label' => 'Tilføj til :label',
            ],

            'add_between' => [
                'label' => 'Indsæt mellem blokke',
            ],

            'delete' => [
                'label' => 'Slet',
            ],

            'reorder' => [
                'label' => 'Flyt',
            ],

            'move_down' => [
                'label' => 'Flyt ned',
            ],

            'move_up' => [
                'label' => 'Flyt op',
            ],

            'collapse' => [
                'label' => 'Skjul',
            ],

            'expand' => [
                'label' => 'Udvid',
            ],

            'collapse_all' => [
                'label' => 'Skjul alle',
            ],

            'expand_all' => [
                'label' => 'Udvid alle',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Fravælg alle',
            ],

            'select_all' => [
                'label' => 'Vælg alle',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Annuller',
                ],

                'drag_crop' => [
                    'label' => 'Træktilstand "beskæring"',
                ],

                'drag_move' => [
                    'label' => 'Træk-tilstand "flyt"',
                ],

                'flip_horizontal' => [
                    'label' => 'Vend billedet horisontalt',
                ],

                'flip_vertical' => [
                    'label' => 'Vend billedet vertikalt',
                ],

                'move_down' => [
                    'label' => 'Flyt billedet ned',
                ],

                'move_left' => [
                    'label' => 'Flyt billedet til venstre',
                ],

                'move_right' => [
                    'label' => 'Flyt billedet til højre',
                ],

                'move_up' => [
                    'label' => 'Flyt billedet op',
                ],

                'reset' => [
                    'label' => 'Nulstil',
                ],

                'rotate_left' => [
                    'label' => 'Roter billedet til venstre',
                ],

                'rotate_right' => [
                    'label' => 'Roter billedet til højre',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Indstil billedformat til :ratio',
                ],

                'save' => [
                    'label' => 'Gem',
                ],

                'zoom_100' => [
                    'label' => 'Zoom billedet til 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoom ind',
                ],

                'zoom_out' => [
                    'label' => 'Zoom ud',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Højde',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotation',
                    'unit' => 'grader',
                ],

                'width' => [
                    'label' => 'Bredde',
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

                'label' => 'Størrelsesforhold',

                'no_fixed' => [
                    'label' => 'Frit',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Redigering af SVG filer anbefales ikke, da det kan resultere i kvalitetstab ved skalering.\n Er du sikker på, at du vil fortsætte?',
                    'disabled' => 'Redigering af SVG filer er deaktiveret, da det kan resultere i kvalitetstab ved skalering.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Tilføj række',
            ],

            'delete' => [
                'label' => 'Slet række',
            ],

            'reorder' => [
                'label' => 'Omorganiser række',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Nøgle',
            ],

            'value' => [
                'label' => 'Værdi',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Vedhæft filer',
            'blockquote' => 'Citere',
            'bold' => 'Fed',
            'bullet_list' => 'Punkter',
            'code_block' => 'Kode',
            'heading' => 'Overskrift',
            'italic' => 'Kursiv',
            'link' => 'Link',
            'ordered_list' => 'Nummereret liste',
            'redo' => 'Gentag',
            'strike' => 'Gennemstregning',
            'table' => 'tabel',
            'undo' => 'Fortryd',
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
                'label' => 'Tilføj til :label',
            ],

            'add_between' => [
                'label' => 'Indsæt mellem',
            ],

            'delete' => [
                'label' => 'Slet',
            ],

            'clone' => [
                'label' => 'Klon',
            ],

            'reorder' => [
                'label' => 'Flyt',
            ],

            'move_down' => [
                'label' => 'Flyt ned',
            ],

            'move_up' => [
                'label' => 'Flyt op',
            ],

            'collapse' => [
                'label' => 'Skjul',
            ],

            'expand' => [
                'label' => 'Udvid',
            ],

            'collapse_all' => [
                'label' => 'Skjul alle',
            ],

            'expand_all' => [
                'label' => 'Udvid alle',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Link',
                    'unlink' => 'Fjern link',
                ],

                'label' => 'URL',

                'placeholder' => 'Indtast en URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Vedhæft filer',
            'blockquote' => 'Citere',
            'bold' => 'Fed',
            'bullet_list' => 'Punkter',
            'code_block' => 'Kode',
            'h1' => 'Titel',
            'h2' => 'Overskrift',
            'h3' => 'Underoverskrift',
            'italic' => 'Kursiv',
            'link' => 'Link',
            'ordered_list' => 'Tal',
            'redo' => 'Gentag',
            'strike' => 'Gennemstreget',
            'undo' => 'Fortryd',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Opret',

                    'actions' => [

                        'create' => [
                            'label' => 'Opret',
                        ],

                        'create_another' => [
                            'label' => 'Opret & opret en mere',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Rediger',

                    'actions' => [

                        'save' => [
                            'label' => 'Gem',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nej',
        ],

        'loading_message' => 'Indlæser...',

        'max_items_message' => 'Kun :count kan vælges.',

        'no_search_results_message' => 'Ingen muligheder der matcher din søgning.',

        'placeholder' => 'Vælg en indstilling',

        'searching_message' => 'Søger...',

        'search_prompt' => 'Begynd at skrive for at søge ...',

    ],

    'tags_input' => [
        'placeholder' => 'Ny mærkat',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Skjul adgangskode',
            ],

            'show_password' => [
                'label' => 'Vis adgangskode',
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
                'label' => 'Tilbage',
            ],

            'next_step' => [
                'label' => 'Næste',
            ],

        ],

    ],

];
