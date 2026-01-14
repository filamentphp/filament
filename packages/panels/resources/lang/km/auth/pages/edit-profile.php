<?php

return [

    'label' => 'ប្រវត្តិរូប',

    'form' => [

        'email' => [
            'label' => 'អាស័យដ្ធានអ៊ីម៉ែល',
        ],

        'name' => [
            'label' => 'ឈ្មោះ',
        ],

        'password' => [
            'label' => 'ពាក្យសម្ងាត់ថ្មី',
            'validation_attribute' => 'ពាក្យសម្ងាត់',
        ],

        'password_confirmation' => [
            'label' => 'បញ្ជាក់ពាក្យសម្ងាត់ថ្មី',
            'validation_attribute' => 'ការបញ្ជាក់ពាក្យសម្ងាត់',
        ],

        'current_password' => [
            'label' => 'ពាក្យសម្ងាត់បច្ចុប្បន្ន',
            'below_content' => 'សម្រាប់សុវត្ថិភាព សូមបញ្ជាក់ពាក្យសម្ងាត់របស់អ្នកដើម្បីបន្ត។',
            'validation_attribute' => 'ពាក្យសម្ងាត់បច្ចុប្បន្ន',
        ],

        'actions' => [

            'save' => [
                'label' => 'រក្សាទុកការផ្លាស់ប្តូរ',
            ],

        ],

    ],

    'notifications' => [

        'saved' => [
            'title' => 'បានរក្សាទុក',
        ],

        'email_change_verification_sent' => [
            'title' => 'សំណើផ្លាស់ប្តូរអាសយដ្ឋានអ៊ីមែលត្រូវបានផ្ញើ',
            'body' => 'សំណើដើម្បីផ្លាស់ប្តូរអាសយដ្ឋានអ៊ីមែលរបស់អ្នកត្រូវបានផ្ញើទៅកាន់ :email ។ សូមពិនិត្យមើលអ៊ីមែលរបស់អ្នកដើម្បីផ្ទៀងផ្ទាត់ការផ្លាស់ប្តូរ។',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'លុបចោល',
        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'ការផ្ទៀងផ្ទាត់ពីរជំហាន (2FA)',
    ],

];
