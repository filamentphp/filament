<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

uses(TestCase::class);

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

it('allows http URLs whose userinfo looks like a dangerous scheme', function (): void {
    expect(Str::sanitizeUrl('http://javascript:alert(1)@example.com'))
        ->toBe('http://javascript:alert(1)@example.com');
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

it('rejects `javascript:` with embedded tab inside the scheme', function (): void {
    expect(Str::sanitizeUrl("java\tscript:alert(1)"))->toBeNull();
});

it('rejects `javascript:` with embedded newline inside the scheme', function (): void {
    expect(Str::sanitizeUrl("java\nscript:alert(1)"))->toBeNull()
        ->and(Str::sanitizeUrl("java\rscript:alert(1)"))->toBeNull();
});

it('rejects `javascript:` with embedded null byte inside the scheme', function (): void {
    expect(Str::sanitizeUrl("ja\0vascript:alert(1)"))->toBeNull();
});

it('rejects `javascript:` with `\x7F` DEL inside the scheme', function (): void {
    expect(Str::sanitizeUrl("javascript\x7F:alert(1)"))->toBeNull();
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
        ->and(Str::sanitizeUrl("  Data:text/html,foo"))->toBeNull()
        ->and(Str::sanitizeUrl("da\tta:text/html,foo"))->toBeNull();
});

it('rejects `vbscript:` with case and whitespace variants', function (): void {
    expect(Str::sanitizeUrl('VBScript:msgbox(1)'))->toBeNull()
        ->and(Str::sanitizeUrl("\tvbscript:msgbox(1)"))->toBeNull();
});

it('does not naively decode percent-encoded schemes', function (): void {
    // The browser does not percent-decode the scheme, so `%6Aavascript:`
    // is treated as a scheme literally named `%6Aavascript`, which is not
    // registered. The allowlist still rejects it because it isn't `http`
    // or `https`, but the regex sees a leading `%` and matches no scheme,
    // so the URL is also considered schemeless. Either outcome is safe.
    expect(Str::sanitizeUrl('%6Aavascript:alert(1)'))
        ->toBe('%6Aavascript:alert(1)');
});

it('rejects URLs with only a dangerous scheme and no body', function (): void {
    expect(Str::sanitizeUrl('javascript:'))->toBeNull()
        ->and(Str::sanitizeUrl('data:'))->toBeNull();
});

it('rejects URLs with a dangerous scheme followed by a fragment', function (): void {
    expect(Str::sanitizeUrl('javascript:void(0)#foo'))->toBeNull();
});

it('preserves percent-encoded whitespace inside a safe URL path', function (): void {
    expect(Str::sanitizeUrl('https://example.com/file%20name.pdf'))
        ->toBe('https://example.com/file%20name.pdf');
});

it('rejects URLs with leading whitespace', function (): void {
    // Legitimate URLs do not carry leading whitespace — rejecting up front
    // matches Symfony's HtmlSanitizer and forces callers to hand us a clean
    // value rather than relying on silent rewrites.
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
    // Legitimate URLs percent-encode spaces; a raw space anywhere in the
    // URL is either a typo or an obfuscation attempt that could resolve to
    // a different host than the visible string suggests.
    expect(Str::sanitizeUrl('http://example.com /path'))->toBeNull()
        ->and(Str::sanitizeUrl('https://exa mple.com'))->toBeNull()
        ->and(Str::sanitizeUrl('http://example.com/path?q=hello world'))->toBeNull();
});

it('rejects URLs with trailing whitespace', function (): void {
    expect(Str::sanitizeUrl('https://example.com '))->toBeNull()
        ->and(Str::sanitizeUrl("https://example.com\t"))->toBeNull();
});

it('still accepts percent-encoded spaces in the path', function (): void {
    // %20 is the safe, encoded form — only raw whitespace is rejected.
    expect(Str::sanitizeUrl('https://example.com/file%20name.pdf'))
        ->toBe('https://example.com/file%20name.pdf');
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
