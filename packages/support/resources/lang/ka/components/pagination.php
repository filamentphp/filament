<?php

return [

    'label' => 'გვერდების ნავიგაცია',

    'overview' => '{1} ნაჩვენებია 1 შედეგი|[2,*] ნაჩვენებია :first-დან :last-მდე :total შედეგიდან',

    'fields' => [

        'records_per_page' => [

            'label' => 'გვერდზე',

            'options' => [
                'all' => 'ყველა',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'პირველი',
        ],

        'go_to_page' => [
            'label' => 'გადადით გვერდზე :page',
        ],

        'last' => [
            'label' => 'ბოლო',
        ],

        'next' => [
            'label' => 'შემდეგი',
        ],

        'previous' => [
            'label' => 'წინა',
        ],

    ],

];
