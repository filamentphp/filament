<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Klonen',
            ],

            'add' => [

                'label' => 'Toevoegen aan :label',

                'modal' => [

                    'heading' => 'Toevoegen aan :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Toevoegen',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Invoegen tussen blokken',

                'modal' => [

                    'heading' => 'Toevoegen aan :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Toevoegen',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Verwijderen',
            ],

            'edit' => [

                'label' => 'Bewerken',

                'modal' => [

                    'heading' => 'Blok bewerken',

                    'actions' => [

                        'save' => [
                            'label' => 'Wijzigingen opslaan',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Verplaatsen',
            ],

            'move_down' => [
                'label' => 'Omlaag verplaatsen',
            ],

            'move_up' => [
                'label' => 'Omhoog verplaatsen',
            ],

            'collapse' => [
                'label' => 'Inklappen',
            ],

            'expand' => [
                'label' => 'Uitklappen',
            ],

            'collapse_all' => [
                'label' => 'Alles inklappen',
            ],

            'expand_all' => [
                'label' => 'Alles uitklappen',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Alles deselecteren',
            ],

            'select_all' => [
                'label' => 'Alles selecteren',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Annuleren',
                ],

                'drag_crop' => [
                    'label' => 'Sleepmodus "bijsnijden"',
                ],

                'drag_move' => [
                    'label' => 'Sleepmodus "verplaatsen"',
                ],

                'flip_horizontal' => [
                    'label' => 'Afbeelding horizontaal spiegelen',
                ],

                'flip_vertical' => [
                    'label' => 'Afbeelding verticaal spiegelen',
                ],

                'move_down' => [
                    'label' => 'Afbeelding naar beneden verplaatsen',
                ],

                'move_left' => [
                    'label' => 'Afbeelding naar links verplaatsen',
                ],

                'move_right' => [
                    'label' => 'Afbeelding naar rechts verplaatsen',
                ],

                'move_up' => [
                    'label' => 'Afbeelding naar boven verplaatsen',
                ],

                'reset' => [
                    'label' => 'Resetten',
                ],

                'rotate_left' => [
                    'label' => 'Afbeelding naar links draaien',
                ],

                'rotate_right' => [
                    'label' => 'Afbeelding naar rechts draaien',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Beeldverhouding instellen op :ratio',
                ],

                'save' => [
                    'label' => 'Opslaan',
                ],

                'zoom_100' => [
                    'label' => 'Afbeelding uitzoomen naar 100%',
                ],

                'zoom_in' => [
                    'label' => 'Inzoomen',
                ],

                'zoom_out' => [
                    'label' => 'Uitzoomen',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Hoogte',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotatie',
                    'unit' => 'gr',
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

                'label' => 'Beeldverhoudingen',

                'no_fixed' => [
                    'label' => 'Vrij',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Het bewerken van SVG-bestanden wordt niet aanbevolen, omdat dit kan leiden tot kwaliteitsverlies bij het schalen.\n Weet je zeker dat je door wilt gaan?',
                    'disabled' => 'Het bewerken van SVG-bestanden is uitgeschakeld omdat dit kan leiden tot kwaliteitsverlies bij het schalen.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Rij toevoegen',
            ],

            'delete' => [
                'label' => 'Rij verwijderen',
            ],

            'reorder' => [
                'label' => 'Rij herordenen',
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

        'file_attachments_accepted_file_types_message' => 'Geüploade bestanden moeten van het volgende type zijn: :values.',

        'file_attachments_max_size_message' => 'Geüploade bestanden mogen niet groter zijn dan :max kilobytes.',

        'tools' => [
            'attach_files' => 'Bestanden bijvoegen',
            'blockquote' => 'Blokcitaat',
            'bold' => 'Vet',
            'bullet_list' => 'Ongeordende lijst',
            'code_block' => 'Codeblok',
            'heading' => 'Kop',
            'italic' => 'Cursief',
            'link' => 'Link',
            'ordered_list' => 'Genummerde lijst',
            'redo' => 'Opnieuw',
            'strike' => 'Doorhalen',
            'table' => 'Tabel',
            'undo' => 'Ongedaan maken',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Selecteren',

                'actions' => [

                    'select' => [
                        'label' => 'Selecteren',
                    ],

                ],

            ],

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
                'label' => 'Toevoegen aan :label',
            ],

            'add_between' => [
                'label' => 'Invoegen',
            ],

            'delete' => [
                'label' => 'Verwijderen',
            ],

            'clone' => [
                'label' => 'Klonen',
            ],

            'reorder' => [
                'label' => 'Verplaatsen',
            ],

            'move_down' => [
                'label' => 'Omlaag verplaatsen',
            ],

            'move_up' => [
                'label' => 'Omhoog verplaatsen',
            ],

            'collapse' => [
                'label' => 'Inklappen',
            ],

            'expand' => [
                'label' => 'Uitklappen',
            ],

            'collapse_all' => [
                'label' => 'Alles inklappen',
            ],

            'expand_all' => [
                'label' => 'Alles uitklappen',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Bestand uploaden',

                'modal' => [

                    'heading' => 'Bestand uploaden',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Bestand',
                                'existing' => 'Bestand vervangen',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt tekst',
                                'existing' => 'Alt tekst wijzigen',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Invoegen',
                        ],

                        'save' => [
                            'label' => 'Opslaan',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Raster',

                'modal' => [

                    'heading' => 'Raster',

                    'form' => [

                        'preset' => [

                            'label' => 'Voorinstelling',

                            'placeholder' => 'Geen',

                            'options' => [
                                'two' => 'Twee',
                                'three' => 'Drie',
                                'four' => 'Vier',
                                'five' => 'Vijf',
                                'two_start_third' => 'Twee (Start Derde)',
                                'two_end_third' => 'Twee (Einde Derde)',
                                'two_start_fourth' => 'Twee (Start Vierde)',
                                'two_end_fourth' => 'Twee (Einde Vierde)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Kolommen',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Vanaf breekpunt',

                            'options' => [
                                'default' => 'Alle',
                                'sm' => 'Klein',
                                'md' => 'Medium',
                                'lg' => 'Groot',
                                'xl' => 'Extra groot',
                                '2xl' => 'Twee keer extra groot',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Twee asymmetrische kolommen',
                        ],

                        'start_span' => [
                            'label' => 'Start bereik',
                        ],

                        'end_span' => [
                            'label' => 'Eind bereik',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Link',

                'modal' => [

                    'heading' => 'Link',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Openen in nieuwe tab',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Tekstkleur',

                'modal' => [

                    'heading' => 'Tekstkleur',

                    'form' => [

                        'color' => [
                            'label' => 'Kleur',
                        ],

                        'custom_color' => [
                            'label' => 'Aangepaste kleur',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Geüploade bestanden moeten van het volgende type zijn: :values.',

        'file_attachments_max_size_message' => 'Geüploade bestanden mogen niet groter zijn dan :max kilobytes.',

        'no_merge_tag_search_results_message' => 'Geen merge tags gevonden.',

        'mentions' => [
            'no_options_message' => 'Geen opties beschikbaar.',
            'no_search_results_message' => 'Geen resultaten gevonden.',
            'search_prompt' => 'Begin met typen om te zoeken...',
            'searching_message' => 'Zoeken...',
        ],

        'tools' => [
            'align_center' => 'Centreren',
            'align_end' => 'Rechts uitlijnen',
            'align_justify' => 'Uitvullen',
            'align_start' => 'Links uitlijnen',
            'attach_files' => 'Bestanden bijvoegen',
            'blockquote' => 'Citaatblok',
            'bold' => 'Vet',
            'bullet_list' => 'Opsommingstekens',
            'clear_formatting' => 'Opmaak wissen',
            'code' => 'Code',
            'code_block' => 'Codeblok',
            'custom_blocks' => 'Blokken',
            'details' => 'Details',
            'h1' => 'Titel',
            'h2' => 'Kop',
            'h3' => 'Subkop',
            'grid' => 'Raster',
            'grid_delete' => 'Raster verwijderen',
            'highlight' => 'Markeren',
            'horizontal_rule' => 'Horizontale lijn',
            'italic' => 'Cursief',
            'lead' => 'Inleidende tekst',
            'link' => 'Link',
            'merge_tags' => 'Merge tags',
            'ordered_list' => 'Genummerde lijst',
            'redo' => 'Opnieuw',
            'small' => 'Kleine tekst',
            'strike' => 'Doorhalen',
            'subscript' => 'Subscript',
            'superscript' => 'Superscript',
            'table' => 'Tabel',
            'table_delete' => 'Tabel verwijderen',
            'table_add_column_before' => 'Kolom ervoor toevoegen',
            'table_add_column_after' => 'Kolom erna toevoegen',
            'table_delete_column' => 'Kolom verwijderen',
            'table_add_row_before' => 'Rij erboven toevoegen',
            'table_add_row_after' => 'Rij eronder toevoegen',
            'table_delete_row' => 'Rij verwijderen',
            'table_merge_cells' => 'Cellen samenvoegen',
            'table_split_cell' => 'Cel splitsen',
            'table_toggle_header_row' => 'Koprij wisselen',
            'table_toggle_header_cell' => 'Kopcel wisselen',
            'text_color' => 'Tekstkleur',
            'underline' => 'Onderstrepen',
            'undo' => 'Ongedaan maken',
        ],

        'uploading_file_message' => 'Bestand uploaden...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Aanmaken',

                'modal' => [

                    'heading' => 'Aanmaken',

                    'actions' => [

                        'create' => [
                            'label' => 'Aanmaken',
                        ],

                        'create_another' => [
                            'label' => 'Aanmaken & nieuwe aanmaken',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Bewerken',

                'modal' => [

                    'heading' => 'Bewerken',

                    'actions' => [

                        'save' => [
                            'label' => 'Opslaan',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nee',
        ],

        'loading_message' => 'Laden...',

        'max_items_message' => 'Er kunnen maar :count geselecteerd worden.',

        'no_options_message' => 'Geen opties beschikbaar.',

        'no_search_results_message' => 'Geen resultaten gevonden.',

        'placeholder' => 'Selecteer een optie',

        'searching_message' => 'Zoeken...',

        'search_prompt' => 'Begin met typen om te zoeken...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Verwijderen',
            ],

        ],

        'placeholder' => 'Nieuwe tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopiëren',
                'message' => 'Gekopieerd',
            ],

            'hide_password' => [
                'label' => 'Wachtwoord verbergen',
            ],

            'show_password' => [
                'label' => 'Wachtwoord tonen',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nee',
        ],

    ],

];
