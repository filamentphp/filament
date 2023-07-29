<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'ir :count daugiau',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Paieška',
            'placeholder' => 'Paieška',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Pertvarkyti įrašus',
        ],

        'filter' => [
            'label' => 'Filtras',
        ],

        'open_bulk_actions' => [
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

        'actions' => [

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

        'selected_count' => '1 įrašas pasirinktas|:count įrašai pasirinkti',

        'actions' => [

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
