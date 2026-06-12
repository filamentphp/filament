<?php

return [

    'label' => 'インポート',

    'modal' => [

        'heading' => 'インポート :label',

        'form' => [

            'file' => [
                'label' => 'File',
                'placeholder' => 'CSVファイルをアップロード',

                'rules' => [
                    'duplicate_columns' => '{0} 空の列ヘッダーが複数存在してはいけません。|{1,*} 次の列ヘッダーが重複しています: :columns。',
                ],

            ],

            'columns' => [
                'label' => 'カラム',
                'placeholder' => '列を選択',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'サンプルCSVファイルをダウンロード',
            ],

            'import' => [
                'label' => 'インポート',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'インポート完了',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => '失敗した行に関する情報のダウンロード',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'CSVファイルが大きすぎます',
            'body' => '一度に:count行を超える行をインポートすることはできません。',
        ],

        'started' => [
            'title' => 'インポート開始',
            'body' => 'インポートが開始され、:count行がバックグラウンドで処理されます。',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'エラー',
        'system_error' => 'システムエラーです。サポートにお問い合わせください。',
        'column_mapping_required_for_new_record' => ':attribute 列がファイル内のどの列にも対応付けられていませんが、新規レコードの作成には必須です。',
    ],

];
