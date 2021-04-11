<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'آواتار',
        ],

        'email' => [
            'label' => 'ایمیل آدرس',
        ],

        'isAdmin' => [
            'label' => 'مدیر فلامینت؟',
            'helpMessage' => 'مدیران فلامینت توانایی دسترسی به تمام نقاط فلامینت داشته و از سایر کاربران دیگر باز رسی می کنند.',

        ],

        'isUser' => [
            'label' => 'کاربر فلامینت؟',
        ],

        'name' => [
            'label' => 'اسم',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'رمز عبور',
                    'edit' => 'رمز عبور جدید ایجاد کنید',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'رمز عبور',
                ],

                'passwordConfirmation' => [
                    'label' => 'تاٌیید رمز عبور',
                ],

            ],

        ],

        'roles' => [
            'label' => 'نقش ها',
            'placeholder' => 'نقشی را انتخاب کنید',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'ایمیل آدرس',
            ],

            'name' => [
                'label' => 'اسم',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'مدیران',
            ],

        ],

    ],

];
