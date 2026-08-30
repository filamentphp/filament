<?php

return [
    'label' => 'Aking profile',
    'form' => [
        'email' => [
            'label' => 'Address ng email',
        ],
        'name' => [
            'label' => 'Pangalan',
        ],
        'password' => [
            'label' => 'Bagong password',
            'validation_attribute' => 'bagong password',
        ],
        'password_confirmation' => [
            'label' => 'Kumpirmahin ang bagong password',
            'validation_attribute' => 'pagkumpirma ng password',
        ],
        'current_password' => [
            'label' => 'Kasalukuyang password',
            'below_content' => 'Para sa seguridad, kumpirmahin ang password mo para magpatuloy.',
            'validation_attribute' => 'kasalukuyang password',
        ],
        'actions' => [
            'save' => [
                'label' => 'I-save ang mga pagbabago',
            ],
        ],
    ],
    'multi_factor_authentication' => [
        'label' => 'Dalawang hakbang na authentication (2FA)',
    ],
    'notifications' => [
        'email_change_verification_sent' => [
            'title' => 'Naipadala ang request na palitan ang email address',
            'body' => 'Naipadala sa :email ang request na palitan ang email address mo. Tingnan ang email mo para i-verify ang pagbabago.',
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
