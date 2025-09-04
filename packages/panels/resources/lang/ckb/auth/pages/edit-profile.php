<?php

return [

    'label' => 'زانیاری کەسیی',

    'form' => [

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'name' => [
            'label' => 'ناو',
        ],

        'password' => [
            'label' => 'تێپەڕەوشەی نوێ',
            'validation_attribute' => 'تێپەڕەوشە',
        ],

        'password_confirmation' => [
            'label' => 'دڵنیاکردنەوەی تێپەڕەوشەی نوێ',
            'validation_attribute' => 'دڵنیاکردنەوەی تێپەڕەوشە',
        ],

        'current_password' => [
            'label' => 'تێپەڕەوشەی ئێستا',
            'below_content' => 'لەبەر لایەنی پاراستن، تکایە تێپەڕەوشەکەت پشتڕاست بکەرەوە بۆ بەردەوامبوون.',
            'validation_attribute' => 'تێپەڕەوشەی ئێستا',
        ],

        'actions' => [

            'save' => [
                'label' => 'پاشەکەوتکردنی گۆڕانکارییەکان',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'ڕەسەنایەتی دوو هۆکار (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'داواکاری گۆڕینی ناونیشانی ئیمەیڵ نێردراوە',
            'body' => 'داواکارییەک بۆ گۆڕینی ناونیشانی ئیمەیڵەکەت بۆ :email نێردراوە. تکایە ئیمەیڵەکەت بپشکنە بۆ پشتڕاستکردنەوەی گۆڕانکارییەکە.',
        ],

        'saved' => [
            'title' => 'پاشەکەوتکرا',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'هەوەشاندنەوە',
        ],

    ],

];
