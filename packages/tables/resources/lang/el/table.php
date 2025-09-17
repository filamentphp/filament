<?php

return [

    'column_manager' => [

        'heading' => 'Στήλες',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Προβολή :count λιγότερων',
                'expand_list' => 'Προβολή :count περισσότερων',
            ],

            'more_list_items' => 'και :count περισσότερα',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Επιλέξτε όλες τις εγγραφές για μαζικές ενέργειες.',
        ],

        'bulk_select_record' => [
            'label' => 'Επιλέξτε την εγγραφή :key για μαζικές ενέργειες.',
        ],

        'bulk_select_group' => [
            'label' => 'Επιλέξτε την ομάδα :title για μαζικές ενέργειες.',
        ],

        'search' => [
            'label' => 'Αναζήτηση',
            'placeholder' => 'Αναζήτηση',
            'indicator' => 'Αναζήτηση',
        ],

    ],

    'summary' => [

        'heading' => 'Σύνοψη',

        'subheadings' => [
            'all' => 'Όλα :label',
            'group' => 'σύνοψη :group',
            'page' => 'This page',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Μέσος όρος',
            ],

            'count' => [
                'label' => 'Σύνολο',
            ],

            'sum' => [
                'label' => 'Άθροισμα',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Τερματισμός αναδιάταξης',
        ],

        'enable_reordering' => [
            'label' => 'Αναδιάταξη εγγραφών',
        ],

        'filter' => [
            'label' => 'Φίλτρο',
        ],

        'group' => [
            'label' => 'Ομάδας',
        ],

        'open_bulk_actions' => [
            'label' => 'Μαζικές ενέργειες',
        ],

        'column_manager' => [
            'label' => 'Column manager',
        ],

    ],

    'empty' => [

        'heading' => 'Δεν υπάρχουν εγγραφές',

        'description' => 'Προσθέστε ένα/μία ":model" για να ξεκινήσετε.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Εφαρμογή φίλτρου',
            ],

            'remove' => [
                'label' => 'Αφαίρεση φίτλρου',
            ],

            'remove_all' => [
                'label' => 'Αφαίρεση φίτλρων',
                'tooltip' => 'Αφαίρεση φίτλρων',
            ],

            'reset' => [
                'label' => 'Επαναφορά',
            ],

        ],

        'heading' => 'Φίλτρα',

        'indicator' => 'Ενεργά φίλτρα',

        'multi_select' => [
            'placeholder' => 'Όλα',
        ],

        'select' => [
            'placeholder' => 'Όλα',
        ],

        'trashed' => [

            'label' => 'Διεγραμμένες εγγραφές',

            'only_trashed' => 'Μόνο διεγραμμένες εγγραφές',

            'with_trashed' => 'Σε συνδυασμό με διεγραμμένες εγγραφές',

            'without_trashed' => 'Χωρίς διεγραμμένες εγγραφές',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Ομαδοποίηση βάσει',
                'placeholder' => 'Ομαδοποίηση βάσει',
            ],

            'direction' => [

                'label' => 'Group direction',

                'options' => [
                    'asc' => 'Αύξουσα σειρά',
                    'desc' => 'Φθίνουσα σειρά',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Σύρετε και αποθέστε τις εγγραφές με τη σειρά.',

    'selection_indicator' => [

        'selected_count' => 'επιλογή 1 εγγραφής|επιλογή :count εγγραφών',

        'actions' => [

            'select_all' => [
                'label' => 'Επιλογή όλων :count',
            ],

            'deselect_all' => [
                'label' => 'Αποεπιλογή όλων',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Ταξινόμηση κατά',
            ],

            'direction' => [

                'label' => 'Κατεύθυνση ταξινόμησης',

                'options' => [
                    'asc' => 'Αύξουσα σειρά',
                    'desc' => 'Φθίνουσα σειρά',
                ],

            ],

        ],

    ],

];
