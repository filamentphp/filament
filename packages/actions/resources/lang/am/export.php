<?php

return [

    'label' => 'ኤክስፖርት :label',
    'modal' => [

        'heading' => 'ኤክስፖርት :label',
        'form' => [

            'columns' => [

                'label' => 'አምዶች',
                'form' => [

                    'is_enabled' => [

                        'label' => ':column በርቶዋል',
                    ],
                    'label' => [

                        'label' => ':column መለያ',
                    ],
                ],
            ],
        ],
        'actions' => [

            'export' => [

                'label' => 'ኤክስፖርት',
            ],
        ],
    ],
    'notifications' => [

        'completed' => [

            'title' => 'ኤክስፖርት ተጠናቋል',
            'actions' => [

                'download_csv' => [

                    'label' => '.csv አውርድ',
                ],
                'download_xlsx' => [

                    'label' => '.xlsx አውርድ',
                ],
            ],
        ],
        'max_rows' => [

            'title' => 'ይህ በጣም ትልቅ ነው',
            'body' => 'በአንድ ጊዜ ከ 1 ረድፍ በላይ ኤክስፖርት ማድረግ አይችሉም።|ረድፎችን በአንድ ጊዜ ከ:count  በላይ ኤክስፖርት ማድረግ አይችሉም።',
        ],
        'started' => [

            'title' => 'ኤክስፖርት ተጀምሮዋል',
            'body' => 'ኤክስፖርቱ ተጀምሯል እና 1 ረድፍ ከበስተጀርባ ይካሄዳል።|ኤክስፖርቱ ጀምሯል እና :count ረድፎች ከበስተጀርባ ይከናወናሉ.',
        ],
    ],
    'file_name' => 'export-:export_id-:model',
];
