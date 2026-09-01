<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Cambio dell\'email bloccato',
            'body' => 'Hai bloccato con successo un tentativo di cambio dell\'email a :email. Se non hai effettuato la richiesta originale, ti preghiamo di contattarci immediatamente.',
        ],

        'failed' => [
            'title' => 'Impossibile bloccare il cambio dell\'email',
            'body' => 'Sfortunatamente, non sei riuscito a impedire che l\'email venisse cambiata in :email, poiché era già stata verificata prima di essere bloccata. Se non hai effettuato la richiesta originale, ti preghiamo di contattarci immediatamente.',
        ],

    ],

];
