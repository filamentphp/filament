<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'name' => [
            'label' => 'Ad',
        ],

        'password' => [
            'label' => 'Yeni şifre',
            'validation_attribute' => 'şifre',
        ],

        'password_confirmation' => [
            'label' => 'Yeni şifreyi onayla',
            'validation_attribute' => 'şifre onayı',
        ],

        'current_password' => [
            'label' => 'Güncel şifre',
            'below_content' => 'Güvenliğiniz için lütfen güncel şifrenizi girin.',
            'validation_attribute' => 'güncel şifre',
        ],

        'actions' => [

            'save' => [
                'label' => 'Değişiklikleri Kaydet',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'İki Faktörlü Doğrulama (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'E-posta adresi güncelleme isteği gönderildi',
            'body' => 'E-posta adresi güncelleme isteği :email adresine gönderildi. Lütfen güncellemeyi tamamlamak için E-posta adresinizi doğrulayın.',
        ],

        'saved' => [
            'title' => 'Kaydedildi',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'İptal',
        ],

    ],

];
