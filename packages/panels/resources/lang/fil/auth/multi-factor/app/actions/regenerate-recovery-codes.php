<?php

return [
    'label' => 'Gumawa ulit ng mga recovery code',
    'modal' => [
        'heading' => 'Gumawa ulit ng mga recovery code ng authenticator app',
        'description' => 'Kung nawala ang mga recovery code mo, puwede kang gumawa ulit dito. Agad na mawawalan ng bisa ang mga luma mong recovery code.',
        'form' => [
            'code' => [
                'label' => 'Ilagay ang 6-digit code mula sa authenticator app',
                'validation_attribute' => 'verification code',
                'messages' => [
                    'invalid' => 'Hindi valid ang inilagay mong code.',
                    'rate_limited' => 'Masyadong maraming attempt. Subukan ulit mamaya.',
                ],
            ],
            'password' => [
                'label' => 'O, ilagay ang kasalukuyan mong password',
                'validation_attribute' => 'kasalukuyang password',
            ],
        ],
        'actions' => [
            'submit' => [
                'label' => 'Gumawa ulit ng mga recovery code',
            ],
        ],
    ],
    'notifications' => [
        'regenerated' => [
            'title' => 'Nagawa na ang mga bagong recovery code ng authenticator app',
        ],
    ],
    'show_new_recovery_codes' => [
        'modal' => [
            'heading' => 'Mga bagong recovery code',
            'description' => 'I-save ang mga sumusunod na recovery code sa ligtas na lugar. Isang beses lang ipapakita ang mga ito, pero kakailanganin mo ang mga ito kung mawalan ka ng access sa authenticator app mo:',
            'actions' => [
                'submit' => [
                    'label' => 'Isara',
                ],
            ],
        ],
    ],
];
