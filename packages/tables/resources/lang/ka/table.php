<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'და კიდევ :count',
        ],

        'messages' => [
            'copied' => 'დაკოპირებულია',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'search_query' => [
            'label' => 'ძიება',
            'placeholder' => 'ძიება',
        ],

    ],

    'pagination' => [

        'label' => 'გვერდებზე ნავიგაცია',

        'overview' => '{1} ნაჩვენებია ერთი შედეგი|[2,*] ნაჩვენებია :first-დან :last-მდე :total-დან',

        'fields' => [

            'records_per_page' => [

                'label' => 'ჩანაწერი თითო გვერდზე',

                'options' => [
                    'all' => 'ყველა',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => '{1} :page-ელ გვერდზე გადასვლა|[2,*] მე-:page გვერდზე გადასვლა',
            ],

            'next' => [
                'label' => 'შემდეგი',
            ],

            'previous' => [
                'label' => 'წინა',
            ],

        ],

    ],

    'buttons' => [

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

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'სვეტის ძიების წაშლა',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

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

        'selected_count' => 'მონიშნულია :count ჩანაწერი.',

        'buttons' => [

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
