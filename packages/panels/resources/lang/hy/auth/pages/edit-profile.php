<?php

return [

    'label' => 'Պրոֆիլ',

    'form' => [

        'email' => [
            'label' => 'Էլ. փոստի հասցե',
        ],

        'name' => [
            'label' => 'Անուն',
        ],

        'password' => [
            'label' => 'Նոր գաղտնաբառ',
            'validation_attribute' => 'գաղտնաբառ',
        ],

        'password_confirmation' => [
            'label' => 'Հաստատել նոր գաղտնաբառը',
            'validation_attribute' => 'գաղտնաբառի հաստատում',
        ],

        'current_password' => [
            'label' => 'Ներկայիս գաղտնաբառ',
            'below_content' => 'Անվտանգության համար խնդրում ենք հաստատել ձեր գաղտնաբառը՝ շարունակելու համար։',
            'validation_attribute' => 'ներկայիս գաղտնաբառ',
        ],

        'actions' => [

            'save' => [
                'label' => 'Պահպանել փոփոխությունները',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Երկգործոնային նույնականացում (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Էլ. փոստի հասցեի փոփոխման հարցումը ուղարկված է',
            'body' => 'Ձեր էլ. փոստի հասցեն փոխելու հարցում ուղարկվել է դեպի :email։ Խնդրում ենք ստուգել ձեր էլ. փոստը՝ փոփոխությունը հաստատելու համար։',
        ],

        'saved' => [
            'title' => 'Պահպանված է',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Չեղարկել',
        ],

    ],

];
