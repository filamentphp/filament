<?php

return [

    'label' => 'ইমেইল',

    'description' => 'আপনার ইমেইল ঠিকানায় একটি প্রমাণীকরণ কোড পাঠান',

    'actions' => [

        'regenerate_recovery_codes' => [
            'label' => 'পুনরুদ্ধার কোড পুনরুত্পাদন করুন',
        ],

        'show_recovery_codes' => [
            'label' => 'পুনরুদ্ধার কোড দেখুন',
        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'ইমেইল প্রমাণীকরণ সক্রিয় হয়েছে',
        ],

        'disabled' => [
            'title' => 'ইমেইল প্রমাণীকরণ নিষ্ক্রিয় হয়েছে',
        ],

    ],

];
