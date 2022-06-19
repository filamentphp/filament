<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Búsqueda',
            'placeholder' => 'Buscar',
        ],

    ],

    'pagination' => [

        'label' => 'Navegación de paginación',

        'overview' => 'Mostrando :first a :last de :total resultados',

        'fields' => [

            'records_per_page' => [
                'label' => 'por página',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ir a la página :page',
            ],

            'next' => [
                'label' => 'Siguiente',
            ],

            'previous' => [
                'label' => 'Anterior',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filtro',
        ],

        'open_actions' => [
            'label' => 'Abrir acciones',
        ],

        'toggle_columns' => [
            'label' => 'Alternar columnas',
        ],

    ],


    'bulk_actions' => [

        'force_delete' => [

            'label' => 'Forzar eliminación',

            'messages' => [
                'deleted' => 'Registros eliminados',
            ],

        ],

        'restore' => [

            'label' => 'Restaurar',

            'messages' => [
                'restored' => 'Registros restaurados',
            ],

        ],

    ],

    'empty' => [
        'heading' => 'No se encontraron registros',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Resetea los filtros',
            ],

            'close' => [
                'label' => 'Cerrar',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Todos',
        ],

        'select' => [
            'placeholder' => 'Todos',
        ],

        'trashed' => [

            'label' => 'Registros eliminados',

            'only_trashed' => 'Solo registros eliminados',

            'with_trashed' => 'Con resgistros eliminados',

            'without_trashed' => 'Sin registros eliminados',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 registro seleccionado.|:count registros seleccionados.',

        'buttons' => [

            'select_all' => [
                'label' => 'Selecciona los :count',
            ],

            'deselect_all' => [
                'label' => 'Deselecciona todos',
            ],

        ],

    ],

];
