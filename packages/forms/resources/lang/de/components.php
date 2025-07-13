<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kopieren',
            ],

            'add' => [
                'label' => 'Zu :label hinzufügen',

                'modal' => [

                    'heading' => 'Hinzufügen zu :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Hinzufügen',
                        ],

                    ],

                ],

            ],

            'add_between' => [
                'label' => 'Dazwischen einfügen',

                'modal' => [

                    'heading' => 'Hinzufügen zu :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Hinzufügen',
                        ],

                    ],

                ],
            ],

            'delete' => [
                'label' => 'Löschen',
            ],

            'edit' => [

                'label' => 'Bearbeiten',

                'modal' => [

                    'heading' => 'Block bearbeiten',

                    'actions' => [

                        'save' => [
                            'label' => 'Änderungen speichern',
                        ],

                    ],

                ],

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
                'label' => 'Alle abwählen',
            ],

            'select_all' => [
                'label' => 'Alle auswählen',
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
                    'label' => 'Drag Modus "zuschneiden"',
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
                    'label' => 'Bild nach links drehen',
                ],

                'rotate_right' => [
                    'label' => 'Bild nach rechts drehen',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Seitenverhältnis auf :ratio setzen',
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
                'label' => 'Zeile neu ordnen',
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

        'tools' => [
            'attach_files' => 'Dateien beifügen',
            'blockquote' => 'Zitat',
            'bold' => 'Fett',
            'bullet_list' => 'Aufzählungsliste',
            'code_block' => 'Code Block',
            'heading' => 'Überschrift',
            'italic' => 'Kursiv',
            'link' => 'Link',
            'ordered_list' => 'Nummerierte Liste',
            'redo' => 'Wiederherstellen',
            'strike' => 'Durchgestrichen',
            'table' => 'Tabelle',
            'undo' => 'Rückgängig',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Auswählen',

                'actions' => [

                    'select' => [
                        'label' => 'Auswählen',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nein',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Zu :label hinzufügen',
            ],

            'add_between' => [
                'label' => 'Dazwischen einfügen',
            ],

            'delete' => [
                'label' => 'Löschen',
            ],

            'clone' => [
                'label' => 'Duplizieren',
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

        'actions' => [

            'attach_files' => [

                'label' => 'Datei hochladen',

                'modal' => [

                    'heading' => 'Datei hochladen',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Datei',
                                'existing' => 'Datei ersetzen',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alt-Text',
                                'existing' => 'Alt-Text ändern',
                            ],

                        ],

                    ],

                    'actions' => [

                        'save' => [
                            'label' => 'Speichern',
                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Einfügen',
                        ],

                        'save' => [
                            'label' => 'Speichern',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Bearbeiten',

                'modal' => [

                    'heading' => 'Link',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'In neuem Tab öffnen',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Keine Ergebnisse für Merge-Tags.',

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

        'tools' => [
            'align_center' => 'Zentriert ausrichten',
            'align_end' => 'Rechts ausrichten',
            'align_justify' => 'Blocksatz',
            'align_start' => 'Links ausrichten',
            'attach_files' => 'Dateien anhängen',
            'blockquote' => 'Zitat',
            'bold' => 'Fett',
            'bullet_list' => 'Aufzählungsliste',
            'clear_formatting' => 'Formatierung löschen',
            'code_block' => 'Code Block',
            'custom_blocks' => 'Blöcke',
            'details' => 'Details',
            'h1' => 'Titel',
            'h2' => 'Überschrift',
            'h3' => 'Unterüberschrift',
            'highlight' => 'Hervorheben',
            'horizontal_rule' => 'Horizontale Linie',
            'italic' => 'Kursiv',
            'lead' => 'Einleitungstext',
            'link' => 'Link',
            'merge_tags' => 'Merge-Tags',
            'ordered_list' => 'Nummerierte Aufzählung',
            'redo' => 'Wiederherstellen',
            'small' => 'Kleiner Text',
            'strike' => 'Durchgestrichen',
            'subscript' => 'Tiefgestellt',
            'superscript' => 'Hochgestellt',
            'table' => 'Tabelle',
            'table_delete' => 'Tabelle löschen',
            'table_add_column_before' => 'Spalte davor einfügen',
            'table_add_column_after' => 'Spalte danach einfügen',
            'table_delete_column' => 'Spalte löschen',
            'table_add_row_before' => 'Zeile davor einfügen',
            'table_add_row_after' => 'Zeile danach einfügen',
            'table_delete_row' => 'Zeile löschen',
            'table_merge_cells' => 'Zellen verbinden',
            'table_split_cell' => 'Zelle teilen',
            'table_toggle_header_row' => 'Kopfzeile umschalten',
            'underline' => 'Unterstreichen',
            'undo' => 'Rückgängig',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Erstellen',

                'modal' => [

                    'heading' => 'Erstellen',

                    'actions' => [

                        'create' => [
                            'label' => 'Erstellen',
                        ],

                        'create_another' => [
                            'label' => 'Erstellen & weiteres erstellen',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Bearbeiten',

                'modal' => [

                    'heading' => 'Bearbeiten',

                    'actions' => [

                        'save' => [
                            'label' => 'Speichern',
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
        'placeholder' => 'Neue Kennzeichnung',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Passwort verbergen',
            ],

            'show_password' => [
                'label' => 'Passwort anzeigen',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Ja',
            'false' => 'Nein',
        ],

    ],

];
