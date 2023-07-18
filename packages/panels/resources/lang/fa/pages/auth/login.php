<?php

return [

    'title' => 'ورود',

    'heading' => 'ورود به حساب کاربری شما',

    'form' => [

        'email' => [
            'label' => 'آدرس ایمیل',
        ],

        'password' => [
            'label' => 'رمزعبور',
        ],

        'remember' => [
            'label' => 'مرا به خاطر بسپار',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'ورود',
            ],

        ],

    ],

    'messages' => [
        'failed' => 'مشخصات وارد شده با اطلاعات ما سازگار نیست.',
        'throttled' => 'شما بیش از حد مجاز درخواست ورود داشته‌اید. لطفاً :seconds ثانیه دیگر تلاش کنید.',
    ],

];
