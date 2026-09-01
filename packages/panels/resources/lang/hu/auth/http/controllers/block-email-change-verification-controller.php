<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'E-mail cím módosítása letiltva',
            'body' => 'Sikeresen letiltottad az e-mail cím módosítási kísérletét a következő címre: :email. Ha nem te kezdeményezted az eredeti kérelmet, kérjük, azonnal vedd fel velünk a kapcsolatot.',
        ],

        'failed' => [
            'title' => 'E-mail cím módosításának letiltása sikertelen',
            'body' => 'Sajnos nem tudtad megakadályozni az e-mail cím módosítását a következő címre: :email, mivel már ellenőrizve lett, mielőtt letiltottad volna. Ha nem te kezdeményezted az eredeti kérelmet, kérjük, azonnal vedd fel velünk a kapcsolatot.',
        ],

    ],

];
