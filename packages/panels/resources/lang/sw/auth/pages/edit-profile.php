<?php

return [

    'label' => 'Wasifu',

    'form' => [
        'email' => [
            'label' => 'Barua pepe',
        ],

        'name' => [
            'label' => 'Jina',
        ],

        'password' => [
            'label' => 'Nenosiri jipya',
            'validation_attribute' => 'nenosiri',
        ],

        'password_confirmation' => [
            'label' => 'Thibitisha nenosiri jipya',
            'validation_attribute' => 'uthibitisho wa nenosiri',
        ],

        'current_password' => [
            'label' => 'Nenosiri la sasa',
            'below_content' => 'Kwa usalama, tafadhali thibitisha nenosiri lako ili kuendelea.',
            'validation_attribute' => 'nenosiri la sasa',
        ],

        'actions' => [
            'save' => [
                'label' => 'Hifadhi mabadiliko',
            ],
        ],
    ],

    'multi_factor_authentication' => [
        'label' => 'Uthibitishaji wa hatua mbili (2FA)',
    ],

    'notifications' => [
        'email_change_verification_sent' => [
            'title' => 'Ombi la kubadilisha barua pepe limetumwa',
            'body' => 'Ombi la kubadilisha barua pepe yako limetumwa kwenda :email. Tafadhali angalia barua pepe yako ili kuthibitisha mabadiliko.',
        ],

        'saved' => [
            'title' => 'Imehifadhiwa',
        ],

        'throttled' => [
            'title' => 'Maombi mengi mno. Tafadhali jaribu tena baada ya sekunde :seconds.',
            'body' => 'Tafadhali jaribu tena baada ya sekunde :seconds.',
        ],
    ],

    'actions' => [
        'cancel' => [
            'label' => 'Ghairi',
        ],
    ],

];
