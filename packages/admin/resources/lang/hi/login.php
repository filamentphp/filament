<?php

return [

    'title' => 'लॉग इन',

    'heading' => 'अपने अकाउंट में साइन इन करें',

    'buttons' => [

        'submit' => [
            'label' => 'लॉग इन',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'ईमेल',
        ],

        'password' => [
            'label' => 'पासवर्ड',
        ],

        'remember' => [
            'label' => 'मुझे याद रखना',
        ],

    ],

    'messages' => [
        'failed' => 'ये प्रमाण हमारे रिकॉर्ड से मेल नहीं खा रहे हैं।',
        'throttled' => 'बहुत सारे लॉगिन प्रयास। :seconds सेकंड में फिर से कोशिश करें।',
    ],

];
