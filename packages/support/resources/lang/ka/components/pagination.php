<?php

return [

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

    'actions' => [

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

];
