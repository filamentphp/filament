<?php

return [

    'notifications' => [

        'blocked' => [
            'title' => 'Email address change blocked',
            'body' => 'You have successfully blocked an email address change attempt to :email. If you did not make the original request, please contact us immediately.',
        ],

        'failed' => [
            'title' => 'Failed to block email address change',
            'body' => 'Unfortunately, you were unable to prevent the email address from being changed to :email, since it was already verified before you blocked it. If you did not make the original request, please contact us immediately.',
        ],

    ],

];
