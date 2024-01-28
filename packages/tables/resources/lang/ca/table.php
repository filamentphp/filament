<?php

return [

    'column_toggle' => [

        'heading' => 'Columnes',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Mostrar :count menys',
                'expand_list' => 'Mostrar :count més',
            ],

            'more_list_items' => 'i :count més',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleccionar/desseleccionar tots els elements per les accions massives.',
        ],

        'bulk_select_record' => [
            'label' => 'Seleccionar/desseleccionar l\'element :key per accions massives.',
        ],

        'bulk_select_group' => [
            'label' => 'Seleccionar/desseleccionar grup :title per accions massives.',
        ],

        'search' => [
            'label' => 'Cerca',
            'placeholder' => 'Cercar',
            'indicator' => 'Cercar',
        ],

    ],

    'summary' => [

        'heading' => 'Resum',

        'subheadings' => [
            'all' => 'Tots :label',
            'group' => 'Resum del :group',
            'page' => 'Aquesta pàgina',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Mitja',
            ],

            'count' => [
                'label' => 'Recompte',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Acabar de reorganitzar registres',
        ],

        'enable_reordering' => [
            'label' => 'Reorganitzar registres',
        ],

        'filter' => [
            'label' => 'Filtrar',
        ],

        'group' => [
            'label' => 'Grup',
        ],

        'open_bulk_actions' => [
            'label' => 'Accions massives',
        ],

        'toggle_columns' => [
            'label' => 'Alternar columnes',
        ],

    ],

    'empty' => [

        'heading' => 'No s\'han trobat registres',

        'description' => 'Crea un :model per començar.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Aplicar filtres',
            ],

            'remove' => [
                'label' => 'Esborrar filtre',
            ],

            'remove_all' => [
                'label' => 'Esborrar tots els filtres',
                'tooltip' => 'Esborrar tots els filtres',
            ],

            'reset' => [
                'label' => 'Restablir',
            ],

        ],

        'heading' => 'Filtres',

        'indicator' => 'Filtres actius',

        'multi_select' => [
            'placeholder' => 'Tots',
        ],

        'select' => [
            'placeholder' => 'Tots',
        ],

        'trashed' => [

            'label' => 'Registres esborrats',

            'only_trashed' => 'Només registres esborrats',

            'with_trashed' => 'Amb registres esborrats',

            'without_trashed' => 'Sense registres esborrats',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Agrupar per',
                'placeholder' => 'Agrupar per',
            ],

            'direction' => [

                'label' => 'Sentit',

                'options' => [
                    'asc' => 'Ascendent',
                    'desc' => 'Descendent',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Arrossega i deixa anar els registres per ordenar-los.',

    'selection_indicator' => [

        'selected_count' => '1 registre seleccionat|:count registres seleccionats',

        'actions' => [

            'select_all' => [
                'label' => 'Selecciona\'ls tots :count',
            ],

            'deselect_all' => [
                'label' => 'Desselecciona\'ls tots',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ordenar per',
            ],

            'direction' => [

                'label' => 'Sentit',

                'options' => [
                    'asc' => 'Ascendent',
                    'desc' => 'Descendent',
                ],

            ],

        ],

    ],

];
