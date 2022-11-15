<?php

return [

    'fields' => [

        'search' => [
            'label' => 'ძიება',
            'placeholder' => 'ძიება',
        ],

    ],

    'pagination' => [

        'label' => 'გვერდების ნავიგაცია',

        'overview' => 'Showing :first to :last of :total results',

        'fields' => [

            'records_per_page' => [
                'label' => 'ჩანაწერი თითო გვერდზე',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => ':page-ე გვერდზე გადასვლა',
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

        'filter' => [
            'label' => 'ფილტრი',
        ],

        'open_bulk_actions' => [
            'label' => 'Open actions',
        ],

    ],

    'empty' => [
        'heading' => 'ჩანაწერები არ არსებობს.',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

        ],

    ],

];
