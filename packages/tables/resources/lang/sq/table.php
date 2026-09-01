<?php

return [

    'column_manager' => [

        'heading' => 'Kolonat',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'dhe :count më shumë',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Zgjidh/çzgjidh të gjithë artikujt për veprime në masë.',
        ],

        'bulk_select_record' => [
            'label' => 'Zgjidh/çzgjidh artikullin :key për veprime në masë.',
        ],

        'search' => [
            'label' => 'Kërko',
            'placeholder' => 'Kërko',
            'indicator' => 'Kërko',
        ],

    ],

    'summary' => [

        'heading' => 'Përmbledhje',

        'subheadings' => [
            'all' => 'Të gjithë :label',
            'group' => ':group përmbledhje',
            'page' => 'Kjo faqe',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Mesatare',
            ],

            'count' => [
                'label' => 'Numëro',
            ],

            'sum' => [
                'label' => 'Mblidh',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Përfundo rirenditjen e regjistrave',
        ],

        'enable_reordering' => [
            'label' => 'Rirenditni të dhënat',
        ],

        'filter' => [
            'label' => 'Filtro',
        ],

        'group' => [
            'label' => 'Grupi',
        ],

        'open_bulk_actions' => [
            'label' => 'Veprime me shumicë',
        ],

        'column_manager' => [
            'label' => 'Ndrysho kolonat',
        ],

    ],

    'empty' => [

        'heading' => 'S\'ka :model',

        'description' => 'Krijo një :model për të filluar.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Hiq filtrin',
            ],

            'remove_all' => [
                'label' => 'Hiqni të gjithë filtrat',
                'tooltip' => 'Hiqni të gjithë filtrat',
            ],

            'reset' => [
                'label' => 'Rivendos',
            ],

        ],

        'heading' => 'Filtrat',

        'indicator' => 'Filtrat aktivë',

        'multi_select' => [
            'placeholder' => 'Të gjitha',
        ],

        'select' => [
            'placeholder' => 'Të gjitha',
        ],

        'trashed' => [

            'label' => 'Të dhënat e fshira',

            'only_trashed' => 'Vetëm të dhënat e fshira',

            'with_trashed' => 'Me të dhëna të fshira',

            'without_trashed' => 'Pa të dhëna të fshira',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupo sipas',
                'placeholder' => 'Grupo sipas',
            ],

            'direction' => [

                'label' => 'Drejtimi i grupit',

                'options' => [
                    'asc' => 'Në ngjitje',
                    'desc' => 'Duke zbritur',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Tërhiqni dhe lëshoni të dhënat sipas renditjes',

    'selection_indicator' => [

        'selected_count' => '1 regjistrim i zgjedhur|:count regjistrime të zgjedhura',

        'actions' => [

            'select_all' => [
                'label' => 'Zgjidhni të gjitha :count',
            ],

            'deselect_all' => [
                'label' => 'Çzgjidh të gjitha',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ndaj sipas',
            ],

            'direction' => [

                'label' => 'Rendit drejtimin',

                'options' => [
                    'asc' => 'Në ngjitje',
                    'desc' => 'Duke zbritur',
                ],

            ],

        ],

    ],

];
