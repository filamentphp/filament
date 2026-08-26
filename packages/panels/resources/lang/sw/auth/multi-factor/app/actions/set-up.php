<?php

return [

    'label' => 'Weka',

    'modal' => [
        'heading' => 'Weka programu ya uthibitishaji',

        'description' => 'Utahitaji programu kama Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) kukamilisha utaratibu huu.',

        'content' => [
            'qr_code' => [
                'instruction' => 'Changanua msimbo huu wa QR kwa kutumia programu yako ya uthibitishaji:',
                'alt' => 'Msimbo wa QR wa kuchanganuliwa kwa programu ya uthibitishaji',
            ],

            'text_code' => [
                'instruction' => 'Au weka msimbo huu kwa mkono:',

                'messages' => [
                    'copied' => 'Imenakiliwa',
                ],
            ],

            'recovery_codes' => [
                'instruction' => 'Tafadhali hifadhi vificho vifavyo vya urejesho mahali salama. Vitaonyeshwa mara moja tu, lakini utahitaji ukipoteza ufikiaji wa programu yako ya uthibitishaji:',
            ],
        ],

        'form' => [
            'code' => [
                'label' => 'Weka msimbo wa tarakimu 6 kutoka programu ya uthibitishaji',

                'validation_attribute' => 'msimbo',

                'below_content' => 'Utahitaji kuweka msimbo wa tarakimu 6 kutoka programu yako ya uthibitishaji kila unapoingia au kufanya vitendo nyeti.',

                'messages' => [
                    'invalid' => 'Msimbo uliouweka ni batili.',
                    'rate_limited' => 'Majaribio mengi mno. Tafadhali jaribu tena baadaye.',
                ],
            ],
        ],

        'actions' => [
            'submit' => [
                'label' => 'Washa programu ya uthibitishaji',
            ],
        ],
    ],

    'notifications' => [
        'enabled' => [
            'title' => 'Programu ya uthibitishaji imewashwa',
        ],
    ],

];
