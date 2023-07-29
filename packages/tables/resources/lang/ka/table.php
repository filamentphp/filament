<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'და კიდევ :count',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'ძიება',
            'placeholder' => 'ძიება',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'ჩანაწერების გადანაცვლების დასრულება',
        ],

        'enable_reordering' => [
            'label' => 'ჩანაწერების გადანაცვლება',
        ],

        'filter' => [
            'label' => 'ფილტრი',
        ],

        'open_actions' => [
            'label' => 'ქმედებები',
        ],

        'toggle_columns' => [
            'label' => 'სვეტების დამალვა/გამოჩენა',
        ],

    ],

    'empty' => [

        'heading' => 'ჩანაწერები ვერ მოიძებნა',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'ფილტრის მოხსნა',
            ],

            'remove_all' => [
                'label' => 'ყველა ფილტრის მოხსნა',
                'tooltip' => 'ყველა ფილტრის მოხსნა',
            ],

            'reset' => [
                'label' => 'ფილტრების გაუქმება',
            ],

        ],

        'indicator' => 'აქტიური ფილტრები',

        'multi_select' => [
            'placeholder' => 'ყველა',
        ],

        'select' => [
            'placeholder' => 'ყველა',
        ],

        'trashed' => [

            'label' => 'წაშლილი ჩანაწერები',

            'only_trashed' => 'მხოლოდ წაშლილი ჩანაწერები',

            'with_trashed' => 'წაშლილი ჩანაწერებიანა',

            'without_trashed' => 'წაშლილი ჩანაწერების გარეშე',

        ],

    ],

    'reorder_indicator' => 'გადაადგილე ჩანაწერები.',

    'selection_indicator' => [

        'selected_count' => 'მონიშნულია :count ჩანაწერი',

        'actions' => [

            'select_all' => [
                'label' => '{1} მონიშნე ყველა|[2,*] მონიშნე :count-ივე',
            ],

            'deselect_all' => [
                'label' => 'მონიშვნების მოხსნა',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'სორტირება',
            ],

            'direction' => [

                'label' => 'სორტირების მიმართულება',

                'options' => [
                    'asc' => 'ზრდადობით',
                    'desc' => 'კლებადობით',
                ],

            ],

        ],

    ],

];
