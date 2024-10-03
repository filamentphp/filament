<?php

return [

    'label' => 'Səhifələndirmə Naviqasiyası',

    'overview' => 'Toplam :total nəticədən :first ile :last arası göstərilir',

    'fields' => [

        'records_per_page' => [
            'label' => 'səhifə başına',

            'options' => [
                'all' => 'Hamısı',
            ],
        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => ':page. səhifəyə get',
        ],

        'next' => [
            'label' => 'Sonrakı',
        ],

        'previous' => [
            'label' => 'Əvvəlki',
        ],

    ],

];
