<?php

return [

    'title' => 'Վերականգնել ձեր գաղտնաբառը',

    'heading' => 'Վերականգնել ձեր գաղտնաբառը',

    'form' => [

        'email' => [
            'label' => 'Էլ. փոստի հասցե',
        ],

        'password' => [
            'label' => 'Գաղտնաբառ',
            'validation_attribute' => 'գաղտնաբառ',
        ],

        'password_confirmation' => [
            'label' => 'Հաստատել գաղտնաբառը',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Վերականգնել գաղտնաբառը',
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
