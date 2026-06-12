<?php

return [

    'label' => 'Կարգավորել',

    'modal' => [

        'heading' => 'Կարգավորել հաստատող հավելվածը',

        'description' => <<<'BLADE'
            Այս գործընթացը ավարտելու համար ձեզ անհրաժեշտ կլինի հավելված, օրինակ՝ Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>)։
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Սկանավորեք այս QR կոդը հաստատող հավելվածով․',

                'alt' => 'QR կոդ հաստատող հավելվածով սկանավորելու համար',

            ],

            'text_code' => [

                'instruction' => 'Կամ մուտքագրեք այս կոդը ձեռքով․',

                'messages' => [
                    'copied' => 'Պատճենվել է',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Խնդրում ենք պահպանել հետևյալ վերականգնման կոդերը անվտանգ վայրում։ Դրանք կցուցադրվեն միայն մեկ անգամ, բայց անհրաժեշտ կլինեն, եթե կորցնեք մուտքը հաստատող հավելվածի նկատմամբ։',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Մուտքագրեք 6-նիշանոց կոդը հաստատող հավելվածից',

                'validation_attribute' => 'կոդ',

                'below_content' => 'Ամեն անգամ մուտք գործելու կամ զգայուն գործողություն կատարելու ժամանակ ձեզ անհրաժեշտ կլինի մուտքագրել հաստատող հավելվածի 6-նիշանոց կոդը։',

                'messages' => [

                    'invalid' => 'Մուտքագրված կոդը սխալ է։',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Միացնել հաստատող հավելվածը',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Հաստատող հավելվածը միացվել է',
        ],

    ],

];
