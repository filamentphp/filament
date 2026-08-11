<?php

return [

    'form' => [

        'email' => [
            'label' => 'Address ng email',
        ],

        'name' => [
            'label' => 'Pangalan',
        ],

        'password' => [
            'label' => 'Bagong password',
        ],

        'password_confirmation' => [
            'label' => 'Kumpirmahin ang bagong password',
        ],

        'current_password' => [
            'label' => 'Kasalukuyang password',
            'below_content' => 'Para sa seguridad, kumpirmahin ang iyong password para magpatuloy.',
        ],

        'actions' => [

            'save' => [
                'label' => 'I-save ang mga pagbabago',
            ],

        ],

    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Naipadala ang request na baguhin ang email address',
            'body' => 'Naipadala sa :email ang request na baguhin ang iyong email address. Tingnan ang iyong email para i-verify ang pagbabago.',
        ],

        'saved' => [
            'title' => 'Na-save',
        ],

        'throttled' => [
            'title' => 'Masyadong maraming request. Subukan ulit pagkalipas ng :seconds segundo.',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Kanselahin',
        ],

    ],

];
