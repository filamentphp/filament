<?php

return [

    'label' => 'Kurtarma kodlarını yeniden oluştur',

    'modal' => [

        'heading' => 'Kurtarma kodlarını yeniden oluştur',

        'description' => 'Eğer kurtarma kodlarınızı kaybederseniz buradan yeniden oluşturabilirsiniz. Eski kodlarınız devre dışı kalacaktır.',

        'form' => [

            'code' => [

                'label' => 'Doğrulama uygulamanızdaki 6 haneli kodu girin',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Girmiş olduğunuz kod geçersiz.',

                ],

            ],

            'password' => [

                'label' => 'Veya, geçerli şifrenizi girin',

                'validation_attribute' => 'şifre',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Kodları yeniden oluştur',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Yeni kurtarma kodları oluşturuldu',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Yeni kodlar',

            'description' => 'Lütfen bu kodları güvenli bir şekilde saklayın. Bu kodlar size sadece bir kere gösterilecek ve eğer doğrulama uygulamanıza erişiminizi kaybederseniz bu kodları kullanmanız gerekecek:',

            'actions' => [

                'submit' => [
                    'label' => 'Kapat',
                ],

            ],

        ],

    ],

];
