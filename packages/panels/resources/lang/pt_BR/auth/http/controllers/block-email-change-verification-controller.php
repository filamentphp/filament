<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Alteração de e-mail bloqueada',
            'body' => 'Você bloqueou com sucesso uma tentativa de alterar o endereço de e-mail para :email. Se você não fez a solicitação original, entre em contato conosco imediatamente.',
        ],

        'failed' => [
            'title' => 'Falha ao bloquear a alteração de e-mail',
            'body' => 'Infelizmente, não foi possível impedir que o e-mail fosse alterado para :email, pois ele já havia sido verificado antes do bloqueio. Se você não fez a solicitação original, entre em contato conosco imediatamente.',
        ],

    ],

];
