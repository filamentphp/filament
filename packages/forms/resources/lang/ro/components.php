<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clonează',
            ],

            'add' => [

                'label' => 'Adăugare la :label',

                'modal' => [

                    'heading' => 'Adăugare la :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Adaugă',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Inserează între blocuri',

                'modal' => [

                    'heading' => 'Adăugare la :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Adaugă',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Ștergere',
            ],

            'edit' => [

                'label' => 'Editare',

                'modal' => [

                    'heading' => 'Editare bloc',

                    'actions' => [

                        'save' => [
                            'label' => 'Salvează modificările',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Mutare',
            ],

            'move_down' => [
                'label' => 'Mutare în jos',
            ],

            'move_up' => [
                'label' => 'Mutare în sus',
            ],

            'collapse' => [
                'label' => 'Comprimare',
            ],

            'expand' => [
                'label' => 'Expandare',
            ],

            'collapse_all' => [
                'label' => 'Comprimare toate',
            ],

            'expand_all' => [
                'label' => 'Expandare toate',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deselectează toate',
            ],

            'select_all' => [
                'label' => 'Selectează toate',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Anulare',
                ],

                'drag_crop' => [
                    'label' => 'Mod de glisare "decupare"',
                ],

                'drag_move' => [
                    'label' => 'Mod de glisare "mutare"',
                ],

                'flip_horizontal' => [
                    'label' => 'Întoarce imaginea pe orizontală',
                ],

                'flip_vertical' => [
                    'label' => 'Întoarce imaginea pe verticală',
                ],

                'move_down' => [
                    'label' => 'Mută imaginea în jos',
                ],

                'move_left' => [
                    'label' => 'Mută imaginea în stânga',
                ],

                'move_right' => [
                    'label' => 'Mută imaginea în dreapta',
                ],

                'move_up' => [
                    'label' => 'Mută imaginea în sus',
                ],

                'reset' => [
                    'label' => 'Resetare',
                ],

                'rotate_left' => [
                    'label' => 'Rotește imaginea spre stânga',
                ],

                'rotate_right' => [
                    'label' => 'Rotește imaginea spre dreapta',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Setează raportul de aspect la :ratio',
                ],

                'save' => [
                    'label' => 'Salvare',
                ],

                'zoom_100' => [
                    'label' => 'Mărește imaginea la 100%',
                ],

                'zoom_in' => [
                    'label' => 'Mărește',
                ],

                'zoom_out' => [
                    'label' => 'Micșorează',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Înălțime',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotire',
                    'unit' => 'grade',
                ],

                'width' => [
                    'label' => 'Lățime',
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

                'label' => 'Rapoarte de aspect',

                'no_fixed' => [
                    'label' => 'Liber',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Editarea fișierelor SVG nu este recomandată deoarece poate rezulta în pierderea calității la scalare.\n Sunteți sigur că doriți să continuați?',
                    'disabled' => 'Editarea fișierelor SVG este dezactivată deoarece poate rezulta în pierderea calității la scalare.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Adăugare rând',
            ],

            'delete' => [
                'label' => 'Ștergere rând',
            ],

            'reorder' => [
                'label' => 'Reordonare rând',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Cheie',
            ],

            'value' => [
                'label' => 'Valoare',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Fișierele încărcate trebuie să fie de tipul: :values.',

        'file_attachments_max_size_message' => 'Fișierele încărcate nu trebuie să depășească :max kiloocteți.',

        'tools' => [
            'attach_files' => 'Atașare fișiere',
            'blockquote' => 'Citat',
            'bold' => 'Îngroșat',
            'bullet_list' => 'Listă cu puncte',
            'code_block' => 'Bloc de cod',
            'heading' => 'Titlu',
            'italic' => 'Cursiv',
            'link' => 'Link',
            'ordered_list' => 'Listă numerotată',
            'redo' => 'Refă',
            'strike' => 'Tăiat',
            'table' => 'Tabel',
            'undo' => 'Anulează',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Selectează',

                'actions' => [

                    'select' => [
                        'label' => 'Selectează',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Da',
            'false' => 'Nu',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Adăugare la :label',
            ],

            'add_between' => [
                'label' => 'Inserează',
            ],

            'delete' => [
                'label' => 'Ștergere',
            ],

            'clone' => [
                'label' => 'Clonează',
            ],

            'reorder' => [
                'label' => 'Mutare',
            ],

            'move_down' => [
                'label' => 'Mutare în jos',
            ],

            'move_up' => [
                'label' => 'Mutare în sus',
            ],

            'collapse' => [
                'label' => 'Comprimare',
            ],

            'expand' => [
                'label' => 'Expandare',
            ],

            'collapse_all' => [
                'label' => 'Comprimare toate',
            ],

            'expand_all' => [
                'label' => 'Expandare toate',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Încărcare fișier',

                'modal' => [

                    'heading' => 'Încărcare fișier',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fișier',
                                'existing' => 'Înlocuire fișier',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Text alternativ',
                                'existing' => 'Modificare text alternativ',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Inserează',
                        ],

                        'save' => [
                            'label' => 'Salvează',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Grilă',

                'modal' => [

                    'heading' => 'Grilă',

                    'form' => [

                        'preset' => [

                            'label' => 'Presetare',

                            'placeholder' => 'Niciunul',

                            'options' => [
                                'two' => 'Două',
                                'three' => 'Trei',
                                'four' => 'Patru',
                                'five' => 'Cinci',
                                'two_start_third' => 'Două (Prima treime)',
                                'two_end_third' => 'Două (Ultima treime)',
                                'two_start_fourth' => 'Două (Prima pătrime)',
                                'two_end_fourth' => 'Două (Ultima pătrime)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Coloane',
                        ],

                        'from_breakpoint' => [

                            'label' => 'De la breakpoint',

                            'options' => [
                                'default' => 'Toate',
                                'sm' => 'Mic',
                                'md' => 'Mediu',
                                'lg' => 'Mare',
                                'xl' => 'Extra mare',
                                '2xl' => 'Dublu extra mare',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Două coloane asimetrice',
                        ],

                        'start_span' => [
                            'label' => 'Span început',
                        ],

                        'end_span' => [
                            'label' => 'Span sfârșit',
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
                            'label' => 'Deschide într-o filă nouă',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Culoare text',

                'modal' => [

                    'heading' => 'Culoare text',

                    'form' => [

                        'color' => [
                            'label' => 'Culoare',
                        ],

                        'custom_color' => [
                            'label' => 'Culoare personalizată',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Fișierele încărcate trebuie să fie de tipul: :values.',

        'file_attachments_max_size_message' => 'Fișierele încărcate nu trebuie să depășească :max kiloocteți.',

        'no_merge_tag_search_results_message' => 'Nu s-au găsit etichete de îmbinare.',

        'mentions' => [
            'no_options_message' => 'Nu sunt disponibile opțiuni.',
            'no_search_results_message' => 'Niciun rezultat nu corespunde căutării.',
            'search_prompt' => 'Începeți să tastați pentru a căuta...',
            'searching_message' => 'Se caută...',
        ],

        'tools' => [
            'align_center' => 'Aliniere centru',
            'align_end' => 'Aliniere dreapta',
            'align_justify' => 'Aliniere justificată',
            'align_start' => 'Aliniere stânga',
            'attach_files' => 'Atașare fișiere',
            'blockquote' => 'Citat',
            'bold' => 'Îngroșat',
            'bullet_list' => 'Listă cu puncte',
            'clear_formatting' => 'Curăță formatarea',
            'code' => 'Cod',
            'code_block' => 'Bloc de cod',
            'custom_blocks' => 'Blocuri',
            'details' => 'Detalii',
            'h1' => 'Titlu',
            'h2' => 'Subtitlu',
            'h3' => 'Subtitlu mic',
            'grid' => 'Grilă',
            'grid_delete' => 'Șterge grila',
            'highlight' => 'Evidențiere',
            'horizontal_rule' => 'Linie orizontală',
            'italic' => 'Cursiv',
            'lead' => 'Text principal',
            'link' => 'Link',
            'merge_tags' => 'Etichete de îmbinare',
            'ordered_list' => 'Listă numerotată',
            'redo' => 'Refă',
            'small' => 'Text mic',
            'strike' => 'Tăiat',
            'subscript' => 'Indice',
            'superscript' => 'Exponent',
            'table' => 'Tabel',
            'table_delete' => 'Șterge tabelul',
            'table_add_column_before' => 'Adaugă coloană înainte',
            'table_add_column_after' => 'Adaugă coloană după',
            'table_delete_column' => 'Șterge coloana',
            'table_add_row_before' => 'Adaugă rând deasupra',
            'table_add_row_after' => 'Adaugă rând dedesubt',
            'table_delete_row' => 'Șterge rândul',
            'table_merge_cells' => 'Îmbină celulele',
            'table_split_cell' => 'Separă celula',
            'table_toggle_header_row' => 'Comută rândul antet',
            'table_toggle_header_cell' => 'Comută celula antet',
            'text_color' => 'Culoare text',
            'underline' => 'Subliniat',
            'undo' => 'Anulează',
        ],

        'uploading_file_message' => 'Se încarcă fișierul...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Creează',

                'modal' => [

                    'heading' => 'Creează',

                    'actions' => [

                        'create' => [
                            'label' => 'Creează',
                        ],

                        'create_another' => [
                            'label' => 'Creează și adaugă altul',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Editează',

                'modal' => [

                    'heading' => 'Editare',

                    'actions' => [

                        'save' => [
                            'label' => 'Salvare',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Da',
            'false' => 'Nu',
        ],

        'loading_message' => 'Se încarcă...',

        'max_items_message' => 'Pot fi selectate doar :count.',

        'no_options_message' => 'Nu sunt disponibile opțiuni.',

        'no_search_results_message' => 'Nicio opțiune nu corespunde căutării.',

        'placeholder' => 'Selectați o opțiune',

        'searching_message' => 'Se caută...',

        'search_prompt' => 'Începeți să tastați pentru a căuta...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Ștergere',
            ],

        ],

        'placeholder' => 'Etichetă nouă',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Copiază',
                'message' => 'Copiat',
            ],

            'hide_password' => [
                'label' => 'Ascunde parola',
            ],

            'show_password' => [
                'label' => 'Afișează parola',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Da',
            'false' => 'Nu',
        ],

    ],

];
