<?php

return [

    'distinct' => [
        'must_be_selected' => 'Pelo menos um campo :attribute deve ser selecionado.',
        'only_one_must_be_selected' => 'Apenas um campo :attribute deve ser selecionado.',
    ],

    'date_range' => [
        'max_days' => 'O campo :attribute não deve ser maior que :days dia.|O campo :attribute não deve ser maior que :days dias.',
    ],

    'tampered_file_path' => 'O campo :attribute contém um caminho de arquivo que não é permitido.',

];
