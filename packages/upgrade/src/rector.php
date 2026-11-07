<?php

use Composer\InstalledVersions;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Panel;
use Filament\Upgrade\Rector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        dirname((new ReflectionClass(InstalledVersions::class))->getFileName(), 2),
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Filament\\Actions\\Concerns\\CanBeLabeledFrom' => 'Filament\\Actions\\Concerns\\CanBeLabeledFromBreakpoint',
        'Filament\\Support\\Concerns\\CanBeLazy' => 'Filament\\Support\\Concerns\\CanBeLazyLoaded',
        'Filament\\Tables\\Filters\\Concerns\\HasSchema' => 'Filament\\Tables\\Filters\\Concerns\\HasForm',
        'Filament\\Widgets\\ChartWidget\\Concerns\\HasFiltersSchema' => 'Filament\\Widgets\\ChartWidget\\Concerns\\HasFiltersForm',
    ]);

    $rectorConfig->rules([
        Rector\SimpleMethodChangesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename(Panel::class, 'getResetPasswordRoutePrefix', 'getPasswordResetRoutePrefix'),
        new MethodCallRename(EmailAuthentication::class, 'generateCodesUsing', 'generateCodeUsing'),
        new MethodCallRename(FileUpload::class, 'isSvgEditingConfirmed', 'shouldConfirmSvgEditing'),
        new MethodCallRename(MentionProvider::class, 'getChar', 'getTriggerCharacter'),
    ]);
};
