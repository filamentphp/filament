<?php

return [

    'title' => 'आफ्नो पासवर्ड रिसेट गर्नुहोस्',

    'heading' => 'आफ्नो पासवर्ड रिसेट गर्नुहोस्',

    'form' => [

        'email' => [
            'label' => 'इमेल ठेगाना',
        ],

        'password' => [
            'label' => 'पासवर्ड',
            'validation_attribute' => 'पासवर्ड',
        ],

        'password_confirmation' => [
            'label' => 'पासवर्ड सुनिश्चित गर्नुहोस',
        ],

        'actions' => [

            'reset' => [
                'label' => 'पासवर्ड रिसेट',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'धेरै धेरै रिसेट प्रयासहरू',
            'body' => 'कृपया :seconds सेकेन्डमा पुन प्रयास गर्नुहोस्।',
        ],

    ],

];
