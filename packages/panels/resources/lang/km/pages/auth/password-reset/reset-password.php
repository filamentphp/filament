<?php

return [

    'title' => 'កំណត់ពាក្យសម្ងាត់របស់អ្នកឡើងវិញ',

    'heading' => 'កំណត់ពាក្យសម្ងាត់របស់អ្នកឡើងវិញ',

    'form' => [

        'email' => [
            'label' => 'អាស័យដ្ធានអ៊ីម៉ែល',
        ],

        'password' => [
            'label' => 'ពាក្យសម្ងាត់',
            'validation_attribute' => 'ពាក្យសម្ងាត់',
        ],

        'password_confirmation' => [
            'label' => 'បញ្ជាក់ពាក្យសម្ងាត់',
        ],

        'actions' => [

            'reset' => [
                'label' => 'កំណត់ពាក្យសម្ងាត់ឡើងវិញ',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'ការព្យាយាមកំណត់ឡើងវិញច្រើនដងពេក',
            'body' => 'សូមព្យាយាមម្តងទៀតក្នុងរយៈពេល :seconds វិនាទី។.',
        ],

    ],

];
