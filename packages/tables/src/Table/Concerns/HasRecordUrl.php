<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

trait HasRecordUrl
{
    protected bool | Closure $shouldOpenRecordUrlInNewTab = false;

    protected string | Closure | null $recordUrl = null;

    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraRecordLinkAttributes = [];

    public function openRecordUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenRecordUrlInNewTab = $condition;

        return $this;
    }

    public function recordUrl(string | Closure | null $url, bool | Closure $shouldOpenInNewTab = false): static
    {
        $this->openRecordUrlInNewTab($shouldOpenInNewTab);
        $this->recordUrl = $url;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getRecordUrl(Model | array $record): ?string
    {
        return $this->evaluate(
            $this->recordUrl,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        );
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function shouldOpenRecordUrlInNewTab(Model | array $record): bool
    {
        return (bool) $this->evaluate(
            $this->shouldOpenRecordUrlInNewTab,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        );
    }

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraRecordLinkAttributes(array | Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraRecordLinkAttributes[] = $attributes;
        } else {
            $this->extraRecordLinkAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     * @return array<mixed>
     */
    public function getExtraRecordLinkAttributes(Model | array $record): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraRecordLinkAttributes as $extraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge(
                $this->evaluate(
                    $extraAttributes,
                    namedInjections: [
                        'record' => $record,
                    ],
                    typedInjections: ($record instanceof Model) ? [
                        Model::class => $record,
                        $record::class => $record,
                    ] : [],
                ),
                escape: false,
            );
        }

        return $temporaryAttributeBag->getAttributes();
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getExtraRecordLinkAttributeBag(Model | array $record): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraRecordLinkAttributes($record));
    }
}
