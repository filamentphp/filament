<?php

return [
    'notifications' => [
        'blocked' => [
            'title' => 'Změna e-mailové adresy zablokována',
            'body' => 'Úspěšně jste zablokovali pokus o změnu e-mailové adresy na :email. Pokud jste o změnu nežádali, kontaktujte nás prosím ihned.',
        ],

        'failed' => [
            'title' => 'Nepodařilo se zablokovat změnu e-mailové adresy',
            'body' => 'Bohužel se Vám nepodařilo zabránit změně e-mailové adresy na :email, protože již byla ověřena před zablokováním. Pokud jste o změnu nežádali, kontaktujte nás prosím ihned.',
        ],
    ],
];
