<?php

return [

    'label' => 'පැතිකඩ',

    'form' => [

        'email' => [
            'label' => 'විද්‍යුත් තැපැල් ලිපිනය',
        ],

        'name' => [
            'label' => 'නම',
        ],

        'password' => [
            'label' => 'නව මුරපදය',
            'validation_attribute' => 'මුරපදය',
        ],

        'password_confirmation' => [
            'label' => 'නව මුරපදය තහවුරු කරන්න',
            'validation_attribute' => 'මුරපද තහවුරුව',
        ],

        'current_password' => [
            'label' => 'වත්මන් මුරපදය',
            'below_content' => 'ආරක්ෂාව සඳහා, ඉදිරියට යාමට කරුණාකර ඔබගේ මුරපදය තහවුරු කරන්න.',
            'validation_attribute' => 'වත්මන් මුරපදය',
        ],

        'actions' => [

            'save' => [
                'label' => 'වෙනස්කම් සුරකින්න',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'ද්වි-පියවර සත්‍යාපනය (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'විද්‍යුත් තැපැල් ලිපිනය වෙනස් කිරීමේ ඉල්ලීම යවන ලදී',
            'body' => 'ඔබගේ විද්‍යුත් තැපැල් ලිපිනය වෙනස් කිරීමේ ඉල්ලීමක් :email වෙත යවා ඇත. වෙනස සත්‍යාපනය කිරීමට කරුණාකර ඔබගේ විද්‍යුත් තැපෑල පරීක්ෂා කරන්න.',
        ],

        'saved' => [
            'title' => 'සුරකින ලදී',
        ],

        'throttled' => [
            'title' => 'ඉල්ලීම් වැඩියි. කරුණාකර තත්පර :seconds කින් නැවත උත්සාහ කරන්න.',
            'body' => 'කරුණාකර තත්පර :seconds කින් නැවත උත්සාහ කරන්න.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'අවලංගු කරන්න',
        ],

    ],

];
