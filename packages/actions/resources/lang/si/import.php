<?php

return [

    'label' => ':label ආනයනය කරන්න',

    'modal' => [

        'heading' => ':label ආනයනය කරන්න',

        'form' => [

            'file' => [

                'label' => 'ගොනුව',

                'placeholder' => 'CSV ගොනුවක් උඩුගත කරන්න',

                'rules' => [
                    'duplicate_columns' => '{0} ගොනුවේ හිස් තීරු ශීර්ෂයක් එකකට වඩා අඩංගු නොවිය යුතුය.|{1,*} ගොනුවේ අනුපිටපත් තීරු ශීර්ෂ අඩංගු නොවිය යුතුය: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'තීරු',
                'placeholder' => 'තීරුවක් තෝරන්න',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'උදාහරණාත්මක CSV ගොනුව බාගත කරන්න',
            ],

            'import' => [
                'label' => 'ආනයනය කරන්න',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'ආනයනය සම්පූර්ණයි',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'අසාර්ථක වූ පේළිය පිළිබඳ තොරතුරු බාගන්න|අසාර්ථක වූ පේළි පිළිබඳ තොරතුරු බාගන්න',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'උඩුගත කළ CSV ගොනුව විශාල වැඩියි',
            'body' => 'ඔබට එකවර පේළි 1 කට වඩා ආනයනය කළ නොහැක.|ඔබට එකවර පේළි :count කට වඩා ආනයනය කළ නොහැක.',
        ],

        'started' => [
            'title' => 'ආනයනය ආරම්භ විය',
            'body' => 'ඔබගේ ආනයනය ආරම්භ වී ඇති අතර පේළි 1 ක් පසුබිමේ සැකසෙනු ඇත.|ඔබගේ ආනයනය ආරම්භ වී ඇති අතර පේළි :count ක් පසුබිමේ සැකසෙනු ඇත.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'දෝෂය',
        'system_error' => 'පද්ධති දෝෂයකි, කරුණාකර සහාය සම්බන්ධ කරගන්න.',
        'column_mapping_required_for_new_record' => ':attribute තීරුව ගොනුවේ කිසිදු තීරුවකට සිතියම්ගත කර නොමැති අතර, නව වාර්තා සෑදීමට එය අවශ්‍ය වේ.',
    ],

];
