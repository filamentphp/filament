<?php

return [

    'title' => 'Giriş yap',

    'heading' => 'Hesabınıza girip yapın',

    'buttons' => [

        'submit' => [
            'label' => 'Giriş yap',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Parola',
        ],

        'remember' => [
            'label' => 'Beni hatırla',
        ],

    ],

    'messages' => [
        'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',
        'throttled' => 'Çok fazla giriş denemesi. Lütfen :seconds saniye içinde tekrar deneyin.',
    ],

];
