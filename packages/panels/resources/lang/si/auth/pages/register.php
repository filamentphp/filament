<?php

return [

    'title' => 'ලියාපදිංචිය',

    'heading' => 'ලියාපදිංචි වන්න',

    'actions' => [

        'login' => [
            'before' => 'හෝ',
            'label' => 'ඔබගේ ගිණුමට පිවිසෙන්න',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'විද්‍යුත් තැපැල් ලිපිනය',
        ],

        'name' => [
            'label' => 'නම',
        ],

        'password' => [
            'label' => 'මුරපදය',
            'validation_attribute' => 'මුරපදය',
        ],

        'password_confirmation' => [
            'label' => 'මුරපදය තහවුරු කරන්න',
        ],

        'actions' => [

            'register' => [
                'label' => 'ලියාපදිංචි වන්න',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'ලියාපදිංචි වීමේ උත්සාහයන් වැඩියි',
            'body' => 'කරුණාකර තත්පර :seconds කින් නැවත උත්සාහ කරන්න.',
        ],

    ],

];
