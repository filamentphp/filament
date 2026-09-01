<?php

return [

    'title' => 'ავტორიზაცია',

    'heading' => 'ავტორიზაცია',

    'actions' => [

        'register' => [
            'before' => 'ან',
            'label' => 'დარეგისტრირდით ანგარიშისთვის',
        ],

        'request_password_reset' => [
            'label' => 'დაგავიწყდა პაროლი?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ელფოსტის მისამართი',
        ],

        'password' => [
            'label' => 'პაროლი',
        ],

        'remember' => [
            'label' => 'დამიმახსოვრე',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'ავტორიზაცია',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'ეს მონაცემები არ ემთხვევა ჩვენს ჩანაწერებს.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'ზედმეტად ბევრი შესვლის მცდელობა',
            'body' => 'გთხოვთ, სცადეთ ხელახლა :seconds წამში.',
        ],

    ],

];
