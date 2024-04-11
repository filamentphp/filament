<?php

return [

    'title' => 'लग - इन',

    'heading' => 'साइन इन गर्नुहोस्',

    'actions' => [

        'register' => [
            'before' => 'वा',
            'label' => 'खाता को लागि साइन अप गर्नुहोस्',
        ],

        'request_password_reset' => [
            'label' => 'पासवर्ड बिर्सनुभयो?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ईमेल ठेगाना',
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

        'failed' => 'यी प्रमाणहरू हाम्रो रेकर्डसँग मेल खाँदैनन्।',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'धेरै पटक लगिन प्रयास गरिएको',
            'body' => ':seconds सेकेण्डमा पुनः प्रयास गर्नुहोस्।',
        ],

    ],

];
