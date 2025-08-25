<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Clonar',
            ],

            'add' => [

                'label' => 'Adicionar a :label',

                'modal' => [

                    'heading' => 'Adicionar a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Adicionar',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Inserir entre blocos',

                'modal' => [

                    'heading' => 'Adicionar a :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Adicionar',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Eliminar',
            ],

            'edit' => [

                'label' => 'Editar',

                'modal' => [

                    'heading' => 'Editar bloco',

                    'actions' => [

                        'save' => [
                            'label' => 'Guardar alterações',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Mover',
            ],

            'move_down' => [
                'label' => 'Mover para baixo',
            ],

            'move_up' => [
                'label' => 'Mover para cima',
            ],

            'collapse' => [
                'label' => 'Recolher',
            ],

            'expand' => [
                'label' => 'Expandir',
            ],

            'collapse_all' => [
                'label' => 'Recolher todos',
            ],

            'expand_all' => [
                'label' => 'Expandir todos',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Desmarcar todos',
            ],

            'select_all' => [
                'label' => 'Marcar todos',
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
                    'label' => 'Modo de arrastar "cortar"',
                ],

                'drag_move' => [
                    'label' => 'Modo de arrastar "mover"',
                ],

                'flip_horizontal' => [
                    'label' => 'Inverter imagem horizontalmente',
                ],

                'flip_vertical' => [
                    'label' => 'Inverter imagem verticalmente',
                ],

                'move_down' => [
                    'label' => 'Mover imagem para baixo',
                ],

                'move_left' => [
                    'label' => 'Mover imagem para a esquerda',
                ],

                'move_right' => [
                    'label' => 'Mover imagem para a direita',
                ],

                'move_up' => [
                    'label' => 'Mover imagem para cima',
                ],

                'reset' => [
                    'label' => 'Repôr',
                ],

                'rotate_left' => [
                    'label' => 'Rodar imagem para a esquerda',
                ],

                'rotate_right' => [
                    'label' => 'Rodar imagem para a direita',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Definir proporção para :ratio',
                ],

                'save' => [
                    'label' => 'Guardar',
                ],

                'zoom_100' => [
                    'label' => 'Ampliar imagem para 100%',
                ],

                'zoom_in' => [
                    'label' => 'Mais zoom',
                ],

                'zoom_out' => [
                    'label' => 'Menos zoom',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Altura',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Rotação',
                    'unit' => 'graus',
                ],

                'width' => [
                    'label' => 'Largura',
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

                'label' => 'Proporções',

                'no_fixed' => [
                    'label' => 'Livre',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Não é recomendado editar ficheiros SVG, pois pode resultar em perda de qualidade ao redimensionar.\n Tem a certeza de que deseja prosseguir?',
                    'disabled' => 'A edição de ficheiros SVG está desactivada, pois pode resultar em perda de qualidade ao redimensionar.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Adicionar linha',
            ],

            'delete' => [
                'label' => 'Eliminar linha',
            ],

            'reorder' => [
                'label' => 'Reordenar linha',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Chave',
            ],

            'value' => [
                'label' => 'Valor',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Anexar ficheiros',
            'blockquote' => 'Bloco de citação',
            'bold' => 'Negrito',
            'bullet_list' => 'Lista',
            'code_block' => 'Bloco de código',
            'heading' => 'Cabeçalho',
            'italic' => 'Itálico',
            'link' => 'Hiperligação',
            'ordered_list' => 'Lista numerada',
            'redo' => 'Refazer',
            'strike' => 'Rasurado',
            'table' => 'Tabela',
            'undo' => 'Desfazer',
        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Sim',
            'false' => 'Não',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Adicionar a :label',
            ],

            'add_between' => [
                'label' => 'Adicionar entre',
            ],

            'delete' => [
                'label' => 'Eliminar',
            ],

            'clone' => [
                'label' => 'Clonar',
            ],

            'reorder' => [
                'label' => 'Mover',
            ],

            'move_down' => [
                'label' => 'Mover para baixo',
            ],

            'move_up' => [
                'label' => 'Mover para cima',
            ],

            'collapse' => [
                'label' => 'Recolher',
            ],

            'expand' => [
                'label' => 'Expandir',
            ],

            'collapse_all' => [
                'label' => 'Recolher todos',
            ],

            'expand_all' => [
                'label' => 'Expandir todos',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Carregar ficheiro',

                'modal' => [

                    'heading' => 'Carregar ficheiro',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Ficheiro',
                                'existing' => 'Substituir ficheiro',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Texto alternativo',
                                'existing' => 'Alterar texto alternativo',
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
                            'label' => 'Guardar',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Editar',

                'modal' => [

                    'heading' => 'Hiperligação',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Abrir numa nova aba',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Nenhuma tag dinâmica encontrada.',

        'tools' => [
            'align_center' => 'Alinhar ao centro',
            'align_end' => 'Alinhar à direita',
            'align_justify' => 'Justificar',
            'align_start' => 'Alinhar à esquerda',
            'attach_files' => 'Anexar ficheiros',
            'blockquote' => 'Bloco de citação',
            'bold' => 'Negrito',
            'bullet_list' => 'Lista',
            'clear_formatting' => 'Limpar formatação',
            'code_block' => 'Bloco de código',
            'custom_blocks' => 'Blocos',
            'details' => 'Detalhes',
            'h1' => 'Título',
            'h2' => 'Cabeçalho',
            'h3' => 'Subtítulo',
            'highlight' => 'Destacar',
            'horizontal_rule' => 'Linha horizontal',
            'italic' => 'Itálico',
            'lead' => 'Texto de destaque',
            'link' => 'Hiperligação',
            'merge_tags' => 'Tags dinâmicas',
            'ordered_list' => 'Lista numerada',
            'redo' => 'Refazer',
            'small' => 'Texto pequeno',
            'strike' => 'Rasurado',
            'subscript' => 'Subscrito',
            'superscript' => 'Sobrescrito',
            'table' => 'Tabela',
            'table_delete' => 'Eliminar tabela',
            'table_add_column_before' => 'Adicionar coluna antes',
            'table_add_column_after' => 'Adicionar coluna depois',
            'table_delete_column' => 'Eliminar coluna',
            'table_add_row_before' => 'Adicionar linha acima',
            'table_add_row_after' => 'Adicionar linha abaixo',
            'table_delete_row' => 'Eliminar linha',
            'table_merge_cells' => 'Unir células',
            'table_split_cell' => 'Dividir célula',
            'table_toggle_header_row' => 'Alternar linha de cabeçalho',
            'underline' => 'Sublinhado',
            'undo' => 'Desfazer',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Criar',

                'modal' => [

                    'heading' => 'Criar',

                    'actions' => [

                        'create' => [
                            'label' => 'Criar',
                        ],

                        'create_another' => [
                            'label' => 'Criar e criar outro',
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
                            'label' => 'Guardar',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Sim',
            'false' => 'Não',
        ],

        'loading_message' => 'A carregar...',

        'max_items_message' => 'Apenas :count item pode ser seleccionado.|Apenas :count itens podem ser seleccionados.',

        'no_search_results_message' => 'Nenhuma opção corresponde à sua pesquisa.',

        'placeholder' => 'Seleccione uma opção',

        'searching_message' => 'A pesquisar...',

        'search_prompt' => 'Comece a escrever para pesquisar...',

    ],

    'tags_input' => [
        'placeholder' => 'Nova etiqueta',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Esconder a palavra-passe',
            ],

            'show_password' => [
                'label' => 'Mostrar a palavra-passe',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Sim',
            'false' => 'Não',
        ],

    ],

];
