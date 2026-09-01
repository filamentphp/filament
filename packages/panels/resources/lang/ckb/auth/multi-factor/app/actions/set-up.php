<?php

return [

    'label' => 'جێگیرکردن',

    'modal' => [

        'heading' => 'جێگیرکردنی بەرنامەی ڕەسەنایەتی',

        'description' => <<<'BLADE'
            پێویستت بە بەرنامەیەکی وەکو Google Authenticator هەیە (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) to complete this process.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'ئەم کۆدی QR بە بەرنامەی ڕەسەنایەتی بپشکنە:',

                'alt' => 'کۆدی QR بۆ پشکنین بە بەرنامەی ڕەسەنایەتی',

            ],

            'text_code' => [

                'instruction' => 'یان ئەم کۆدە بە دەستی داخڵ بکە:',

                'messages' => [
                    'copied' => 'لەبەریگیرایەوە',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'تکایە ئەم کۆدانەی گەڕاندنەوە لە خوارەوە لە شوێنێکی پارێزراودا هەڵبگرن. تەنها جارێک پیشان دەدرێن، بەڵام پێویستت پێیان دەبێت ئەگەر دەستڕاگەیشتن بە بەرنامەی ڕەسەنایەتیت لەدەست بدەیت:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'کۆدی 6 ژمارەیی لە بەرنامەی ڕەسەنایەتی داخڵ بکە',

                'validation_attribute' => 'کۆد',

                'below_content' => 'پێویستە کۆدی 6 ژمارەیی لە بەرنامەی ڕەسەنایەتیەوە داخڵ بکەیت هەر جارێک کە دەچیتە ژوورەوە یان کردارە هەستیارەکان ئەنجام دەدەیت.',

                'messages' => [

                    'invalid' => 'کۆدی هەڵە داخڵکراوە.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'چالاککردنی بەرنامەی ڕەسەنایەتی',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'بەرنماەی ڕەسەنایەتی چالاککرا',
        ],

    ],

];
