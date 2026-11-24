<?php

return [

    'notifications' => [

        'rejected' => [
            'title' => 'E-Mail-Adresse konnte nicht geändert werden',
            'body' => 'Die Änderung der E-Mail-Adresse konnte nicht abgeschlossen werden. Bitte versuchen Sie erneut, Ihre E-Mail-Adresse zu ändern.',
        ],

        'unavailable' => [
            'title' => 'Diese E-Mail-Adresse ist nicht mehr verfügbar.',
            'body' => 'Sie wurde von einem anderen Konto beansprucht, während Ihr Bestätigungslink noch gültig war.',
        ],

        'verified' => [
            'title' => 'E-Mail-Adresse geändert',
            'body' => 'Ihre E-Mail-Adresse wurde erfolgreich zu :email geändert.',
        ],

    ],

];
