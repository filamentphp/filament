<?php

return [

    'title' => 'लॉग इन',

    'heading' => 'अपने अकाउंट में साइन इन करें',

    'form' => [

        'email' => [
            'label' => 'ईमेल',
        ],

        'password' => [
            'label' => 'पासवर्ड',
        ],

        'remember' => [
            'label' => 'मुझे याद रखना',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'लॉग इन',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'ये प्रमाण हमारे रिकॉर्ड से मेल नहीं खा रहे हैं।',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'बहुत सारे लॉगिन प्रयास। :seconds सेकंड में फिर से कोशिश करें।',
        ],

    ],

];
