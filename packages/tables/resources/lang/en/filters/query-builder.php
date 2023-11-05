<?php

return [

    'form' => [

        'or_groups' => [

            'label' => 'Groups',

            'block' => [
                'label' => 'Disjunction (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Rules',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(No rules)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Add rule',
        ],

        'add_rule_group' => [
            'label' => 'Add rule group',
        ],

    ],

];
