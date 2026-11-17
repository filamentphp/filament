<?php

return [

    'label' => 'Կարգավորել',

    'modal' => [

        'heading' => 'Կարգավորել նույնականացման հավելվածը',

        'description' => <<<'BLADE'
            Այս գործընթացը ավարտելու համար ձեզ անհրաժեշտ կլինի հավելված, օրինակ՝ Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>)։
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Սկանավորեք այս QR կոդը նույնականացման հավելվածով․',

                'alt' => 'QR կոդ նույնականացման հավելվածով սկանավորելու համար',

            ],

            'text_code' => [

                'instruction' => 'Կամ մուտքագրեք այս կոդը ձեռքով․',

                'messages' => [
                    'copied' => 'Պատճենվել է',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Խնդրում ենք պահպանել հետևյալ վերականգնման կոդերը անվտանգ վայրում։ Դրանք կցուցադրվեն միայն մեկ անգամ, բայց անհրաժեշտ կլինեն, եթե կորցնեք մուտքը նույնականացման հավելվածի նկատմամբ։',

            ],

        ],

        'form' => [

            'password' => [
                'label' => 'Ընթացիկ գաղտնաբառ',
                'validation_attribute' => 'ընթացիկ գաղտնաբառ',
            ],

            'code' => [

                'label' => 'Մուտքագրեք 6-նիշանոց կոդը նույնականացման հավելվածից',

                'validation_attribute' => 'կոդ',

                'below_content' => 'Ամեն անգամ մուտք գործելու կամ զգայուն գործողություն կատարելու ժամանակ ձեզ անհրաժեշտ կլինի մուտքագրել նույնականացման հավելվածի 6-նիշանոց կոդը։',

                'messages' => [

                    'invalid' => 'Մուտքագրված կոդը սխալ է։',

                    'rate_limited' => 'Չափազանց շատ փորձեր։ Խնդրում ենք կրկին փորձել ավելի ուշ։',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Միացնել նույնականացման հավելվածը',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Նույնականացման հավելվածը միացվել է',
        ],

    ],

];
