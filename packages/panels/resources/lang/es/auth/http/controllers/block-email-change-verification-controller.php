<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Cambio de correo electrónico bloqueado',
            'body' => 'Ha bloqueado con éxito un intento de cambio de correo electrónico a :email. Si no hizo la solicitud original, contáctenos de inmediato.',
        ],

        'failed' => [
            'title' => 'Error al bloquear el cambio de correo electrónico',
            'body' => 'Lamentablemente, no se pudo evitar que el correo electrónico cambiara a :email, ya que estaba verificado antes de que se bloqueara. Si no hizo la solicitud original, póngase en contacto con nosotros de inmediato.',
        ],

    ],

];
