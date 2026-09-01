<?php

return [

    'label' => 'הצגת רשומות',

    'overview' => 'מציג :first - :last מתוך :total תוצאות',

    'fields' => [

        'records_per_page' => [

            'label' => 'בעמוד',

            'options' => [
                'all' => 'הכל',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'ראשון',
        ],

        'go_to_page' => [
            'label' => 'נווט לעמוד :page',
        ],

        'last' => [
            'label' => 'אחרון',
        ],

        'next' => [
            'label' => 'הבא',
        ],

        'previous' => [
            'label' => 'הקודם',
        ],

    ],

];
