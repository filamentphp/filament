<?php

dataset('proposed email addresses', [
    'ordinary' => 'new@example.com',
    'quoted Markdown link' => '"unchanged. [Click here](https://example.com) "@example.com',
    'quoted Markdown image' => '"![image](https://example.com/image.png) "@example.com',
    'Markdown emphasis and code' => '*b*`code`@example.com',
    'HTML link' => '"<a href=https://example.com>Sign in</a>"@example.com',
    'HTML image' => '"<img src=https://example.com/image.png>"@example.com',
    'HTML styles' => '"<style>body{display:none}</style>"@example.com',
    'plus addressing and underscores' => 'first_last+tag@example.com',
    'Unicode' => 'δοκιμή+日本語😀@example.com',
    'Unicode outside ASCII' => 'quotedéÿ@example.com',
    'valid punctuation' => '!#$%&\'*+-/=?^_`{|}~@example.com',
    'HTML emphasis and code' => '"<em>name</em><code>value</code>"@example.com',
    'entity-like text' => '"&lt;b&gt;&#42;&amp;"@example.com',
    'quoted backslash' => '"quoted\\\\local"@example.com',
    'quoted backslashes before Markdown syntax' => '"quoted\\\\[literal]"@example.com',
]);
