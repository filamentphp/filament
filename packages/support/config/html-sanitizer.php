<?php

return [

    /*
     * Maximum length in number of characters this sanitizer will accept as inputs.
     * Longer inputs will be truncated.
     *
     * This field is necessary to prevent a Denial of Service attack induced by extremely
     * long HTML inputs from users.
     */
    'max_input_length' => 20000,

    /*
     * List of extensions to enable on this sanitizer.
     */
    'extensions' => ['basic', 'list', 'table', 'image', 'code', 'iframe', 'details', 'extra'],

    /*
     * Configuration for specific tags.
     */
    'tags' => [
        'abbr' => [
            'allowed_attributes' => ['class'],
        ],
        'a' => [

            'allowed_attributes' => ['href', 'title', 'class'],

            /*
             * If an array is provided, links targeting other hosts than one in this array
             * will be disabled (the `href` attribute will be blank). This can be useful if you want
             * to prevent links targeting external websites. Keep null to allow all hosts.
             * Any allowed domain also includes its subdomains.
             *
             * Example:
             *      'allowed_hosts' => ['trusted1.com', 'google.com'],
             */
            'allowed_hosts' => null,

            /*
             * If false, all links containing a mailto target will be disabled (the `href` attribute
             * will be blank).
             */
            'allow_mailto' => true,

            /*
             * If true, all links targets using the HTTP protocol will be rewritten to use HTTPS instead.
             */
            'force_https' => false,

        ],
        'blockquote' => [
            'allowed_attributes' => ['class'],
        ],
        'br' => [
            'allowed_attributes' => ['class'],
        ],
        'caption' => [
            'allowed_attributes' => ['class'],
        ],
        'code' => [
            'allowed_attributes' => ['class'],
        ],
        'dd' => [
            'allowed_attributes' => ['class'],
        ],
        'del' => [
            'allowed_attributes' => ['class'],
        ],
        'details' => [
            'allowed_attributes' => ['open'],
        ],
        'div' => [
            'allowed_attributes' => ['class'],
        ],
        'dl' => [
            'allowed_attributes' => ['class'],
        ],
        'dt' => [
            'allowed_attributes' => ['class'],
        ],
        'em' => [
            'allowed_attributes' => ['class'],
        ],
        'figcaption' => [
            'allowed_attributes' => ['class'],
        ],
        'figure' => [
            'allowed_attributes' => ['class'],
        ],
        'h1' => [
            'allowed_attributes' => ['class'],
        ],
        'h2' => [
            'allowed_attributes' => ['class'],
        ],
        'h3' => [
            'allowed_attributes' => ['class'],
        ],
        'h4' => [
            'allowed_attributes' => ['class'],
        ],
        'h5' => [
            'allowed_attributes' => ['class'],
        ],
        'h6' => [
            'allowed_attributes' => ['class'],
        ],
        'hr' => [
            'allowed_attributes' => ['class'],
        ],
        'iframe' => [

            'allowed_attributes' => ['src', 'width', 'height', 'frameborder', 'title', 'allow', 'allowfullscreen', 'class'],

            /*
             * If an array is provided, iframes relying on other hosts than one in this array
             * will be disabled (the `src` attribute will be blank). This can be useful if you want
             * to prevent iframes contacting external websites.
             * Any allowed domain also includes its subdomains.
             *
             * Example:
             *      'allowed_hosts' => ['trusted1.com', 'google.com'],
             */
            'allowed_hosts' => null,

            /*
             * If true, all frames URLS using the HTTP protocol will be rewritten to use HTTPS instead.
             */
            'force_https' => false,

        ],
        'img' => [

            'allowed_attributes' => ['src', 'alt', 'title', 'class'],

            /*
             * If an array is provided, images relying on other hosts than one in this array
             * will be disabled (the `src` attribute will be blank). This can be useful if you want
             * to prevent images contacting external websites. Keep null to allow all hosts.
             * Any allowed domain also includes its subdomains.
             *
             * Example:
             *      'allowed_hosts' => ['trusted1.com', 'google.com'],
             */
            'allowed_hosts' => null,

            /*
             * If true, images data-uri URLs will be accepted.
             */
            'allow_data_uri' => false,

            /*
             * If true, all images URLs using the HTTP protocol will be rewritten to use HTTPS instead.
             */
            'force_https' => false,

        ],
        'i' => [
            'allowed_attributes' => ['class'],
        ],
        'li' => [
            'allowed_attributes' => ['class'],
        ],
        'ol' => [
            'allowed_attributes' => ['class'],
        ],
        'pre' => [
            'allowed_attributes' => ['class'],
        ],
        'p' => [
            'allowed_attributes' => ['class'],
        ],
        'q' => [
            'allowed_attributes' => ['class'],
        ],
        'rp' => [
            'allowed_attributes' => ['class'],
        ],
        'rt' => [
            'allowed_attributes' => ['class'],
        ],
        'ruby' => [
            'allowed_attributes' => ['class'],
        ],
        'small' => [
            'allowed_attributes' => ['class'],
        ],
        'span' => [
            'allowed_attributes' => ['class'],
        ],
        'strong' => [
            'allowed_attributes' => ['class'],
        ],
        'sub' => [
            'allowed_attributes' => ['class'],
        ],
        'summary' => [
            'allowed_attributes' => ['class'],
        ],
        'sup' => [
            'allowed_attributes' => ['class'],
        ],
        'table' => [
            'allowed_attributes' => ['class'],
        ],
        'tbody' => [
            'allowed_attributes' => ['class'],
        ],
        'td' => [
            'allowed_attributes' => ['class'],
        ],
        'tfoot' => [
            'allowed_attributes' => ['class'],
        ],
        'thead' => [
            'allowed_attributes' => ['class'],
        ],
        'th' => [
            'allowed_attributes' => ['class'],
        ],
        'tr' => [
            'allowed_attributes' => ['class'],
        ],
        'u' => [
            'allowed_attributes' => ['class'],
        ],
        'ul' => [
            'allowed_attributes' => ['class'],
        ],
    ],

];
