<?php

return [

    'column_manager' => [

        'heading' => 'Columnas',

        'actions' => [

            'apply' => [
                'label' => 'Aplicar columnas',
            ],

            'reset' => [
                'label' => 'Resetear las columnas',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Acción|Acciones',
        ],

        'select' => [

            'loading_message' => 'Cargando...',

            'no_options_message' => 'No hay opciones disponibles.',

            'no_search_results_message' => 'No hay opciones que coincidan con su búsqueda.',

            'placeholder' => 'Seleccione una opción',

            'searching_message' => 'Buscando...',

            'search_prompt' => 'Empiece a escribir para buscar...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostrar :count menos',
                'expand_list' => 'Mostrar :count más',
            ],

            'more_list_items' => 'y :count más',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleccionar/deseleccionar todos los elementos para las acciones masivas.',
        ],

        'bulk_select_record' => [
            'label' => 'Seleccionar/deseleccionar el elemento :key para las acciones masivas.',
        ],

        'bulk_select_group' => [
            'label' => 'Seleccionar/deseleccionar grupo :title para acciones masivas.',
        ],

        'search' => [
            'label' => 'Búsqueda',
            'placeholder' => 'Buscar',
            'indicator' => 'Buscar',
        ],

    ],

    'summary' => [

        'heading' => 'Resumen',

        'subheadings' => [
            'all' => 'Todos :label',
            'group' => 'resumen del :group',
            'page' => 'Esta página',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Media',
            ],

            'count' => [
                'label' => 'Recuento',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Terminar de reordenar registros',
        ],

        'enable_reordering' => [
            'label' => 'Reordenar registros',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Grupo',
        ],

        'open_bulk_actions' => [
            'label' => 'Abrir acciones',
        ],

        'column_manager' => [
            'label' => 'Alternar columnas',
        ],

    ],

    'empty' => [

        'heading' => 'No se encontraron registros',

        'description' => 'Cree un :model para empezar.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplicar filtros',
            ],

            'remove' => [
                'label' => 'Quitar filtro',
            ],

            'remove_all' => [
                'label' => 'Quitar todos los filtros',
                'tooltip' => 'Quitar todos los filtros',
            ],

            'reset' => [
                'label' => 'Resetear los filtros',
            ],

        ],

        'heading' => 'Filtros',

        'indicator' => 'Filtros activos',

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [

            'placeholder' => 'Todos',

            'relationship' => [
                'empty_option_label' => 'Ninguno',
            ],

        ],

        'trashed' => [

            'label' => 'Registros eliminados',

            'only_trashed' => 'Solo registros eliminados',

            'with_trashed' => 'Con registros eliminados',

            'without_trashed' => 'Sin registros eliminados',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Agrupar por',
            ],

            'direction' => [

                'label' => 'Dirección de grupo',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arrastrar los registros en el orden.',

    'selection_indicator' => [

        'selected_count' => '1 registro seleccionado|:count registros seleccionados',

        'actions' => [

            'select_all' => [
                'label' => 'Selecciona todos :count',
            ],

            'deselect_all' => [
                'label' => 'Deselecciona todos',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenar por',
            ],

            'direction' => [

                'label' => 'Dirección del orden',

                'options' => [
                    'asc' => 'Ascendente',
                    'desc' => 'Descendente',
                ],

            ],

        ],

    ],

    'default_model_label' => 'registro',

];
