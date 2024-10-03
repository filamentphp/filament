<?php

return [

    'title' => 'लग-इन',

    'heading' => 'साइन इन गर्नुहोस्',

    'actions' => [

        'register' => [
            'before' => 'वा',
            'label' => 'खाताको लागि साइन अप गर्नुहोस्',
        ],

        'request_password_reset' => [
            'label' => 'तपाईँको पासवर्ड बिर्सनुभयो?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'इमेल ठेगाना',
        ],

        'password' => [
            'label' => 'पासवर्ड',
        ],

        'remember' => [
            'label' => 'मलाई सम्झनुहोस्',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'साइन इन गर्नुहोस्',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'यी प्रमाणहरू हाम्रा रेकर्डहरूसँग मेल खाँदैनन्।',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'धेरै लगइन प्रयासहरू',
            'body' => 'कृपया :seconds सेकेन्डमा पुन प्रयास गर्नुहोस्।',
        ],

    ],

];
