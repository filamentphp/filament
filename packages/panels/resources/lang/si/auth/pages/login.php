<?php

return [

    'title' => 'පිවිසීම',

    'heading' => 'පිවිසෙන්න',

    'actions' => [

        'register' => [
            'before' => 'හෝ',
            'label' => 'ගිණුමක් සාදා ගන්න',
        ],

        'request_password_reset' => [
            'label' => 'මුරපදය අමතකද?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'විද්‍යුත් තැපැල් ලිපිනය',
        ],

        'password' => [
            'label' => 'මුරපදය',
        ],

        'remember' => [
            'label' => 'මාව මතක තබාගන්න',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'පිවිසෙන්න',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'ඔබගේ අනන්‍යතාවය සත්‍යාපනය කරන්න',

        'subheading' => 'පිවිසීම දිගටම කරගෙන යාමට, ඔබ ඔබගේ අනන්‍යතාවය සත්‍යාපනය කළ යුතුය.',

        'form' => [

            'provider' => [
                'label' => 'ඔබට සත්‍යාපනය කිරීමට අවශ්‍ය කෙසේද?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'පිවිසීම තහවුරු කරන්න',
                ],

            ],

        ],

    ],

    'messages' => [
        'failed' => 'මෙම අක්තපත්‍ර අපගේ වාර්තා සමඟ නොගැලපේ.',
    ],

    'notifications' => [

        'throttled' => [
            'title' => 'පිවිසුම් උත්සාහයන් වැඩියි',
            'body' => 'කරුණාකර තත්පර :seconds කින් නැවත උත්සාහ කරන්න.',
        ],

    ],

];
