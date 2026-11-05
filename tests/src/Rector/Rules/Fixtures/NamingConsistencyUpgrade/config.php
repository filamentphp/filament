<?php

use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Panel;
use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return RectorConfig::configure()
    ->withImportNames(importShortClasses: true)
    ->withConfiguredRule(RenameClassRector::class, [
        'Filament\\Support\\Concerns\\CanBeLazy' => 'Filament\\Support\\Concerns\\CanBeLazyLoaded',
    ])
    ->withConfiguredRule(RenameMethodRector::class, [
        new MethodCallRename(Panel::class, 'getResetPasswordRoutePrefix', 'getPasswordResetRoutePrefix'),
        new MethodCallRename(EmailAuthentication::class, 'generateCodesUsing', 'generateCodeUsing'),
    ])
    ->withCache(cacheClass: MemoryCacheStorage::class);
