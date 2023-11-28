<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clonar',
            ],

            'add' => [
                'label' => 'Añadir a :label',
            ],

            'add_between' => [
                'label' => 'Insertar',
            ],

            'delete' => [
                'label' => 'Borrar',
            ],

            'reorder' => [
                'label' => 'Mover',
            ],

            'move_down' => [
                'label' => 'Bajar',
            ],

            'move_up' => [
                'label' => 'Subir',
            ],

            'collapse' => [
                'label' => 'Contraer',
            ],

            'expand' => [
                'label' => 'Expandir',
            ],

            'collapse_all' => [
                'label' => 'Contraer todo',
            ],

            'expand_all' => [
                'label' => 'Expandir todo',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Deseleccionar todos',
            ],

            'select_all' => [
                'label' => 'Seleccionar todos',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Cancelar',
                ],

                'drag_crop' => [
                    'label' => 'Modo de arrastre "recortar"',
                ],

                'drag_move' => [
                    'label' => 'Modo de arrastre "mover"',
                ],

                'flip_horizontal' => [
                    'label' => 'Voltear imagen horizontalmente',
                ],

                'flip_vertical' => [
                    'label' => 'Voltear imagen verticalmente',
                ],

                'move_down' => [
                    'label' => 'Mover imagen hacia abajo',
                ],

                'move_left' => [
                    'label' => 'Mover imagen a la izquierda',
                ],

                'move_right' => [
                    'label' => 'Mover imagen a la derecha',
                ],

                'move_up' => [
                    'label' => 'Mover imagen hacia arriba',
                ],

                'reset' => [
                    'label' => 'Reiniciar',
                ],

                'rotate_left' => [
                    'label' => 'Girar imagen a la izquierda',
                ],

                'rotate_right' => [
                    'label' => 'Girar imagen a la derecha',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Establecer relación de aspecto a :ratio',
                ],

                'save' => [
                    'label' => 'Guardar',
                ],

                'zoom_100' => [
                    'label' => 'Ampliar imagen al 100%',
                ],

                'zoom_in' => [
                    'label' => 'Acercarse',
                ],

                'zoom_out' => [
                    'label' => 'Alejarse',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Altura',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotación',
                    'unit' => 'grados',
                ],

                'width' => [
                    'label' => 'Ancho',
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

                'label' => 'Relaciones de aspecto',

                'no_fixed' => [
                    'label' => 'Libre',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'No se recomienda editar archivos SVG ya que puede provocar una pérdida de calidad al escalar.\n ¿Está seguro de que desea continuar?',
                    'disabled' => 'La edición de archivos SVG está deshabilitada ya que puede provocar una pérdida de calidad al escalar.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Añadir fila',
            ],

            'delete' => [
                'label' => 'Eliminar fila',
            ],

            'reorder' => [
                'label' => 'Reordenar fila',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Clave',
            ],

            'value' => [
                'label' => 'Valor',
            ],

        ],

    ],

    'markdown_editor' => [

        'toolbar_buttons' => [
            'attach_files' => 'Adjuntar archivos',
            'blockquote' => 'Cita',
            'bold' => 'Negrita',
            'bullet_list' => 'Viñetas',
            'code_block' => 'Bloque de código',
            'heading' => 'Encabezado',
            'italic' => 'Cursiva',
            'link' => 'Enlace',
            'ordered_list' => 'Lista numerada',
            'strike' => 'Tachado',
            'redo' => 'Rehacer',
            'table' => 'Tabla',
            'undo' => 'Deshacer',
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
                'label' => 'Añadir a :label',
            ],

            'add_between' => [
                'label' => 'Insertar entre',
            ],

            'delete' => [
                'label' => 'Borrar',
            ],

            'reorder' => [
                'label' => 'Mover',
            ],

            'clone' => [
                'label' => 'Clonar',
            ],

            'move_down' => [
                'label' => 'Bajar',
            ],

            'move_up' => [
                'label' => 'Subir',
            ],

            'collapse' => [
                'label' => 'Contraer',
            ],

            'expand' => [
                'label' => 'Expandir',
            ],

            'collapse_all' => [
                'label' => 'Contraer todo',
            ],

            'expand_all' => [
                'label' => 'Expandir todo',
            ],

        ],

    ],

    'rich_editor' => [

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Enlace',
                    'unlink' => 'Quitar enlace',
                ],

                'label' => 'URL',

                'placeholder' => 'Teclee un enlace URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Adjuntar archivos',
            'blockquote' => 'Cita',
            'bold' => 'Negrita',
            'bullet_list' => 'Viñetas',
            'code_block' => 'Bloque de código',
            'h1' => 'Título',
            'h2' => 'Encabezado',
            'h3' => 'Subencabezado',
            'italic' => 'Cursiva',
            'link' => 'Enlace',
            'ordered_list' => 'Lista numerada',
            'redo' => 'Rehacer',
            'strike' => 'Tachar',
            'underline' => 'Subrayar',
            'undo' => 'Deshacer',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Nuevo',

                    'actions' => [

                        'create' => [
                            'label' => 'Crear',
                        ],
                        'create_another' => [
                            'label' => 'Crear y crear otro',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'modal' => [

                    'heading' => 'Editar',

                    'actions' => [

                        'save' => [
                            'label' => 'Guardar',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Sí',
            'false' => 'No',
        ],

        'loading_message' => 'Cargando...',

        'max_items_message' => 'Solo :count pueden ser seleccionados.',

        'no_search_results_message' => 'No se encontraron coincidencias con su búsqueda.',

        'placeholder' => 'Seleccione una opción',

        'searching_message' => 'Buscando...',

        'search_prompt' => 'Teclee para buscar...',

    ],

    'tags_input' => [
        'placeholder' => 'Nueva etiqueta',
    ],

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Anterior',
            ],

            'next_step' => [
                'label' => 'Siguiente',
            ],

        ],

    ],

];
