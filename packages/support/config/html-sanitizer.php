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
            'allowed_attributes' => ['class', 'style'],
        ],
        'a' => [

            'allowed_attributes' => ['href', 'title', 'class', 'style'],

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
            'allowed_attributes' => ['class', 'style'],
        ],
        'br' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'caption' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'code' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'dd' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'del' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'details' => [
            'allowed_attributes' => ['open', 'style'],
        ],
        'div' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'dl' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'dt' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'em' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'figcaption' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'figure' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h1' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h2' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h3' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h4' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h5' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'h6' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'hr' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'iframe' => [

            'allowed_attributes' => ['src', 'width', 'height', 'frameborder', 'title', 'allow', 'allowfullscreen', 'class', 'style'],

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

            'allowed_attributes' => ['src', 'alt', 'title', 'class', 'style'],

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
            'allowed_attributes' => ['class', 'style'],
        ],
        'li' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'ol' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'pre' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'p' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'q' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'rp' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'rt' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'ruby' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'small' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'span' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'strong' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'sub' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'summary' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'sup' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'table' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'tbody' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'td' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'tfoot' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'thead' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'th' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'tr' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'u' => [
            'allowed_attributes' => ['class', 'style'],
        ],
        'ul' => [
            'allowed_attributes' => ['class', 'style'],
        ],
    ],

];
