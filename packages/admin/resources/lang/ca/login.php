<?php

return [

    'title' => 'Inici de sessió',

    'heading' => 'Entra al teu compte',

    'buttons' => [

        'submit' => [
            'label' => 'Entra',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Correu electrònic',
        ],

        'password' => [
            'label' => 'Contrasenya',
        ],

        'remember' => [
            'label' => 'Recorda\'m',
        ],

    ],

    'messages' => [
        'failed' => 'Aquestes credencials no coincideixen amb els nostres registres.',
        'throttled' => 'Massa intents. Prova de nou en :seconds segons.',
    ],

];
