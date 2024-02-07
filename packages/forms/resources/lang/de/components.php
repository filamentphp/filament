<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kopieren',
            ],

            'add' => [
                'label' => 'Hinzufügen',
            ],

            'add_between' => [
                'label' => 'Einfügen',
            ],

            'delete' => [
                'label' => 'Löschen',
            ],

            'reorder' => [
                'label' => 'Verschieben',
            ],

            'move_down' => [
                'label' => 'Nach unten verschieben',
            ],

            'move_up' => [
                'label' => 'Nach oben verschieben',
            ],

            'collapse' => [
                'label' => 'Einklappen',
            ],

            'expand' => [
                'label' => 'Ausklappen',
            ],

            'collapse_all' => [
                'label' => 'Alle einklappen',
            ],

            'expand_all' => [
                'label' => 'Alle ausklappen',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Alle deselektieren',
            ],

            'select_all' => [
                'label' => 'Alle selektieren',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Abbrechen',
                ],

                'drag_crop' => [
                    'label' => 'Drag Modus "beschneiden"',
                ],

                'drag_move' => [
                    'label' => 'Drag Modus "verschieben"',
                ],

                'flip_horizontal' => [
                    'label' => 'Bild horizontal spiegeln',
                ],

                'flip_vertical' => [
                    'label' => 'Bild vertikal spiegeln',
                ],

                'move_down' => [
                    'label' => 'Bild nach unten',
                ],

                'move_left' => [
                    'label' => 'Bild nach links',
                ],

                'move_right' => [
                    'label' => 'Bild nach rechts',
                ],

                'move_up' => [
                    'label' => 'Bild nach oben',
                ],

                'reset' => [
                    'label' => 'Zurücksetzen',
                ],

                'rotate_left' => [
                    'label' => 'Bild nach links kippen',
                ],

                'rotate_right' => [
                    'label' => 'Bild nach rechts kippen',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Seitenverhältnis festlegen :ratio',
                ],

                'save' => [
                    'label' => 'Speichern',
                ],

                'zoom_100' => [
                    'label' => 'Bild Zoom 100%',
                ],

                'zoom_in' => [
                    'label' => 'Hereinzoomen',
                ],

                'zoom_out' => [
                    'label' => 'Herauszoomen',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Höhe',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Drehung',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Breite',
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

                'label' => 'Seitenverhältnisse',

                'no_fixed' => [
                    'label' => 'Frei',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Das Bearbeiten von SVG Dateien ist nicht empfohlen, da es Qualitätsverluste beim Verändern der Größe geben kann.\n Wirklich fortfahren?',
                    'disabled' => 'Das Bearbeiten von SVG Dateien ist deaktiviert, da es Qualitätsverluste beim Verändern der Größe geben kann.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Zeile hinzufügen',
            ],

            'delete' => [
                'label' => 'Zeile löschen',
            ],

            'reorder' => [
                'label' => 'Zeile neu sortieren',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Schlüssel',
            ],

            'value' => [
                'label' => 'Wert',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Dateien hinzufügen',
            'bold' => 'Fett',
            'bullet_list' => 'Liste',
            'code_block' => 'Code Block',
            'edit' => 'Bearbeiten',
            'italic' => 'Kursiv',
            'link' => 'Link',
            'ordered_list' => 'Nummerierte Liste',
            'preview' => 'Vorschau',
            'strike' => 'Durchgestrichen',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Hinzufügen',
            ],

            'delete' => [
                'label' => 'Löschen',
            ],

            'clone' => [
                'label' => 'Kopieren',
            ],

            'reorder' => [
                'label' => 'Verschieben',
            ],

            'move_down' => [
                'label' => 'Runter verschieben',
            ],

            'move_up' => [
                'label' => 'Hoch verschieben',
            ],

            'collapse' => [
                'label' => 'Einklappen',
            ],

            'expand' => [
                'label' => 'Ausklappen',
            ],

            'collapse_all' => [
                'label' => 'Alle einklappen',
            ],

            'expand_all' => [
                'label' => 'Alle ausklappen',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Verlinken',
                    'unlink' => 'Verlinkung aufheben',
                ],

                'label' => 'URL',

                'placeholder' => 'URL eingeben',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Dateien anhängen',
            'blockquote' => 'Zitat',
            'bold' => 'Fett',
            'bullet_list' => 'Aufzählung',
            'code_block' => 'Code Block',
            'h1' => 'Titel',
            'h2' => 'Überschrift',
            'h3' => 'Unterüberschrift',
            'italic' => 'Kursiv',
            'link' => 'Link',
            'ordered_list' => 'Nummerierte Aufzählung',
            'redo' => 'Wiederholen',
            'strike' => 'Durchgestrichen',
            'undo' => 'Rückgängig',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Erstellen',

                    'actions' => [

                        'create' => [
                            'label' => 'Erstellen',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nein',
        ],

        'loading_message' => 'Lädt...',

        'max_items_message' => 'Es können nur :count ausgewählt werden.',

        'no_search_results_message' => 'Die Suche ergab keine Treffer.',

        'placeholder' => 'Wählen Sie eine Option',

        'searching_message' => 'Sucht...',

        'search_prompt' => 'Beginnen Sie mit der Eingabe, um zu suchen...',

    ],

    'tags_input' => [
        'placeholder' => 'Neues Etikett',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Zurück',
            ],

            'next_step' => [
                'label' => 'Weiter',
            ],

        ],

    ],

];
