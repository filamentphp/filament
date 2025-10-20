<?php

return [

    'title' => 'Մուտք',

    'heading' => 'Մուտք գործել',

    'actions' => [

        'register' => [
            'before' => 'կամ',
            'label' => 'ստեղծել նոր հաշիվ',
        ],

        'request_password_reset' => [
            'label' => 'Մոռացե՞լ եք գաղտնաբառը',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Էլ. փոստի հասցե',
        ],

        'password' => [
            'label' => 'Գաղտնաբառ',
        ],

        'remember' => [
            'label' => 'Հիշել ինձ',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Մուտք գործել',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Հաստատեք ձեր անձը',

        'subheading' => 'Շարունակելու համար անհրաժեշտ է հաստատել ձեր անձը։',

        'form' => [

            'provider' => [
                'label' => 'Ինչպե՞ս կցանկանայիք հաստատել',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Հաստատել մուտքը',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Մուտքի տվյալները չեն համապատասխանում մեր գրանցումներին։',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Չափազանց շատ մուտքի փորձեր',
            'body' => 'Խնդրում ենք փորձել կրկին :seconds վայրկյան հետո։',
        ],

    ],

];
