<?php

return [

    'title' => 'Գրանցվել',

    'heading' => 'Գրանցել նոր հաշիվ',

    'actions' => [

        'login' => [
            'before' => 'կամ',
            'label' => 'մուտք գործեք Ձեր հաշվով',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Էլ. փոստի հասցե',
        ],

        'name' => [
            'label' => 'Անուն',
        ],

        'password' => [
            'label' => 'Գաղտնաբառ',
            'validation_attribute' => 'Գաղտնաբառ',
        ],

        'password_confirmation' => [
            'label' => 'Հաստատեք գաղտնաբառը',
        ],

        'actions' => [

            'register' => [
                'label' => 'Գրանցել նոր հաշիվ',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Գրանցման չափազանց շատ փորձեր',
            'body' => 'Խնդրում ենք :seconds վայրկյան անց կրկին փորձել։',
        ],

    ],

];
