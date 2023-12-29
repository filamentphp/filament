<?php

return [

    'column_toggle' => [

        'heading' => 'Columnes',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'i :count més',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Seleccionar/deseleccionar tots els elements per accions massives.',
        ],

        'bulk_select_record' => [
            'label' => 'Seleccionar/deseleccionar element :key per accions massives.',
        ],

        'bulk_select_group' => [
            'label' => 'Seleccionar/deseleccionar grup :title per accions massives.',
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
            'label' => 'Aturar de reorganitzar registres',
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

        'heading' => 'No s\'ha trobat cap registre de :model',

        'description' => 'Crea un :model per començar.',

    ],

    'filters' => [

        'actions' => [

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
                'label' => 'Selecciona :count',
            ],

            'deselect_all' => [
                'label' => 'Deselecciona tot',
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
