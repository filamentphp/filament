<?php

use Filament\Support\Contracts\LoadingIndicator;
use Filament\Support\View\DefaultLoadingIndicator;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

describe('container bindings', function (): void {
    it('does not share the `originalRequest` binding between Livewire component snapshots', function (): void {
        expect(app()->isShared('originalRequest'))->toBeFalse();
    });

    it('binds the `LoadingIndicator` contract to `DefaultLoadingIndicator` by default', function (): void {
        expect(app(LoadingIndicator::class))
            ->toBeInstanceOf(DefaultLoadingIndicator::class);
    });

    it('allows swapping the loading indicator implementation via the container', function (): void {
        app()->bind(LoadingIndicator::class, CustomLoadingIndicator::class);

        expect(app(LoadingIndicator::class))
            ->toBeInstanceOf(CustomLoadingIndicator::class);

        expect(app(LoadingIndicator::class)->toHtml(new ComponentAttributeBag))
            ->toBe('<div>Custom</div>');
    });
});

describe('`Str::sanitizeHtml()`', function (): void {
    it('preserves safe HTML elements', function (): void {
        $html = '<p>Hello <strong>world</strong>, <em>this</em> is <a href="https://example.com">safe</a>.</p>';

        expect(Str::sanitizeHtml($html))
            ->toBe($html);
    });

    it('preserves allowed `data-*` attributes', function (): void {
        $html = '<span data-type="mergeTag" data-id="user_name" data-color="red">Tag</span>';

        expect(Str::sanitizeHtml($html))
            ->toContain('data-type="mergeTag"')
            ->toContain('data-id="user_name"')
            ->toContain('data-color="red"');
    });

    it('preserves `style` attributes', function (): void {
        $html = '<span style="color: red;">Hello</span>';

        expect(Str::sanitizeHtml($html))
            ->toBe($html);
    });

    it('preserves `class` attributes', function (): void {
        $html = '<div class="fi-prose">Hello</div>';

        expect(Str::sanitizeHtml($html))
            ->toBe($html);
    });

    it('preserves `width` and `height` on images', function (): void {
        $html = '<img src="photo.jpg" width="100" height="50" />';

        expect(Str::sanitizeHtml($html))
            ->toContain('width="100"')
            ->toContain('height="50"');
    });

    it('preserves rich editor grid attributes', function (): void {
        $html = '<div data-cols="3" data-col-span="2" data-from-breakpoint="md">Content</div>';

        expect(Str::sanitizeHtml($html))
            ->toContain('data-cols="3"')
            ->toContain('data-col-span="2"')
            ->toContain('data-from-breakpoint="md"');
    });

    it('strips `<script>` tags', function (): void {
        expect(Str::sanitizeHtml('<p>Hello</p><script>alert("xss")</script>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `<script>` tags with attributes', function (): void {
        expect(Str::sanitizeHtml('<p>Hello</p><script src="evil.js"></script>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `javascript:` links', function (): void {
        expect(Str::sanitizeHtml('<a href="javascript:alert(1)">click</a>'))
            ->toBe('<a>click</a>');
    });

    it('strips `javascript:` links with whitespace obfuscation', function (): void {
        expect(Str::sanitizeHtml('<a href="  javascript:alert(1)">click</a>'))
            ->toBe('<a>click</a>');
    });

    it('strips `onclick` attributes', function (): void {
        expect(Str::sanitizeHtml('<p onclick="alert(1)">Hello</p>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `onerror` attributes', function (): void {
        expect(Str::sanitizeHtml('<img src="x" onerror="alert(1)">'))
            ->toBe('<img src="x" />');
    });

    it('strips `onload` attributes', function (): void {
        expect(Str::sanitizeHtml('<img src="x" onload="alert(1)">'))
            ->toBe('<img src="x" />');
    });

    it('strips `onmouseover` attributes', function (): void {
        expect(Str::sanitizeHtml('<p onmouseover="alert(1)">Hello</p>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `onfocus` attributes with `autofocus`', function (): void {
        expect(Str::sanitizeHtml('<input onfocus="alert(1)" autofocus>'))
            ->not->toContain('onfocus');
    });

    it('strips `<iframe>` tags', function (): void {
        expect(Str::sanitizeHtml('<p>Hello</p><iframe src="evil.html"></iframe>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `<object>` tags', function (): void {
        expect(Str::sanitizeHtml('<p>Hello</p><object data="evil.swf"></object>'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `<embed>` tags', function (): void {
        expect(Str::sanitizeHtml('<p>Hello</p><embed src="evil.swf">'))
            ->toBe('<p>Hello</p>');
    });

    it('strips `<form>` tags', function (): void {
        expect(Str::sanitizeHtml('<form action="evil.php"><input></form>'))
            ->not->toContain('<form');
    });

    it('strips `<svg>` with inline script', function (): void {
        $result = Str::sanitizeHtml('<svg onload="alert(1)"><circle r="10"></circle></svg>');

        expect($result)
            ->not->toContain('onload')
            ->not->toContain('alert');
    });

    it('strips `<math>` with embedded `<script>`', function (): void {
        expect(Str::sanitizeHtml('<math><mtext><script>alert(1)</script></mtext></math>'))
            ->not->toContain('<script');
    });

    it('strips `data:` URIs in links', function (): void {
        expect(Str::sanitizeHtml('<a href="data:text/html,<script>alert(1)</script>">click</a>'))
            ->toBe('<a>click</a>');
    });

    it('strips `vbscript:` links', function (): void {
        expect(Str::sanitizeHtml('<a href="vbscript:alert(1)">click</a>'))
            ->toBe('<a>click</a>');
    });
});

describe('`Str::sanitizeUrl()`', function (): void {
    it('returns `null` for `null` input', function (): void {
        expect(Str::sanitizeUrl(null))->toBeNull();
    });

    it('returns `null` for an empty string', function (): void {
        expect(Str::sanitizeUrl(''))->toBeNull();
    });

    it('returns `null` for a whitespace-only string', function (): void {
        expect(Str::sanitizeUrl('   '))->toBeNull();
    });

    it('allows absolute `http` URLs', function (): void {
        expect(Str::sanitizeUrl('http://example.com/file.pdf'))
            ->toBe('http://example.com/file.pdf');
    });

    it('allows absolute `https` URLs', function (): void {
        expect(Str::sanitizeUrl('https://example.com/file.pdf'))
            ->toBe('https://example.com/file.pdf');
    });

    it('allows `https` URLs with query strings and fragments', function (): void {
        expect(Str::sanitizeUrl('https://example.com/path?foo=bar&baz=1#section'))
            ->toBe('https://example.com/path?foo=bar&baz=1#section');
    });

    it('allows `https` URLs with ports and userinfo', function (): void {
        expect(Str::sanitizeUrl('https://user:pass@example.com:8443/file.pdf'))
            ->toBe('https://user:pass@example.com:8443/file.pdf');
    });

    it('allows mixed-case `HTTP`/`HTTPS` schemes', function (): void {
        expect(Str::sanitizeUrl('HTTP://example.com'))->toBe('HTTP://example.com')
            ->and(Str::sanitizeUrl('HtTpS://example.com'))->toBe('HtTpS://example.com');
    });

    it('allows root-relative URLs', function (): void {
        expect(Str::sanitizeUrl('/storage/files/abc.pdf'))
            ->toBe('/storage/files/abc.pdf');
    });

    it('allows path-relative URLs', function (): void {
        expect(Str::sanitizeUrl('files/abc.pdf'))->toBe('files/abc.pdf');
    });

    it('allows protocol-relative URLs', function (): void {
        expect(Str::sanitizeUrl('//cdn.example.com/file.pdf'))
            ->toBe('//cdn.example.com/file.pdf');
    });

    it('allows query-only and fragment-only URLs', function (): void {
        expect(Str::sanitizeUrl('?download=1'))->toBe('?download=1')
            ->and(Str::sanitizeUrl('#section'))->toBe('#section');
    });

    it('allows URLs whose path contains a `:`', function (): void {
        expect(Str::sanitizeUrl('/files/folder:name/abc.pdf'))
            ->toBe('/files/folder:name/abc.pdf');
    });

    it('allows `http` URLs whose userinfo looks like a dangerous scheme', function (): void {
        expect(Str::sanitizeUrl('http://javascript:alert(1)@example.com'))
            ->toBe('http://javascript:alert(1)@example.com');
    });

    it('passes legitimate URLs with multiple query parameters through unchanged', function (): void {
        expect(Str::sanitizeUrl('https://example.com/?a=1&b=2'))->toBe('https://example.com/?a=1&b=2');
        expect(Str::sanitizeUrl('https://example.com/search?q=hello&page=2&sort=desc'))
            ->toBe('https://example.com/search?q=hello&page=2&sort=desc');
    });

    it('passes legitimate URLs containing escaped ampersand entities through unchanged', function (): void {
        expect(Str::sanitizeUrl('https://example.com/?a=1&amp;b=2'))->toBe('https://example.com/?a=1&amp;b=2');
    });

    it('passes URLs whose query string literally contains the text `javascript:` through unchanged', function (): void {
        expect(Str::sanitizeUrl('https://example.com/?q=javascript%3Aalert(1)'))
            ->toBe('https://example.com/?q=javascript%3Aalert(1)');
    });

    it('does not recursively decode double-encoded entities — single decoding matches browser behaviour', function (): void {
        expect(Str::sanitizeUrl('https://example.com/?q=java&amp;#9;script:1'))
            ->toBe('https://example.com/?q=java&amp;#9;script:1');
    });

    it('does not decode named HTML entities that require a trailing semicolon when the semicolon is missing', function (): void {
        // `&Tab` without trailing `;` is not a valid HTML5 entity. Both the browser
        // and `html_entity_decode(ENT_HTML5)` leave it as literal text.
        $result = Str::sanitizeUrl('https://example.com/?q=&Tab');
        expect($result)->toBe('https://example.com/?q=&Tab');
    });

    it('passes through URLs whose path or fragment legitimately contains a literal `:`', function (): void {
        expect(Str::sanitizeUrl('https://example.com/path:with:colons'))
            ->toBe('https://example.com/path:with:colons');
        expect(Str::sanitizeUrl('https://example.com/#section:1'))
            ->toBe('https://example.com/#section:1');
    });

    it('handles URLs that consist of only a scheme and colon, with no body', function (): void {
        expect(Str::sanitizeUrl('https:'))->toBe('https:');
        expect(Str::sanitizeUrl('javascript:'))->toBeNull();
    });

    it('preserves long URL paths', function (): void {
        $longUrl = 'https://example.com/' . str_repeat('a', 10000);

        expect(Str::sanitizeUrl($longUrl))->toBe($longUrl);
    });

    it('does not naively decode percent-encoded schemes', function (): void {
        // The browser does not percent-decode the scheme, so this remains schemeless.
        expect(Str::sanitizeUrl('%6Aavascript:alert(1)'))
            ->toBe('%6Aavascript:alert(1)');
    });

    it('preserves percent-encoded whitespace inside a safe URL path', function (): void {
        expect(Str::sanitizeUrl('https://example.com/file%20name.pdf'))
            ->toBe('https://example.com/file%20name.pdf');
    });

    it('rejects plain `javascript:` URLs', function (): void {
        expect(Str::sanitizeUrl('javascript:alert(1)'))->toBeNull();
    });

    it('rejects plain `data:` URLs', function (): void {
        expect(Str::sanitizeUrl('data:text/html,<script>alert(1)</script>'))
            ->toBeNull();
    });

    it('rejects `data:` image URLs', function (): void {
        expect(Str::sanitizeUrl('data:image/svg+xml;base64,PHN2Zy8+'))->toBeNull();
    });

    it('rejects plain `vbscript:` URLs', function (): void {
        expect(Str::sanitizeUrl('vbscript:msgbox(1)'))->toBeNull();
    });

    it('rejects `file:` URLs', function (): void {
        expect(Str::sanitizeUrl('file:///etc/passwd'))->toBeNull();
    });

    it('rejects `ftp:` URLs', function (): void {
        expect(Str::sanitizeUrl('ftp://example.com/foo'))->toBeNull();
    });

    it('rejects `mailto:` URLs', function (): void {
        expect(Str::sanitizeUrl('mailto:hacker@example.com'))->toBeNull();
    });

    it('rejects `tel:` URLs', function (): void {
        expect(Str::sanitizeUrl('tel:+15551234567'))->toBeNull();
    });

    it('rejects `blob:` URLs', function (): void {
        expect(Str::sanitizeUrl('blob:https://example.com/uuid'))->toBeNull();
    });

    it('rejects `about:` URLs', function (): void {
        expect(Str::sanitizeUrl('about:blank'))->toBeNull();
    });

    it('rejects `javascript:` with mixed case', function (): void {
        expect(Str::sanitizeUrl('JavaScript:alert(1)'))->toBeNull()
            ->and(Str::sanitizeUrl('JAVASCRIPT:alert(1)'))->toBeNull()
            ->and(Str::sanitizeUrl('jAvAsCrIpT:alert(1)'))->toBeNull();
    });

    it('rejects `javascript:` with leading spaces', function (): void {
        expect(Str::sanitizeUrl('  javascript:alert(1)'))->toBeNull();
    });

    it('rejects `javascript:` with leading tabs', function (): void {
        expect(Str::sanitizeUrl("\tjavascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with leading newlines', function (): void {
        expect(Str::sanitizeUrl("\njavascript:alert(1)"))->toBeNull()
            ->and(Str::sanitizeUrl("\rjavascript:alert(1)"))->toBeNull()
            ->and(Str::sanitizeUrl("\r\njavascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with leading null bytes', function (): void {
        expect(Str::sanitizeUrl("\0javascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with leading form-feed and vertical-tab', function (): void {
        expect(Str::sanitizeUrl("\fjavascript:alert(1)"))->toBeNull()
            ->and(Str::sanitizeUrl("\x0Bjavascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with leading mixed control characters', function (): void {
        expect(Str::sanitizeUrl("\0\t\r\n  javascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with an embedded tab inside the scheme', function (): void {
        expect(Str::sanitizeUrl("java\tscript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with an embedded newline inside the scheme', function (): void {
        expect(Str::sanitizeUrl("java\nscript:alert(1)"))->toBeNull()
            ->and(Str::sanitizeUrl("java\rscript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with an embedded null byte inside the scheme', function (): void {
        expect(Str::sanitizeUrl("ja\0vascript:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with `\x7F` DEL inside the scheme', function (): void {
        expect(Str::sanitizeUrl("javascript\x7F:alert(1)"))->toBeNull();
    });

    it('rejects URLs containing whitespace after HTML entity decoding', function (): void {
        expect(Str::sanitizeUrl('java&#x09;script:alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('java&#10;script:alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('java&#13;script:alert(1)'))->toBeNull();
    });

    it('rejects URLs containing encoded control characters', function (): void {
        expect(Str::sanitizeUrl('java%09script:alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('java%0Ascript:alert(1)'))->toBeNull();
    });

    it('rejects URLs containing raw control characters', function (): void {
        expect(Str::sanitizeUrl("javascript\x7F:alert(1)"))->toBeNull();
    });

    it('rejects URLs containing HTML entity encoded separators', function (): void {
        expect(Str::sanitizeUrl('javascript&colon;alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('javascript&#58;alert(1)'))->toBeNull();
    });

    it('rejects URLs containing control characters after HTML entity decoding', function (): void {
        expect(Str::sanitizeUrl('javascript&#x7F;:alert(1)'))->toBeNull();
    });

    it('rejects URLs containing HTML5 named character entities for control characters', function (): void {
        expect(Str::sanitizeUrl('java&Tab;script:alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('java&NewLine;script:alert(1)'))->toBeNull();
    });

    it('rejects schemes assembled entirely from numeric HTML entities', function (): void {
        // `&#106;` is `j`, `&#x61;` is `a`. The scheme itself is hidden in entities.
        expect(Str::sanitizeUrl('&#106;&#x61;v&#97;script:alert(1)'))->toBeNull();
    });

    it('rejects schemes assembled from mixed entity and percent encoding', function (): void {
        expect(Str::sanitizeUrl('java&#9;script%3Aalert(1)'))->toBeNull();
    });

    it('rejects a null byte hidden in a numeric entity', function (): void {
        // `html_entity_decode()` otherwise replaces NUL with U+FFFD and hides the attack.
        expect(Str::sanitizeUrl('java&#0;script:alert(1)'))->toBeNull();
        expect(Str::sanitizeUrl('java&#x00;script:alert(1)'))->toBeNull();
    });

    it('rejects schemes containing `+` that are not on the allowlist', function (): void {
        expect(Str::sanitizeUrl('git+ssh://example.com/repo.git'))->toBeNull();
        expect(Str::sanitizeUrl('coap+tcp://example.com/'))->toBeNull();
    });

    it('rejects `javascript:` with whitespace before the colon', function (): void {
        expect(Str::sanitizeUrl('javascript :alert(1)'))->toBeNull()
            ->and(Str::sanitizeUrl("javascript\t:alert(1)"))->toBeNull();
    });

    it('rejects `javascript:` with every kind of obfuscation combined', function (): void {
        expect(Str::sanitizeUrl("\0\t  Ja\nVa\rScRiPt\t:alert(1)"))->toBeNull();
    });

    it('rejects `data:` with case and whitespace variants', function (): void {
        expect(Str::sanitizeUrl('DATA:text/html,foo'))->toBeNull()
            ->and(Str::sanitizeUrl('  Data:text/html,foo'))->toBeNull()
            ->and(Str::sanitizeUrl("da\tta:text/html,foo"))->toBeNull();
    });

    it('rejects `vbscript:` with case and whitespace variants', function (): void {
        expect(Str::sanitizeUrl('VBScript:msgbox(1)'))->toBeNull()
            ->and(Str::sanitizeUrl("\tvbscript:msgbox(1)"))->toBeNull();
    });

    it('rejects URLs with only a dangerous scheme and no body', function (): void {
        expect(Str::sanitizeUrl('javascript:'))->toBeNull()
            ->and(Str::sanitizeUrl('data:'))->toBeNull();
    });

    it('rejects URLs with a dangerous scheme followed by a fragment', function (): void {
        expect(Str::sanitizeUrl('javascript:void(0)#foo'))->toBeNull();
    });

    it('rejects URLs with leading whitespace', function (): void {
        expect(Str::sanitizeUrl('  https://example.com'))->toBeNull();
    });

    it('rejects URLs with embedded control characters', function (): void {
        expect(Str::sanitizeUrl("http://exa\tmple.com/\nfile.pdf"))->toBeNull()
            ->and(Str::sanitizeUrl("http://example.com/\x00file.pdf"))->toBeNull()
            ->and(Str::sanitizeUrl("http://example.com\x7F"))->toBeNull();
    });

    it('returns `null` when the URL is only control characters', function (): void {
        expect(Str::sanitizeUrl("\x01"))->toBeNull()
            ->and(Str::sanitizeUrl("\x00\x01\x02"))->toBeNull()
            ->and(Str::sanitizeUrl("\t\r\n"))->toBeNull();
    });

    it('rejects URLs containing embedded ASCII spaces', function (): void {
        expect(Str::sanitizeUrl('http://example.com /path'))->toBeNull()
            ->and(Str::sanitizeUrl('https://exa mple.com'))->toBeNull()
            ->and(Str::sanitizeUrl('http://example.com/path?q=hello world'))->toBeNull();
    });

    it('rejects URLs with trailing whitespace', function (): void {
        expect(Str::sanitizeUrl('https://example.com '))->toBeNull()
            ->and(Str::sanitizeUrl("https://example.com\t"))->toBeNull();
    });

    it('exposes a `Stringable` macro', function (): void {
        expect(Str::of('javascript:alert(1)')->sanitizeUrl())
            ->toBeInstanceOf(Stringable::class)
            ->and((string) Str::of('javascript:alert(1)')->sanitizeUrl())
            ->toBe('')
            ->and((string) Str::of('https://example.com')->sanitizeUrl())
            ->toBe('https://example.com');
    });

    it('allows extra schemes when passed via the `$allowedSchemes` argument', function (): void {
        expect(Str::sanitizeUrl('mailto:foo@example.com', ['http', 'https', 'mailto']))
            ->toBe('mailto:foo@example.com')
            ->and(Str::sanitizeUrl('tel:+15551234567', ['http', 'https', 'tel']))
            ->toBe('tel:+15551234567');
    });

    it('still rejects dangerous schemes when an opt-in list is supplied', function (): void {
        expect(Str::sanitizeUrl('javascript:alert(1)', ['http', 'https', 'mailto']))
            ->toBeNull()
            ->and(Str::sanitizeUrl('data:text/html,foo', ['http', 'https', 'mailto', 'tel']))
            ->toBeNull();
    });

    it('matches `$allowedSchemes` case-insensitively', function (): void {
        expect(Str::sanitizeUrl('MAILTO:foo@example.com', ['mailto']))
            ->toBe('MAILTO:foo@example.com')
            ->and(Str::sanitizeUrl('mailto:foo@example.com', ['MailTo']))
            ->toBe('mailto:foo@example.com');
    });

    it('rejects every scheme when `$allowedSchemes` is empty but keeps relative URLs', function (): void {
        expect(Str::sanitizeUrl('http://example.com', []))->toBeNull()
            ->and(Str::sanitizeUrl('https://example.com', []))->toBeNull()
            ->and(Str::sanitizeUrl('/storage/file.pdf', []))->toBe('/storage/file.pdf')
            ->and(Str::sanitizeUrl('//cdn.example.com/file.pdf', []))->toBe('//cdn.example.com/file.pdf');
    });

    it('returns `null` when the allowlist is empty and the URL has an absolute scheme', function (): void {
        expect(Str::sanitizeUrl('https://example.com', []))->toBeNull();
        expect(Str::sanitizeUrl('http://example.com', []))->toBeNull();
    });

    it('rejects `http(s)` when only an opt-in scheme is listed', function (): void {
        expect(Str::sanitizeUrl('https://example.com', ['mailto']))->toBeNull();
    });

    it('rejects obfuscation even when checking against a custom `$allowedSchemes`', function (): void {
        expect(Str::sanitizeUrl("\tMaIl\nTo:foo@example.com", ['mailto']))->toBeNull();
    });

    it('passes `$allowedSchemes` through the `Stringable` macro', function (): void {
        expect((string) Str::of('mailto:foo@example.com')->sanitizeUrl(['mailto']))
            ->toBe('mailto:foo@example.com')
            ->and((string) Str::of('javascript:alert(1)')->sanitizeUrl(['mailto']))
            ->toBe('');
    });
});

describe('`Str::sanitizeCssColor()`', function (): void {
    it('returns `null` for `null` input', function (): void {
        expect(Str::sanitizeCssColor(null))->toBeNull();
    });

    it('returns `null` for an empty string', function (): void {
        expect(Str::sanitizeCssColor(''))->toBeNull();
    });

    it('returns `null` for a whitespace-only string', function (): void {
        expect(Str::sanitizeCssColor('   '))->toBeNull();
    });

    it('allows `#rgb` hex colors', function (): void {
        expect(Str::sanitizeCssColor('#f00'))->toBe('#f00');
    });

    it('allows `#rgba` hex colors', function (): void {
        expect(Str::sanitizeCssColor('#f00c'))->toBe('#f00c');
    });

    it('allows `#rrggbb` hex colors', function (): void {
        expect(Str::sanitizeCssColor('#ff0000'))->toBe('#ff0000');
    });

    it('allows `#rrggbbaa` hex colors', function (): void {
        expect(Str::sanitizeCssColor('#ff0000cc'))->toBe('#ff0000cc');
    });

    it('allows mixed-case hex colors', function (): void {
        expect(Str::sanitizeCssColor('#AbCdEf'))->toBe('#AbCdEf');
    });

    it('allows bare CSS keyword colors', function (): void {
        expect(Str::sanitizeCssColor('red'))->toBe('red')
            ->and(Str::sanitizeCssColor('transparent'))->toBe('transparent')
            ->and(Str::sanitizeCssColor('rebeccapurple'))->toBe('rebeccapurple');
    });

    it('allows `rgb()` and `rgba()` functional colors', function (): void {
        expect(Str::sanitizeCssColor('rgb(255, 0, 0)'))->toBe('rgb(255, 0, 0)')
            ->and(Str::sanitizeCssColor('rgba(255, 0, 0, 0.5)'))->toBe('rgba(255, 0, 0, 0.5)');
    });

    it('allows `hsl()` and `hsla()` functional colors', function (): void {
        expect(Str::sanitizeCssColor('hsl(120, 50%, 50%)'))->toBe('hsl(120, 50%, 50%)')
            ->and(Str::sanitizeCssColor('hsla(120, 50%, 50%, 0.5)'))->toBe('hsla(120, 50%, 50%, 0.5)');
    });

    it('allows space-separated CSS Color Level 4 `rgb()` and `hsl()` syntax', function (): void {
        expect(Str::sanitizeCssColor('rgb(255 0 0)'))->toBe('rgb(255 0 0)')
            ->and(Str::sanitizeCssColor('rgb(255 0 0 / 50%)'))->toBe('rgb(255 0 0 / 50%)')
            ->and(Str::sanitizeCssColor('hsl(120 50% 50%)'))->toBe('hsl(120 50% 50%)')
            ->and(Str::sanitizeCssColor('hsl(120deg 50% 50% / 0.5)'))->toBe('hsl(120deg 50% 50% / 0.5)');
    });

    it('allows `hwb()`, `lab()`, `lch()`, `oklab()`, `oklch()` and `color()` functional colors', function (): void {
        expect(Str::sanitizeCssColor('hwb(194 0% 0%)'))->toBe('hwb(194 0% 0%)')
            ->and(Str::sanitizeCssColor('lab(52.2% 40.1 59.9)'))->toBe('lab(52.2% 40.1 59.9)')
            ->and(Str::sanitizeCssColor('lch(52.2% 72.2 50)'))->toBe('lch(52.2% 72.2 50)')
            ->and(Str::sanitizeCssColor('oklab(0.4 0.1 0.1)'))->toBe('oklab(0.4 0.1 0.1)')
            ->and(Str::sanitizeCssColor('oklch(0.7 0.15 30)'))->toBe('oklch(0.7 0.15 30)')
            ->and(Str::sanitizeCssColor('color(display-p3 1 0 0)'))->toBe('color(display-p3 1 0 0)');
    });

    it('trims surrounding whitespace from valid colors', function (): void {
        expect(Str::sanitizeCssColor('  #ff0000  '))->toBe('#ff0000');
    });

    it('rejects values containing a `;` that would break out of the declaration', function (): void {
        expect(Str::sanitizeCssColor('red;position:fixed;inset:0;background-image:url(//attacker)'))
            ->toBeNull();
    });

    it('rejects functional notation containing `url()`', function (): void {
        expect(Str::sanitizeCssColor('rgb(0,0,0);background-image:url(//attacker)'))->toBeNull();
    });

    it('rejects functional notation containing a nested `(` or `)`', function (): void {
        expect(Str::sanitizeCssColor('rgb(calc(1))'))->toBeNull();
    });

    it('rejects functional notation containing a `:`', function (): void {
        expect(Str::sanitizeCssColor('rgb(0:0:0)'))->toBeNull();
    });

    it('rejects functional notation containing quotes', function (): void {
        expect(Str::sanitizeCssColor('rgb("0",0,0)'))->toBeNull()
            ->and(Str::sanitizeCssColor("rgb('0',0,0)"))->toBeNull();
    });

    it('rejects an unknown function name', function (): void {
        expect(Str::sanitizeCssColor('expression(alert(1))'))->toBeNull();
    });

    it('rejects a hex color with an invalid length', function (): void {
        expect(Str::sanitizeCssColor('#ff'))->toBeNull()
            ->and(Str::sanitizeCssColor('#fffff'))->toBeNull();
    });

    it('rejects a keyword containing non-alphabetic characters', function (): void {
        expect(Str::sanitizeCssColor('red;'))->toBeNull()
            ->and(Str::sanitizeCssColor('red 0'))->toBeNull();
    });

    it('allows a string-backed enum, using its value', function (): void {
        expect(Str::sanitizeCssColor(CssColorStringBackedEnum::Hex))->toBe('#ff0000')
            ->and(Str::sanitizeCssColor(CssColorStringBackedEnum::Keyword))->toBe('red')
            ->and(Str::sanitizeCssColor(CssColorStringBackedEnum::Untrimmed))->toBe('#00ff00');
    });

    it('rejects a string-backed enum with an unsafe value', function (): void {
        expect(Str::sanitizeCssColor(CssColorStringBackedEnum::Unsafe))->toBeNull();
    });

    it('rejects an int-backed enum, since its value is not a valid color', function (): void {
        expect(Str::sanitizeCssColor(CssColorIntBackedEnum::Red))->toBeNull();
    });

    it('exposes a `Stringable` macro', function (): void {
        expect(Str::of('#ff0000')->sanitizeCssColor())
            ->toBeInstanceOf(Stringable::class)
            ->and((string) Str::of('#ff0000')->sanitizeCssColor())
            ->toBe('#ff0000')
            ->and((string) Str::of('red;position:fixed')->sanitizeCssColor())
            ->toBe('');
    });

    it('returns an empty `Stringable` from the macro when the sanitizer returns `null`', function (): void {
        expect(Str::of('')->sanitizeCssColor())
            ->toBeInstanceOf(Stringable::class)
            ->and((string) Str::of('')->sanitizeCssColor())
            ->toBe('');
    });
});

enum CssColorStringBackedEnum: string
{
    case Hex = '#ff0000';
    case Keyword = 'red';
    case Untrimmed = '  #00ff00  ';
    case Unsafe = 'red;position:fixed';
}

enum CssColorIntBackedEnum: int
{
    case Red = 0xFF0000;
}

class CustomLoadingIndicator implements LoadingIndicator
{
    public function toHtml(ComponentAttributeBag $attributes): string
    {
        return '<div>Custom</div>';
    }
}
