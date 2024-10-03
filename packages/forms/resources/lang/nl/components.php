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

                'label' => 'Invoegen',

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
                    'label' => 'Geen',
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

        'toolbar_buttons' => [
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
            'undo' => 'Herstellen',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Link',
                    'unlink' => 'Unlink',
                ],

                'label' => 'URL',

                'placeholder' => 'Voer een URL in',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Bestanden bijvoegen',
            'blockquote' => 'Quote',
            'bold' => 'Vet',
            'bullet_list' => 'Ongeordende lijst',
            'code_block' => 'Codeblok',
            'h1' => 'Titel',
            'h2' => 'Kop',
            'h3' => 'Tussenkop',
            'italic' => 'Cursief',
            'link' => 'Link',
            'ordered_list' => 'Genummerde lijst',
            'redo' => 'Opnieuw uitvoeren',
            'strike' => 'Doorhalen',
            'underline' => 'Onderstrepen',
            'undo' => 'Ongedaan maken',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

        'no_search_results_message' => 'Er zijn geen resultaten voor je zoekopdracht.',

        'placeholder' => 'Selecteer een optie',

        'searching_message' => 'Zoeken...',

        'search_prompt' => 'Start met typen om te zoeken...',

    ],

    'tags_input' => [
        'placeholder' => 'Nieuwe tag',
    ],

    'text_input' => [

        'actions' => [

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Vorige',
            ],

            'next_step' => [
                'label' => 'Volgende',
            ],

        ],

    ],

];
