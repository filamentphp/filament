<?php

return [

    'title' => 'Giriş yap',

    'heading' => 'Hesabınıza giriş yapın',

    'actions' => [

        'authenticate' => [
            'label' => 'Giriş yap',
        ],

    ],

    'form' => [

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
        'failed' => 'Bu kimlik bilgileri kayıtlarla eşleşmiyor.',
        'throttled' => 'Çok fazla giriş denemesi. Lütfen :seconds saniye sonra tekrar deneyin.',
    ],

];
