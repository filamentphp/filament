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

            'delete' => [
                'label' => 'Esborrar',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Enllaç',
                    'unlink' => 'Elimina l\'enllaç',
                ],

                'label' => 'URL',

                'placeholder' => 'Escriu una adreça URL',

            ],

        ],

        'tools' => [
            'attach_files' => 'Adjuntar fitxers',
            'blockquote' => 'Bloc de cita',
            'bold' => 'Negreta',
            'bullet_list' => 'Llista de vinyetes',
            'code_block' => 'Bloc de codi',
            'h1' => 'Títol',
            'h2' => 'Capçalera',
            'h3' => 'Subtítol',
            'italic' => 'Cursiva',
            'link' => 'Enllaç',
            'ordered_list' => 'Llista numerada',
            'redo' => 'Refer',
            'strike' => 'Ratllat',
            'underline' => 'Subratllat',
            'undo' => 'Desfer',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

        'no_search_results_message' => 'No s\'ha trobat cap opció que coincideixi amb la vostra cerca.',

        'placeholder' => 'Trieu una opció',

        'searching_message' => 'Cercant...',

        'search_prompt' => 'Comenceu a escriure per cercar...',

    ],

    'tags_input' => [
        'placeholder' => 'Nova etiqueta',
    ],

    'text_input' => [

        'actions' => [

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
