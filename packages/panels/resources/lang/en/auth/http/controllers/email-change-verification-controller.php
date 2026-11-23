<?php

return [

    'notifications' => [

        'rejected' => [
            'title' => 'Email address could not be changed',
            'body' => 'The email change could not be completed. Please try changing your email address again.',
        ],

        'unavailable' => [
            'title' => 'That email address is no longer available.',
            'body' => 'It was claimed by another account while your verification link was outstanding.',
        ],

        'verified' => [
            'title' => 'Email address changed',
            'body' => 'Your email address has been successfully changed to :email.',
        ],

    ],

];
