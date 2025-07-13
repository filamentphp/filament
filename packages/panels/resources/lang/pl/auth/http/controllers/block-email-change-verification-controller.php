<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Zablokowano zmianę adresu e-mail',
            'body' => 'Pomyślnie zablokowano próbę zmiany adresu e-mail na :email. Skontaktuj się z nami niezwłocznie, jeśli zmiana adresu e-mail nie była przez Ciebie inicjowana.',
        ],

        'failed' => [
            'title' => 'Nie udało się zablokować zmiany adresu e-mail',
            'body' => 'Niestety, nie udało się zapobiec zmianie adresu e-mail na :email, ponieważ adres został zweryfikowany przed zablokowaniem. Skontaktuj się z nami niezwłocznie, jeśli zmiana adresu e-mail nie była przez Ciebie inicjowana.',
        ],

    ],

];
