<?php

return [

    'title' => 'तपाइँको पासवर्ड रिसेट गर्नुहोस्',

    'heading' => 'तपाइँको पासवर्ड रिसेट गर्नुहोस्',

    'form' => [

        'email' => [
            'label' => 'ईमेल ठेगाना',
        ],

        'password' => [
            'label' => 'पासवर्ड',
            'validation_attribute' => 'पासवर्ड',
        ],

        'password_confirmation' => [
            'label' => 'पासवर्ड पुष्टि गर्नुहोस्',
        ],

        'actions' => [

            'reset' => [
                'label' => 'पासवर्ड रिसेट गर्नुहोस्',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'धेरै पटक रिसेट प्रयास गरिएको',
            'body' => ':seconds सेकेण्डमा पुन: प्रयास गर्नुहोस्।',
        ],

    ],

];
