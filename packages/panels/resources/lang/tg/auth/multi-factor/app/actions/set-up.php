<?php

return [

    'label' => 'Фаъол кардан',

    'modal' => [

        'heading' => 'Танзим кардани барномаи 2FA',

        'description' => <<<'BLADE'
            Барои анҷом додани танзим ба шумо барномае монанди Google Authenticator лозим мешавад (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Ин QR-рамзро бо истифода аз барномаи 2FA скан кунед:',

                'alt' => 'QR-рамз барои скан кардан бо барномаи 2FA',

            ],

            'text_code' => [

                'instruction' => 'Ё ин рамзро дастӣ ворид кунед:',

                'messages' => [
                    'copied' => 'Нусхабардорӣ шуд',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Лутфан рамзҳои зерини барқарорсозиро дар ҷои бехатар нигоҳ доред. Онҳо танҳо як маротиба намоиш дода мешаванд, аммо агар дастрасӣ ба барномаи 2FA-ро аз даст диҳед, ба онҳо ниёз хоҳед дошт:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Рамзи 6-рақамаро аз барномаи 2FA ворид кунед',

                'validation_attribute' => 'рамз',

                'below_content' => 'Ҳангоми ҳар як воридшавӣ ба низом ё иҷрои амалҳои ҳассос, шумо бояд рамзи 6-рақамаро аз барномаи 2FA ворид кунед.',

                'messages' => [

                    'invalid' => 'Рамзи воридшуда нодуруст аст.',

                    'rate_limited' => 'Кӯшишҳо аз ҳад зиёд шуданд. Лутфан баъдтар дубора кӯшиш кунед.',
                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Фаъол кардани барномаи 2FA',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Барномаи 2FA фаъол карда шуд',
        ],

    ],

];
