<?php

return [

    'title' => 'Վերականգնել Ձեր գաղտնաբառը',

    'heading' => 'Վերականգնել Ձեր գաղտնաբառը',

    'form' => [

        'email' => [
            'label' => 'Էլ. փոստի հասցե',
        ],

        'password' => [
            'label' => 'Գաղտնաբառ',
            'validation_attribute' => 'Գաղտնաբառ',
        ],

        'password_confirmation' => [
            'label' => 'Հաստատել գաղտնաբառը',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Գաղտնաբառի վերականգնում',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Վերականգնման չափազանց շատ փորձեր',
            'body' => 'Խնդրում ենք :seconds վայրկյան անց կրկին փորձել։',
        ],

    ],

];
