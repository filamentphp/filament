<?php

return [

    'label' => 'Kur',

    'modal' => [

        'heading' => 'Doğrulama uygulaması kur',

        'description' => <<<'BLADE'
            Devam etmek için Google Authenticator gibi (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) uygulamalardan birine ihtiyacınız olacak.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Doğrulama uygulamanızla aşağıdaki QR kodunu taratın:',

                'alt' => 'QR kodunu taratın',

            ],

            'text_code' => [

                'instruction' => 'Veya aşağıdaki kodu elle girin:',

                'messages' => [
                    'copied' => 'Kopyalandı',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Lütfen bu kodları güvenli bir şekilde saklayın. Bu kodlar size sadece bir kere gösterilecek ve eğer doğrulama uygulamanıza erişiminizi kaybederseniz bu kodları kullanmanız gerekecek:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Doğrulama uygulamanızdaki 6 haneli kodu girin',

                'validation_attribute' => 'kod',

                'below_content' => 'Giriş yaparken veya hassas bir işlem gerçekleştirirken doğrulama uygulamanız tarafından oluşturulan 6 haneli kodu girmeniz gerekecek.',

                'messages' => [

                    'invalid' => 'Girmiş olduğunuz kod geçersiz.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Doğrulama uygulamasını etkinleştir',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Doğrulama uygulaması etkinleştirildi',
        ],

    ],

];
