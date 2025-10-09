<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klon',
            ],

            'add' => [

                'label' => 'Legg til i :label',

                'modal' => [

                    'heading' => 'Legg til i :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Legg til',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Sett inn mellom blokker',

                'modal' => [

                    'heading' => 'Legg til i :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Legg til',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Slett',
            ],

            'edit' => [

                'label' => 'Endre',

                'modal' => [

                    'heading' => 'Endre blokk',

                    'actions' => [

                        'save' => [
                            'label' => 'Lagre endringer',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Flytt',
            ],

            'move_down' => [
                'label' => 'Flytt ned',
            ],

            'move_up' => [
                'label' => 'Flytt opp',
            ],

            'collapse' => [
                'label' => 'Fold sammen',
            ],

            'expand' => [
                'label' => 'Utvid',
            ],

            'collapse_all' => [
                'label' => 'Fold sammen alle',
            ],

            'expand_all' => [
                'label' => 'Utvid alle',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Fjern alle',
            ],

            'select_all' => [
                'label' => 'Velg alle',
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
                    'label' => 'Dra-modus "beskjær"',
                ],

                'drag_move' => [
                    'label' => 'Dra-modus "flytt"',
                ],

                'flip_horizontal' => [
                    'label' => 'Vend bildet horisontalt',
                ],

                'flip_vertical' => [
                    'label' => 'Vend bildet vertikalt',
                ],

                'move_down' => [
                    'label' => 'Flytt bildet ned',
                ],

                'move_left' => [
                    'label' => 'Flytt vildet til venstre',
                ],

                'move_right' => [
                    'label' => 'Flytt bildet til høyre',
                ],

                'move_up' => [
                    'label' => 'Flytt bildet opp',
                ],

                'reset' => [
                    'label' => 'Nullstill',
                ],

                'rotate_left' => [
                    'label' => 'Roter bildet til venstre',
                ],

                'rotate_right' => [
                    'label' => 'Roter bildet til høyre',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Sett sideforhold til :ratio',
                ],

                'save' => [
                    'label' => 'Lagre',
                ],

                'zoom_100' => [
                    'label' => 'Zoom bildet til 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoom inn',
                ],

                'zoom_out' => [
                    'label' => 'Zoom ut',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Høyde',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotasjon',
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

                'label' => 'Sideforhold',

                'no_fixed' => [
                    'label' => 'Fri',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Redigering av SVG filer er ikke anbefalt da det kan resultere i tap av kvalitet ved skalering.\n Er du sikker på at du vil fortsette?',
                    'disabled' => 'Redigering av SVG filer er deaktivert da det kan føre til tap av kvalitet ved skalering.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Legg til rad',
            ],

            'delete' => [
                'label' => 'Slett rad',
            ],

            'reorder' => [
                'label' => 'Omorganiser rad',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Nøkkel',
            ],

            'value' => [
                'label' => 'Verdi',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Legg til filer',
            'blockquote' => 'Sitat',
            'bold' => 'Fet',
            'bullet_list' => 'Punktliste',
            'code_block' => 'Kode',
            'heading' => 'Tittel',
            'italic' => 'Kursiv',
            'link' => 'Lenke',
            'ordered_list' => 'Nummerert liste',
            'redo' => 'Gjør om',
            'strike' => 'Gjennomstrekning',
            'table' => 'Tabell',
            'undo' => 'Angre',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Velg',

                'actions' => [

                    'select' => [
                        'label' => 'Velg',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nei',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Legg til i :label',
            ],

            'add_between' => [
                'label' => 'Legg til mellom',
            ],

            'delete' => [
                'label' => 'Slett',
            ],

            'clone' => [
                'label' => 'Klon',
            ],

            'reorder' => [
                'label' => 'Flytt',
            ],

            'move_down' => [
                'label' => 'Flytt ned',
            ],

            'move_up' => [
                'label' => 'Flytt opp',
            ],

            'collapse' => [
                'label' => 'Fold sammen',
            ],

            'expand' => [
                'label' => 'Utvid',
            ],

            'collapse_all' => [
                'label' => 'Fold sammen alle',
            ],

            'expand_all' => [
                'label' => 'Utvid alle',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Last opp fil',

                'modal' => [

                    'heading' => 'Last opp fil',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fil',
                                'existing' => 'Erstatt fil',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt tekst',
                                'existing' => 'Endre alt tekst',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Sett inn',
                        ],

                        'save' => [
                            'label' => 'Lagre',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Endre',

                'modal' => [

                    'heading' => 'Lenke',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Åpne i ny fane',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Ingen treff for flettefelt.',

        'tools' => [
            'align_center' => 'Midtstill',
            'align_end' => 'Høyrejuster',
            'align_justify' => 'Blokkjuster',
            'align_start' => 'Venstrejuster',
            'attach_files' => 'Legg ved filer',
            'blockquote' => 'Blokksitat',
            'bold' => 'Fet',
            'bullet_list' => 'Punktliste',
            'clear_formatting' => 'Fjern formatering',
            'code' => 'Kode',
            'code_block' => 'Kodeblokk',
            'custom_blocks' => 'Blokker',
            'details' => 'Detaljer',
            'h1' => 'Tittel',
            'h2' => 'Overskrift',
            'h3' => 'Underoverskrift',
            'highlight' => 'Uthev',
            'horizontal_rule' => 'Horisontal linje',
            'italic' => 'Kursiv',
            'lead' => 'Ingress',
            'link' => 'Lenke',
            'merge_tags' => 'Flettefelt',
            'ordered_list' => 'Nummerert liste',
            'redo' => 'Gjør om',
            'small' => 'Liten tekst',
            'strike' => 'Gjennomstreking',
            'subscript' => 'Senket skrift',
            'superscript' => 'Hevet skrift',
            'table' => 'Tabell',
            'table_delete' => 'Slett tabell',
            'table_add_column_before' => 'Legg til kolonne før',
            'table_add_column_after' => 'Legg til kolonne etter',
            'table_delete_column' => 'Slett kolonne',
            'table_add_row_before' => 'Legg til rad over',
            'table_add_row_after' => 'Legg til rad under',
            'table_delete_row' => 'Slett rad',
            'table_merge_cells' => 'Slå sammen celler',
            'table_split_cell' => 'Del celle',
            'table_toggle_header_row' => 'Slå av/på overskriftsrad',
            'underline' => 'Understrek',
            'undo' => 'Angre',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Opprett',

                'modal' => [

                    'heading' => 'Opprett',

                    'actions' => [

                        'create' => [
                            'label' => 'Opprett',
                        ],

                        'create_another' => [
                            'label' => 'Opprett & opprett en til',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Endre',

                'modal' => [

                    'heading' => 'Endre',

                    'actions' => [

                        'save' => [
                            'label' => 'Lagre',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nei',
        ],

        'loading_message' => 'Laster...',

        'max_items_message' => 'Bare :count kan velges.',

        'no_search_results_message' => 'Ingen alternativer matcher ditt søk.',

        'placeholder' => 'Velg et alternativ',

        'searching_message' => 'Søker...',

        'search_prompt' => 'Skriv for å søke...',

    ],

    'tags_input' => [
        'placeholder' => 'Ny emneknagg',
    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopiér',
                'message' => 'Kopiert',
            ],

            'hide_password' => [
                'label' => 'Skjul passord',
            ],

            'show_password' => [
                'label' => 'Vis passord',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nei',
        ],

    ],

];
