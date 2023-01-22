<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'ir :count daugiau',
        ],

        'messages' => [
            'copied' => 'Nukopijuota',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Paieška',
            'placeholder' => 'Paieška',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => 'Rodomi nuo :first iki :last rezultatai iš :total',

        'fields' => [

            'records_per_page' => [

                'label' => 'per puslapį',

                'options' => [
                    'all' => 'Viską',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Eiti į puslapį :page',
            ],

            'next' => [
                'label' => 'Kitas',
            ],

            'previous' => [
                'label' => 'Buvęs',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Pertvarkyti įrašus',
        ],

        'filter' => [
            'label' => 'Filtras',
        ],

        'open_actions' => [
            'label' => 'Atidaryti veiksmus',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [
        'heading' => 'Nerasta įrašų',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Pašalinti filtrą',
            ],

            'remove_all' => [
                'label' => 'Pašalinti visus filtrus',
                'tooltip' => 'Pašalinti visus filtrus',
            ],

            'reset' => [
                'label' => 'Nustatyti filtrus iš naujo',
            ],

        ],

        'indicator' => 'Aktyvūs filtrai',

        'multi_select' => [
            'placeholder' => 'Visi',
        ],

        'select' => [
            'placeholder' => 'Visi',
        ],

        'trashed' => [

            'label' => 'Ištrinti įrašaų',

            'only_trashed' => 'Tik ištrinti įrašai',

            'with_trashed' => 'Su ištrintais įrašais',

            'without_trashed' => 'Be ištrintų įrašų',

        ],

    ],

    'reorder_indicator' => 'Vilk ir paleisk pakeisti įrašų eiliškumui.',

    'selection_indicator' => [

        'selected_count' => '1 įrašas pasirinktas.|:count įrašai pasirinkti.',

        'buttons' => [

            'select_all' => [
                'label' => 'Pažymėti visus :count',
            ],

            'deselect_all' => [
                'label' => 'Atžymėti visus',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Rūšiuoti pagal',
            ],

            'direction' => [

                'label' => 'Rūšiavimo kryptis',

                'options' => [
                    'asc' => 'Didėjimo tvarka',
                    'desc' => 'Mažėjimo tvarka',
                ],

            ],

        ],

    ],

];
