<?php

use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;

uses(TestCase::class);

it('renders the proposed email literally in HTML and plaintext mail', function (string $newEmail): void {
    config()->set('mail.default', 'array');
    config()->set('app.name', 'Filament');

    app(ChannelManager::class)->sendNow(
        (new AnonymousNotifiable)->route('mail', 'current@example.com'),
        new NoticeOfEmailChangeRequest($newEmail, 'https://example.com/block'),
    );

    $message = app('mailer')->getSymfonyTransport()->messages()->sole()->getOriginalMessage();
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">' . $message->getHtmlBody());
    $xpath = new DOMXPath($document);

    expect($document->textContent)->toContain($newEmail)
        ->and($message->getTextBody())->toContain($newEmail)
        ->and($xpath->query('//em | //code | //img'))->toHaveCount(0)
        ->and($xpath->query('//a[not(@href="https://example.com/block") and not(@href="http://localhost")]'))->toHaveCount(0)
        ->and($xpath->query('//body//style'))->toHaveCount(0);
})->with('proposed email addresses');

it('preserves folded quoted addresses without interpreting Markdown', function (string $newEmail): void {
    config()->set('mail.default', 'array');

    app(ChannelManager::class)->sendNow(
        (new AnonymousNotifiable)->route('mail', 'current@example.com'),
        new NoticeOfEmailChangeRequest($newEmail, 'https://example.com/block'),
    );

    $message = app('mailer')->getSymfonyTransport()->messages()->sole()->getOriginalMessage();
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">' . $message->getHtmlBody());

    expect(str_replace("\r\n", "\n", $document->textContent))->toContain(str_replace("\r\n", "\n", $newEmail))
        ->and($message->getTextBody())->toContain($newEmail)
        ->and((new DOMXPath($document))->query('//a[@href="https://example.org"]'))->toHaveCount(0);
})->with([
    'folded line' => "\"name\r\n [Sign in](https://example.org)\"@example.com",
    'blank line' => "\"name\r\n\r\n [Sign in](https://example.org)\"@example.com",
]);
