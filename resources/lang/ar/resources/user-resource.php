<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'الصورة',
        ],

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'isAdmin' => [
            'label' => 'مشرف Filament؟',
            'helpMessage' => 'يستطيع مشرفو Filament الوصول إلى جميع مناطق Filament وإدارة المستخدمين الآخرين.',
        ],

        'isUser' => [
            'label' => 'مستخدم Filament؟',
        ],

        'name' => [
            'label' => 'الأسم',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'كلمة المرور',
                    'edit' => 'تعيين كلمة مرور جديدة',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'كلمة المرور',
                ],

                'passwordConfirmation' => [
                    'label' => 'تأكيد كلمة المرور',
                ],

            ],

        ],

        'roles' => [
            'label' => 'الأدوار',
            'placeholder' => 'إختر دور',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'البريد الإلكتروني',
            ],

            'name' => [
                'label' => 'الاسم',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'المشرفون',
            ],

        ],

    ],

];
