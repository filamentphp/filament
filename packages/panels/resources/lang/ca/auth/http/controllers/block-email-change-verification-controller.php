<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Canvi d\'email bloquejat',
            'body' => 'Ha bloquejat amb èxit un intent de canvi d\'email a :email. Si no has realitzat la sol·licitud original, posa\'t en contacte amb nosaltres immediatament.',
        ],

        'failed' => [
            'title' => 'Error en bloquejar el canvi d\'email',
            'body' => 'Lamentablement, no s\'ha pogut evitar que l\'adreça de correu electrònica hagi canviat a :email, ja que estava verificat abans que es bloquegés. Si no has realitzat la sol·licitud original, posa\'t en contacte amb nosaltres immediatament.',
        ],

    ],

];
