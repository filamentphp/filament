<?php

namespace NamingConsistencyUpgrade;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanBeLabeledFrom;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Panel;
use Filament\Support\Concerns\CanBeLazy;

trait CustomLoading
{
    use CanBeLazy;
}

trait CustomResponsiveLabel
{
    use CanBeLabeledFrom {
        labeledFrom as responsiveLabelFrom;
    }
}

class CustomAction extends Action
{
    use CustomResponsiveLabel;
}

class CustomFileUpload extends FileUpload {}

class CustomMentionProvider extends MentionProvider {}

class CustomPanel extends Panel {}

class CustomEmailAuthentication extends EmailAuthentication {}

class Unrelated
{
    public function getResetPasswordRoutePrefix(): string
    {
        return 'unchanged';
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }

    public function isSvgEditingConfirmed(): bool
    {
        return false;
    }

    public function getChar(): string
    {
        return '#';
    }
}

function configure(Panel $panel, ?EmailAuthentication $emailAuthentication, CustomPanel $customPanel, CustomEmailAuthentication $customEmailAuthentication, Unrelated $unrelated): void
{
    $panel->getResetPasswordRoutePrefix();
    Panel::make()->passwordResetRoutePrefix('recovery')->getResetPasswordRoutePrefix();
    $emailAuthentication?->generateCodesUsing(callback: static fn (): string => '012345');
    EmailAuthentication::make()->generateCodesUsing(null)->generateCode();
    $customPanel->getResetPasswordRoutePrefix();
    $customEmailAuthentication->generateCodesUsing(null);
    $getPrefix = $panel->getResetPasswordRoutePrefix(...);
    $configureCode = $emailAuthentication?->generateCodesUsing(null);
    (static fn (): Panel => $panel)()->getResetPasswordRoutePrefix();
    array_map(static fn (EmailAuthentication $provider) => $provider->generateCodesUsing(null), [$emailAuthentication]);
    Panel::configureUsing(static fn (Panel $configuredPanel) => $configuredPanel->getResetPasswordRoutePrefix());
    $unrelated->getResetPasswordRoutePrefix();
    $unrelated->generateCodesUsing(null);
}

function configureUpload(FileUpload $upload, ?FileUpload $optionalUpload, CustomFileUpload $customUpload, Unrelated $unrelated): void
{
    $upload->isSvgEditingConfirmed();
    $optionalUpload?->isSvgEditingConfirmed();
    FileUpload::make('icon')->confirmSvgEditing()->isSvgEditingConfirmed();
    $customUpload->isSvgEditingConfirmed();
    $confirmation = $upload->isSvgEditingConfirmed(...);
    (static fn (): FileUpload => $upload)()->isSvgEditingConfirmed();
    array_map(static fn (FileUpload $component) => $component->isSvgEditingConfirmed(), [$upload]);
    FileUpload::configureUsing(static fn (FileUpload $configuredUpload) => $configuredUpload->isSvgEditingConfirmed());
    $unrelated->isSvgEditingConfirmed();
}

function configureMentions(MentionProvider $provider, ?MentionProvider $optionalProvider, CustomMentionProvider $customProvider, Unrelated $unrelated): void
{
    $provider->getChar();
    $optionalProvider?->getChar();
    MentionProvider::make(char: '#')->getChar();
    (new MentionProvider(char: '@'))->getChar();
    $customProvider->getChar();
    $getTrigger = $provider->getChar(...);
    (static fn (): MentionProvider => $provider)()->getChar();
    array_map(static fn (MentionProvider $configuredProvider) => $configuredProvider->getChar(), [$provider]);
    $unrelated->getChar();
}
-----
<?php

namespace NamingConsistencyUpgrade;

use Filament\Support\Concerns\CanBeLazyLoaded;
use Filament\Actions\Concerns\CanBeLabeledFromBreakpoint;
use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Panel;

trait CustomLoading
{
    use CanBeLazyLoaded;
}

trait CustomResponsiveLabel
{
    use CanBeLabeledFromBreakpoint {
        labeledFrom as responsiveLabelFrom;
    }
}

class CustomAction extends Action
{
    use CustomResponsiveLabel;
}

class CustomFileUpload extends FileUpload {}

class CustomMentionProvider extends MentionProvider {}

class CustomPanel extends Panel {}

class CustomEmailAuthentication extends EmailAuthentication {}

class Unrelated
{
    public function getResetPasswordRoutePrefix(): string
    {
        return 'unchanged';
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }

    public function isSvgEditingConfirmed(): bool
    {
        return false;
    }

    public function getChar(): string
    {
        return '#';
    }
}

function configure(Panel $panel, ?EmailAuthentication $emailAuthentication, CustomPanel $customPanel, CustomEmailAuthentication $customEmailAuthentication, Unrelated $unrelated): void
{
    $panel->getPasswordResetRoutePrefix();
    Panel::make()->passwordResetRoutePrefix('recovery')->getPasswordResetRoutePrefix();
    $emailAuthentication?->generateCodeUsing(callback: static fn (): string => '012345');
    EmailAuthentication::make()->generateCodeUsing(null)->generateCode();
    $customPanel->getPasswordResetRoutePrefix();
    $customEmailAuthentication->generateCodeUsing(null);
    $getPrefix = $panel->getPasswordResetRoutePrefix(...);
    $configureCode = $emailAuthentication?->generateCodeUsing(null);
    (static fn (): Panel => $panel)()->getPasswordResetRoutePrefix();
    array_map(static fn (EmailAuthentication $provider) => $provider->generateCodeUsing(null), [$emailAuthentication]);
    Panel::configureUsing(static fn (Panel $configuredPanel) => $configuredPanel->getPasswordResetRoutePrefix());
    $unrelated->getResetPasswordRoutePrefix();
    $unrelated->generateCodesUsing(null);
}

function configureUpload(FileUpload $upload, ?FileUpload $optionalUpload, CustomFileUpload $customUpload, Unrelated $unrelated): void
{
    $upload->shouldConfirmSvgEditing();
    $optionalUpload?->shouldConfirmSvgEditing();
    FileUpload::make('icon')->confirmSvgEditing()->shouldConfirmSvgEditing();
    $customUpload->shouldConfirmSvgEditing();
    $confirmation = $upload->shouldConfirmSvgEditing(...);
    (static fn (): FileUpload => $upload)()->shouldConfirmSvgEditing();
    array_map(static fn (FileUpload $component) => $component->shouldConfirmSvgEditing(), [$upload]);
    FileUpload::configureUsing(static fn (FileUpload $configuredUpload) => $configuredUpload->shouldConfirmSvgEditing());
    $unrelated->isSvgEditingConfirmed();
}

function configureMentions(MentionProvider $provider, ?MentionProvider $optionalProvider, CustomMentionProvider $customProvider, Unrelated $unrelated): void
{
    $provider->getTriggerCharacter();
    $optionalProvider?->getTriggerCharacter();
    MentionProvider::make(char: '#')->getTriggerCharacter();
    (new MentionProvider(char: '@'))->getTriggerCharacter();
    $customProvider->getTriggerCharacter();
    $getTrigger = $provider->getTriggerCharacter(...);
    (static fn (): MentionProvider => $provider)()->getTriggerCharacter();
    array_map(static fn (MentionProvider $configuredProvider) => $configuredProvider->getTriggerCharacter(), [$provider]);
    $unrelated->getChar();
}
