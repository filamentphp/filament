<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clona',
            ],

            'add' => [
                'label' => 'Aggiungi a :label',

                'modal' => [

                    'heading' => 'Aggiungi a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Aggiungi',
                        ],

                    ],

                ],
            ],

            'add_between' => [
                'label' => 'Inserisci tra i blocchi',

                'modal' => [

                    'heading' => 'Aggiungi a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Aggiungi',
                        ],

                    ],

                ],
            ],

            'delete' => [
                'label' => 'Elimina',
            ],

            'edit' => [

                'label' => 'Modifica',

                'modal' => [

                    'heading' => 'Modifica blocco',

                    'actions' => [

                        'save' => [
                            'label' => 'Salva modifiche',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Sposta',
            ],

            'move_down' => [
                'label' => 'Sposta in basso',
            ],

            'move_up' => [
                'label' => 'Sposta in alto',
            ],

            'collapse' => [
                'label' => 'Comprimi',
            ],

            'expand' => [
                'label' => 'Espandi',
            ],

            'collapse_all' => [
                'label' => 'Comprimi tutti',
            ],

            'expand_all' => [
                'label' => 'Espandi tutti',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deseleziona tutti',
            ],

            'select_all' => [
                'label' => 'Seleziona tutti',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Annulla',
                ],

                'drag_crop' => [
                    'label' => 'Modalità trascinamento "ritaglio"',
                ],

                'drag_move' => [
                    'label' => 'Modalità trascinamento "sposta"',
                ],

                'flip_horizontal' => [
                    'label' => 'Capovolgi immagine orizzontalmente',
                ],

                'flip_vertical' => [
                    'label' => 'Capovolgi immagine verticalmente',
                ],

                'move_down' => [
                    'label' => 'Sposta immagine in basso',
                ],

                'move_left' => [
                    'label' => 'Sposta immagine a sinistra',
                ],

                'move_right' => [
                    'label' => 'Sposta immagine a destra',
                ],

                'move_up' => [
                    'label' => 'Sposta immagine in alto',
                ],

                'reset' => [
                    'label' => 'Reimposta',
                ],

                'rotate_left' => [
                    'label' => 'Ruota immagine a sinistra',
                ],

                'rotate_right' => [
                    'label' => 'Ruota immagine a destra',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Imposta rapporto a :ratio',
                ],

                'save' => [
                    'label' => 'Salva',
                ],

                'zoom_100' => [
                    'label' => 'Zoom immagine al 100%',
                ],

                'zoom_in' => [
                    'label' => 'Zoom avanti',
                ],

                'zoom_out' => [
                    'label' => 'Zoom indietro',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Altezza',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotazione',
                    'unit' => 'gradi',
                ],

                'width' => [
                    'label' => 'Larghezza',
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

                'label' => 'Rapporti',

                'no_fixed' => [
                    'label' => 'Libero',

                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'La modifica dei file SVG non è consigliata in quanto può comportare una perdita di qualità durante il ridimensionamento.\n Sei sicuro di voler continuare?',
                    'disabled' => 'La modifica dei file SVG è disabilitata in quanto può comportare una perdita di qualità durante il ridimensionamento.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Aggiungi riga',
            ],

            'delete' => [
                'label' => 'Elimina riga',
            ],

            'reorder' => [
                'label' => 'Riordina riga',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Chiave',
            ],

            'value' => [
                'label' => 'Valore',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Allega file',
            'blockquote' => 'Citazione',
            'bold' => 'Grassetto',
            'bullet_list' => 'Elenco puntato',
            'code_block' => 'Blocco di codice',
            'heading' => 'Intestazione',
            'italic' => 'Corsivo',
            'link' => 'Link',
            'ordered_list' => 'Elenco numerato',
            'redo' => 'Ripristina',
            'strike' => 'Barrato',
            'table' => 'Tabella',
            'undo' => 'Annulla',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Si',
            'false' => 'No',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Aggiungi a :label',
            ],

            'add_between' => [
                'label' => 'Inserisci tra',
            ],

            'delete' => [
                'label' => 'Elimina',
            ],

            'clone' => [
                'label' => 'Clona',
            ],

            'reorder' => [
                'label' => 'Sposta',
            ],

            'move_down' => [
                'label' => 'Sposta in basso',
            ],

            'move_up' => [
                'label' => 'Sposta in alto',
            ],

            'collapse' => [
                'label' => 'Comprimi',
            ],

            'expand' => [
                'label' => 'Espandi',
            ],

            'collapse_all' => [
                'label' => 'Comprimi tutti',
            ],

            'expand_all' => [
                'label' => 'Espandi tutti',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Collega',
                    'unlink' => 'Scollega',
                ],

                'label' => 'URL',

                'placeholder' => 'Inserisci un URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Allega file',
            'blockquote' => 'Citazione',
            'bold' => 'Grassetto',
            'bullet_list' => 'Elenco puntato',
            'code_block' => 'Blocco di codice',
            'h1' => 'Titolo',
            'h2' => 'Intestazione',
            'h3' => 'Sottotitolo',
            'italic' => 'Corsivo',
            'link' => 'Link',
            'ordered_list' => 'Elenco numerato',
            'redo' => 'Ripristina',
            'strike' => 'Barrato',
            'underline' => 'Sottolineato',
            'undo' => 'Annulla',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Crea',

                'modal' => [

                    'heading' => 'Crea',

                    'actions' => [

                        'create' => [
                            'label' => 'Crea',
                        ],

                        'create_another' => [
                            'label' => 'Crea & creane un altro',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Modifica',

                'modal' => [

                    'heading' => 'Modifica',

                    'actions' => [

                        'save' => [
                            'label' => 'Salva',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Si',
            'false' => 'No',
        ],

        'loading_message' => 'Caricamento...',

        'max_items_message' => 'Solo :count possono essere selezionati.',

        'no_search_results_message' => 'Nessuna opzione corrisponde alla tua ricerca.',

        'placeholder' => "Seleziona un'opzione",

        'searching_message' => 'Ricerca...',

        'search_prompt' => 'Digita per cercare...',

    ],

    'tags_input' => [
        'placeholder' => 'Nuovo tag',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Nascondi password',
            ],

            'show_password' => [
                'label' => 'Mostra password',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Si',
            'false' => 'No',
        ],

    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Precedente',
            ],

            'next_step' => [
                'label' => 'Successivo',
            ],

        ],

    ],

];
