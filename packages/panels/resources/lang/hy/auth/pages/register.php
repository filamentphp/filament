<?php

return [

    'title' => 'Գրանցվել',

    'heading' => 'Գրանցում',

    'actions' => [

        'login' => [
            'before' => 'կամ',
            'label' => 'մուտք գործել ձեր հաշիվ',
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
            'validation_attribute' => 'գաղտնաբառ',
        ],

        'password_confirmation' => [
            'label' => 'Հաստատեք գաղտնաբառը',
        ],

        'actions' => [

            'register' => [
                'label' => 'Գրանցվել',
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
