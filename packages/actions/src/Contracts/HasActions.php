<?php

namespace Filament\Actions\Contracts;

use Closure;
use Filament\Actions\Action;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

interface HasActions
{
    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name, bool $isMounting = true): ?Action;

    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $context
     */
    public function mountAction(string $name, array $arguments = [], array $context = []): mixed;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mergeMountedActionArguments(array $arguments): void;

    public function getDefaultActionRecord(Action $action): ?Model;

    public function getDefaultActionRecordTitle(Action $action): ?string;

    /**
     * @return ?class-string<Model>
     */
    public function getDefaultActionModel(Action $action): ?string;

    public function getDefaultActionModelLabel(Action $action): ?string;

    public function getDefaultActionUrl(Action $action): ?string;

    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string;

    public function getDefaultActionFailureRedirectUrl(Action $action): ?string;

    public function getDefaultActionRelationship(Action $action): ?Relation;

    public function getDefaultActionSchemaResolver(Action $action): ?Closure;

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response;

    public function getDefaultActionIndividualRecordAuthorizationResponseResolver(Action $action): ?Closure;

    public function getMountedActionSchemaName(): ?string;
}
