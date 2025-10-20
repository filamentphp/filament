<?php

return [

    'label' => 'पृष्ठांकन नेभिगेसन',

    'overview' => '{1} एउटा परिणाम देखाउँदै|[2,*] :total नतिजा मध्ये :first देखि :last देखाउँदै',

    'fields' => [

        'records_per_page' => [

            'label' => 'प्रति पृष्ठ',

            'options' => [
                'all' => 'सबै',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'पृष्ठ :page मा जानुहोस्',
        ],

        'next' => [
            'label' => 'अर्को',
        ],

        'previous' => [
            'label' => 'अघिल्लो',
        ],

    ],

];
