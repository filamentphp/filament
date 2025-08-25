<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Alteração de endereço de email bloqueada',
            'body' => 'Bloqueou com sucesso uma tentativa de alteração de endereço de email para :email. Se não fez o pedido original, contacte-nos imediatamente.',
        ],

        'failed' => [
            'title' => 'Falha ao bloquear alteração de endereço de email',
            'body' => 'Infelizmente, não foi possível impedir que o endereço de email fosse alterado para :email, pois já tinha sido verificado antes de o bloquear. Se não fez o pedido original, contacte-nos imediatamente.',
        ],

    ],

];
