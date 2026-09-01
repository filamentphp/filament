<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Zmena e-mailovej adresy bola zablokovaná',
            'body' => 'Úspešne ste zablokovali pokus o zmenu e-mailovej adresy na :email. Ak ste o zmenu nepožiadali, kontaktujte nás prosím okamžite.',
        ],

        'failed' => [
            'title' => 'Nepodarilo sa zablokovať zmenu e-mailovej adresy',
            'body' => 'Žiaľ, nepodarilo sa Vám zabrániť zmene e-mailovej adresy na :email, pretože už bola overená pred zablokovaním. Ak ste o zmenu nepožiadali, kontaktujte nás prosím okamžite.',
        ],

    ],

];
