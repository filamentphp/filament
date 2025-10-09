<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Endring av e-postadresse blokkert',
            'body' => 'Du har blokkert et forsøk på å endre e-postadressen til :email. Hvis du ikke gjorde den opprinnelige forespørselen, kontakt oss umiddelbart.',
        ],

        'failed' => [
            'title' => 'Kunne ikke blokkere endring av e-postadresse',
            'body' => 'Du klarte ikke å forhindre at e-postadressen ble endret til :email, siden den allerede var verifisert før du blokkerte den. Hvis du ikke gjorde den opprinnelige forespørselen, kontakt oss umiddelbart.',
        ],

    ],

];
