<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledContracts;

use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface CustomProvider extends MultiFactorAuthenticationProvider
{
    public function getChallengeFormComponents(Authenticatable $user): array;
}

class ParentProvider extends EmailAuthentication
{
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [...parent::getChallengeFormComponents($user), 'parent'];
    }
}

class ChildProvider extends ParentProvider
{
    public function inspect(Authenticatable $user): array
    {
        return [...$this->getChallengeFormComponents($user), ...self::getChallengeFormComponents($user), ...static::getChallengeFormComponents($user)];
    }

    public function inspectClassString(Authenticatable $user): array
    {
        $providerClass = ParentProvider::class;

        return [...$providerClass::getChallengeFormComponents($user), ...EmailAuthentication::getChallengeFormComponents($user)];
    }
}

abstract class CustomUser implements HasEmailAuthentication
{
    public function toggleEmailAuthentication(bool $condition): void {}
}

abstract class CustomDriver implements TranslatableContentDriver
{
    public function getRecordAttributesToArray(Model $record): array
    {
        return $record->attributesToArray();
    }
}

class CustomHalt extends Halt
{
    public function shouldRollbackDatabaseTransaction(): bool
    {
        return parent::shouldRollbackDatabaseTransaction();
    }
}

function inspect(MultiFactorAuthenticationProvider $provider, ?ChildProvider $child, HasEmailAuthentication $user, TranslatableContentDriver $driver, Model $record, Authenticatable $authenticatable, Halt | Cancel $exception): void
{
    $provider->getChallengeFormComponents(user: $authenticatable);
    $child?->getChallengeFormComponents($authenticatable);
    $user->toggleEmailAuthentication(condition: false);
    $driver->getRecordAttributesToArray(record: $record);
    $callback = $driver->getRecordAttributesToArray(...);
    $exception->shouldRollbackDatabaseTransaction();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledContracts;

use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface CustomProvider extends MultiFactorAuthenticationProvider
{
    public function getChallengeSchemaComponents(Authenticatable $user): array;
}

class ParentProvider extends EmailAuthentication
{
    public function getChallengeSchemaComponents(Authenticatable $user): array
    {
        return [...parent::getChallengeSchemaComponents($user), 'parent'];
    }
}

class ChildProvider extends ParentProvider
{
    public function inspect(Authenticatable $user): array
    {
        return [...$this->getChallengeSchemaComponents($user), ...self::getChallengeSchemaComponents($user), ...static::getChallengeSchemaComponents($user)];
    }

    public function inspectClassString(Authenticatable $user): array
    {
        $providerClass = ParentProvider::class;

        return [...$providerClass::getChallengeSchemaComponents($user), ...EmailAuthentication::getChallengeSchemaComponents($user)];
    }
}

abstract class CustomUser implements HasEmailAuthentication
{
    public function saveEmailAuthentication(bool $condition): void {}
}

abstract class CustomDriver implements TranslatableContentDriver
{
    public function recordAttributesToArray(Model $record): array
    {
        return $record->attributesToArray();
    }
}

class CustomHalt extends Halt
{
    public function shouldRollBackDatabaseTransaction(): bool
    {
        return parent::shouldRollBackDatabaseTransaction();
    }
}

function inspect(MultiFactorAuthenticationProvider $provider, ?ChildProvider $child, HasEmailAuthentication $user, TranslatableContentDriver $driver, Model $record, Authenticatable $authenticatable, Halt | Cancel $exception): void
{
    $provider->getChallengeSchemaComponents(user: $authenticatable);
    $child?->getChallengeSchemaComponents($authenticatable);
    $user->saveEmailAuthentication(condition: false);
    $driver->recordAttributesToArray(record: $record);
    $callback = $driver->recordAttributesToArray(...);
    $exception->shouldRollBackDatabaseTransaction();
}
