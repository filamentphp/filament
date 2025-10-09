<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Ändring av mejladress blockerad',
            'body' => 'Du har framgångsrikt blockerat ett försök att ändra mejladress till :email. Om du inte gjorde den ursprungliga begäran, vänligen kontakta oss omedelbart.',
        ],

        'failed' => [
            'title' => 'Misslyckades att blockera ändring av mejladress',
            'body' => 'Tyvärr kunde du inte förhindra att mejladressen ändrades till :email, eftersom den redan verifierades innan du blockerade den. Om du inte gjorde den ursprungliga begäran, vänligen kontakta oss omedelbart.',
        ],

    ],

];
