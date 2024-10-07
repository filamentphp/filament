<?php

return [

    'title' => 'दर्ता गर्नुहोस्',

    'heading' => 'साइन अप',

    'actions' => [

        'login' => [
            'before' => 'वा',
            'label' => 'आफ्नो खातामा साइन इन गर्नुहोस्',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'इमेल ठेगाना',
        ],

        'name' => [
            'label' => 'नाम',
        ],

        'password' => [
            'label' => 'पासवर्ड',
            'validation_attribute' => 'पासवर्ड',
        ],

        'password_confirmation' => [
            'label' => 'पासवर्ड सुनिश्चित गर्नुहोस',
        ],

        'actions' => [

            'register' => [
                'label' => 'साइन अप',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'धेरै धेरै दर्ता प्रयासहरू',
            'body' => 'कृपया :seconds सेकेन्डमा पुन प्रयास गर्नुहोस्।',
        ],

    ],

];
