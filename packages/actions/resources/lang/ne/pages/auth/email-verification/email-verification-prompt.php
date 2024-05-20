<?php

return [

    'title' => 'तपाईंको ईमेल ठेगाना प्रमाणित गर्नुहोस्',

    'heading' => 'तपाईंको ईमेल ठेगाना प्रमाणित गर्नुहोस्',

    'actions' => [

        'resend_notification' => [
            'label' => 'पुनः पठाउनुहोस्',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'हामीले पठाएको ईमेल तपाईंले पाउनुभएन?',
        'notification_sent' => 'तपाईंको ईमेल :email मा प्रमाणित गर्ने निर्देशनहरू सहित एउटा ईमेल पठाइएको छ।',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'हामीले ईमेल पुनः पठाएका छौं।',
        ],

        'notification_resend_throttled' => [
            'title' => 'धेरै पटक पुनः पठाउने प्रयास गरियो',
            'body' => ':seconds सेकेण्डमा पुनः प्रयास गर्नुहोस्।',
        ],

    ],

];
