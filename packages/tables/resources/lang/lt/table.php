<?php

return [

    'column_toggle' => [

        'heading' => 'Stulpeliai',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'ir dar :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pažymėti/atžymėti visus įrašus masiniam veiksmui.',
        ],

        'bulk_select_record' => [
            'label' => 'Pažymėti/atžymėti įrašą :key masiniam veiksmui.',
        ],

        'search' => [
            'label' => 'Paieška',
            'placeholder' => 'Paieška',
            'indicator' => 'Paieška',
        ],

    ],

    'summary' => [

        'heading' => 'Santrauka',

        'subheadings' => [
            'all' => 'Viso :label',
            'group' => ':group santrauka',
            'page' => 'Šis puslapis',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Vidurkis',
            ],

            'count' => [
                'label' => 'Viso',
            ],

            'sum' => [
                'label' => 'Suma',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Pabaik pertvarkyti įrašus',
        ],

        'enable_reordering' => [
            'label' => 'Pertvarkyti įrašus',
        ],

        'filter' => [
            'label' => 'Filtras',
        ],

        'group' => [
            'label' => 'Grupė',
        ],

        'open_bulk_actions' => [
            'label' => 'Atidaryti veiksmus',
        ],

        'toggle_columns' => [
            'label' => 'Perjungti stulpelius',
        ],

    ],

    'empty' => [

        'heading' => 'Nerasta įrašų',

        'description' => 'Norėdami pradėti, sukurkite :model.',

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

        'heading' => 'Filtrai',

        'indicator' => 'Aktyvūs filtrai',

        'multi_select' => [
            'placeholder' => 'Visi',
        ],

        'select' => [
            'placeholder' => 'Visi',
        ],

        'trashed' => [

            'label' => 'Ištrinti įrašai',

            'only_trashed' => 'Tik ištrinti įrašai',

            'with_trashed' => 'Su ištrintais įrašais',

            'without_trashed' => 'Be ištrintų įrašų',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Grupuoti pagal',
                'placeholder' => 'Grupuoti pagal',
            ],

            'direction' => [

                'label' => 'Grupės kryptis',

                'options' => [
                    'asc' => 'Didėjančia tvarka',
                    'desc' => 'Mažėjančia tvarka',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Vilkite ir paleiskite įrašų rikiavimui.',

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
