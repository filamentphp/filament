<?php

namespace Filament\Tests\Upgrade\Fixtures\ReconciledBoundaries;

use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Illuminate\Contracts\Auth\Authenticatable;

class Unrelated
{
    public function getChallengeFormComponents(): array { return []; }
    public function toggleEmailAuthentication(bool $condition): void {}
    public function getRecordAttributesToArray(): array { return []; }
    public function shouldRollbackDatabaseTransaction(): bool { return false; }
}

class ConflictingProvider extends EmailAuthentication
{
    public function getChallengeFormComponents(Authenticatable $user): array { return ['old']; }
    public function getChallengeSchemaComponents(Authenticatable $user): array { return ['new']; }
}

trait SharedWithUnrelated
{
    public function toggleEmailAuthentication(bool $condition): void {}
}

abstract class ContractUser implements HasEmailAuthentication
{
    use SharedWithUnrelated;
}

class UnrelatedUser
{
    use SharedWithUnrelated;
}

function inspect(Unrelated $object, ConflictingProvider $provider, Authenticatable $user, ContractUser $contractUser, string $method): void
{
    $object->getChallengeFormComponents();
    $object->toggleEmailAuthentication(true);
    $object->getRecordAttributesToArray();
    $object->shouldRollbackDatabaseTransaction();
    $provider->getChallengeFormComponents($user);
    $contractUser->toggleEmailAuthentication(true);
    $provider->{$method}($user);
}
