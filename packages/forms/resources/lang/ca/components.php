<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clonar',
            ],

            'add' => [

                'label' => 'Afegir a :label',

                'modal' => [

                    'heading' => 'Afegeix a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Afegeix',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Inserir entre blocs',

                'modal' => [

                    'heading' => 'Afegir a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Afegir',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Esborrar',
            ],

            'edit' => [

                'label' => 'Edita',

                'modal' => [

                    'heading' => 'Edita bloc',

                    'actions' => [

                        'save' => [
                            'label' => 'Desa els canvis',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Moure',
            ],

            'move_down' => [
                'label' => 'Moure cap avall',
            ],

            'move_up' => [
                'label' => 'Moure cap amunt',
            ],

            'collapse' => [
                'label' => 'Replegar',
            ],

            'expand' => [
                'label' => 'Ampliar',
            ],

            'collapse_all' => [
                'label' => 'Replegar tots',
            ],

            'expand_all' => [
                'label' => 'Ampliar tots',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Desseleccionar tots',
            ],

            'select_all' => [
                'label' => 'Seleccionar tots',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Cancel·lar',
                ],

                'drag_crop' => [
                    'label' => 'Mode d\'arrossegament "retallar"',
                ],

                'drag_move' => [
                    'label' => 'Mode d\'arrossegament "moure"',
                ],

                'flip_horizontal' => [
                    'label' => 'Girar imatge horitzontalment',
                ],

                'flip_vertical' => [
                    'label' => 'Girar imatge verticalment',
                ],

                'move_down' => [
                    'label' => 'Mou la imatge cap avall',
                ],

                'move_left' => [
                    'label' => 'Mou la imatge cap a l\'esquerra',
                ],

                'move_right' => [
                    'label' => 'Mou la imatge cap a la dreta',
                ],

                'move_up' => [
                    'label' => 'Mou la imatge cap amunt',
                ],

                'reset' => [
                    'label' => 'Restablir',
                ],

                'rotate_left' => [
                    'label' => 'Rota la imatge cap a l\'esquerra',
                ],

                'rotate_right' => [
                    'label' => 'Rota la imatge cap a la dreta',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Estableix la relació d\'aspecte a :ratio',
                ],

                'save' => [
                    'label' => 'Desar',
                ],

                'zoom_100' => [
                    'label' => 'Amplia la imatge a 100%',
                ],

                'zoom_in' => [
                    'label' => 'Ampliar el zoom',
                ],

                'zoom_out' => [
                    'label' => 'Reduir el zoom',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Altura',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotació',
                    'unit' => 'graus',
                ],

                'width' => [
                    'label' => 'Amplada',
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

                'label' => 'Relacions d\'aspecte',

                'no_fixed' => [
                    'label' => 'Lliure',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'No es recomana editar fitxers SVG ja que pot provocar una pèrdua de qualitat en escalar-los.\n Esteu segur que voleu continuar?',
                    'disabled' => 'L\'edició de fitxers SVG està desactivada ja que pot provocar una pèrdua de qualitat en escalar-los.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Afegir fila',
            ],

            'delete' => [
                'label' => 'Esborrar fila',
            ],

            'reorder' => [
                'label' => 'Reordenar fila',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Clau',
            ],

            'value' => [
                'label' => 'Valor',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Els fitxers pujats han de ser del tipus: :values.',

        'file_attachments_max_size_message' => 'Els fitxers pujats no poden superar els :max kilobytes.',

        'tools' => [
            'attach_files' => 'Adjuntar fitxers',
            'blockquote' => 'Cita de bloc',
            'bold' => 'Negreta',
            'bullet_list' => 'Llista de vinyetes',
            'code_block' => 'Bloc de codi',
            'heading' => 'Encapçalament',
            'italic' => 'Cursiva',
            'link' => 'Enllaç',
            'ordered_list' => 'Llista numerada',
            'redo' => 'Refer',
            'strike' => 'Ratllat',
            'table' => 'Taula',
            'undo' => 'Desfer',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Seleccionar',

                'actions' => [

                    'select' => [
                        'label' => 'Seleccionar',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Sí',
            'false' => 'No',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Afegir a :label',
            ],

            'add_between' => [
                'label' => 'Inserir entre',
            ],

            'delete' => [
                'label' => 'Esborrar',
            ],

            'clone' => [
                'label' => 'Clonar',
            ],

            'reorder' => [
                'label' => 'Moure',
            ],

            'move_down' => [
                'label' => 'Moure cap avall',
            ],

            'move_up' => [
                'label' => 'Moure cap amunt',
            ],

            'collapse' => [
                'label' => 'Replegar',
            ],

            'expand' => [
                'label' => 'Ampliar',
            ],

            'collapse_all' => [
                'label' => 'Replegar tots',
            ],

            'expand_all' => [
                'label' => 'Ampliar tots',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Pujar fitxer',

                'modal' => [

                    'heading' => 'Pujar fitxer',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Fitxer',
                                'existing' => 'Substituir fitxer',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Text alternatiu',
                                'existing' => 'Canviar text alternatiu',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Inserir',
                        ],

                        'save' => [
                            'label' => 'Desar',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Quadrícula',

                'modal' => [

                    'heading' => 'Quadrícula',

                    'form' => [

                        'preset' => [

                            'label' => 'Predefinit',

                            'placeholder' => 'Cap',

                            'options' => [
                                'two' => 'Dos',
                                'three' => 'Tres',
                                'four' => 'Quatre',
                                'five' => 'Cinc',
                                'two_start_third' => 'Dos (inici terç)',
                                'two_end_third' => 'Dos (final terç)',
                                'two_start_fourth' => 'Dos (inici quart)',
                                'two_end_fourth' => 'Dos (final quart)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Columnes',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Des del punt de ruptura',

                            'options' => [
                                'default' => 'Tot',
                                'sm' => 'Petit',
                                'md' => 'Mitjà',
                                'lg' => 'Gran',
                                'xl' => 'Extra gran',
                                '2xl' => 'Doble extra gran',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'Dues columnes asimètriques',
                        ],

                        'start_span' => [
                            'label' => 'Amplada inicial',
                        ],

                        'end_span' => [
                            'label' => 'Amplada final',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Enllaç',

                'modal' => [

                    'heading' => 'Enllaç',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Obrir en una pestanya nova',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Color del text',

                'modal' => [

                    'heading' => 'Color del text',

                    'form' => [

                        'color' => [
                            'label' => 'Color',
                        ],

                        'custom_color' => [
                            'label' => 'Color personalitzat',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Els fitxers pujats han de ser del tipus: :values.',

        'file_attachments_max_size_message' => 'Els fitxers pujats no poden superar els :max kilobytes.',

        'no_merge_tag_search_results_message' => 'No s\'han trobat resultats d\'etiquetes de combinació.',

        'mentions' => [
            'no_options_message' => 'No hi ha opcions disponibles.',
            'no_search_results_message' => 'Cap resultat coincideix amb la vostra cerca.',
            'search_prompt' => 'Comenceu a escriure per cercar...',
            'searching_message' => 'Cercant...',
        ],

        'tools' => [
            'align_center' => 'Alinear al centre',
            'align_end' => 'Alinear al final',
            'align_justify' => 'Justificar',
            'align_start' => 'Alinear a l\'inici',
            'attach_files' => 'Adjuntar fitxers',
            'blockquote' => 'Bloc de cita',
            'bold' => 'Negreta',
            'bullet_list' => 'Llista de vinyetes',
            'clear_formatting' => 'Netejar format',
            'code' => 'Codi',
            'code_block' => 'Bloc de codi',
            'custom_blocks' => 'Blocs',
            'details' => 'Detalls',
            'h1' => 'Títol',
            'h2' => 'Capçalera',
            'h3' => 'Subtítol',
            'grid' => 'Quadrícula',
            'grid_delete' => 'Esborrar quadrícula',
            'highlight' => 'Ressaltar',
            'horizontal_rule' => 'Línia horitzontal',
            'italic' => 'Cursiva',
            'lead' => 'Text destacat',
            'link' => 'Enllaç',
            'merge_tags' => 'Etiquetes de combinació',
            'ordered_list' => 'Llista numerada',
            'redo' => 'Refer',
            'small' => 'Text petit',
            'strike' => 'Ratllat',
            'subscript' => 'Subíndex',
            'superscript' => 'Superíndex',
            'table' => 'Taula',
            'table_delete' => 'Esborrar taula',
            'table_add_column_before' => 'Afegir columna abans',
            'table_add_column_after' => 'Afegir columna després',
            'table_delete_column' => 'Esborrar columna',
            'table_add_row_before' => 'Afegir fila a sobre',
            'table_add_row_after' => 'Afegir fila a sota',
            'table_delete_row' => 'Esborrar fila',
            'table_merge_cells' => 'Combinar cel·les',
            'table_split_cell' => 'Dividir cel·la',
            'table_toggle_header_row' => 'Commutar fila de capçalera',
            'table_toggle_header_cell' => 'Commutar cel·la de capçalera',
            'text_color' => 'Color del text',
            'underline' => 'Subratllat',
            'undo' => 'Desfer',
        ],

        'uploading_file_message' => 'Pujant fitxer...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Crear',

                'modal' => [

                    'heading' => 'Nou',

                    'actions' => [

                        'create' => [
                            'label' => 'Crear',
                        ],

                        'create_another' => [
                            'label' => 'Crear i crear-ne un altre',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Editar',

                'modal' => [

                    'heading' => 'Editar',

                    'actions' => [

                        'save' => [
                            'label' => 'Desar',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Sí',
            'false' => 'No',
        ],

        'loading_message' => 'Carregant...',

        'max_items_message' => 'Només :count poden ser seleccionats.',

        'no_options_message' => 'No hi ha opcions disponibles.',

        'no_search_results_message' => 'No s\'ha trobat cap opció que coincideixi amb la vostra cerca.',

        'placeholder' => 'Trieu una opció',

        'searching_message' => 'Cercant...',

        'search_prompt' => 'Comenceu a escriure per cercar...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Esborrar',
            ],

        ],

        'placeholder' => 'Nova etiqueta',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Copiar',
                'message' => 'Copiat',
            ],

            'hide_password' => [
                'label' => 'Ocultar contrasenya',
            ],

            'show_password' => [
                'label' => 'Mostrar contrasenya',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Sí',
            'false' => 'No',
        ],

    ],

];
