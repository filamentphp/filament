<?php

namespace Filament\Forms\Components;

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Str;

class MultipleFileUpload extends Field
{
    protected string $view = 'forms::components.multiple-file-upload';

    protected $uploadComponent = null;

    protected $maxFiles = null;

    protected $minFiles = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (MultipleFileUpload $component, $state) {
            $files = $state;

            if (! is_array($files)) {
                $files = [];
            }

            $state = [];

            foreach ($files as $file) {
                if ($file) {
                    $state[(string) Str::uuid()] = [
                        'file' => $file,
                    ];
                }
            }

            if (!$this->reachedMaxFiles($files)) {
                $state[(string)Str::uuid()] = [
                    'file' => null,
                ];
            }

            $component->state($state);
        });

        $this->dehydrateStateUsing(function ($state) {
            $files = [];

            foreach ($state as $item) {
                if ($file = $item['file'] ?? null) {
                    $files[] = $file;
                }
            }

            return $files;
        });
    }


    public function maxFiles($value): static
    {
        $this->maxFiles = $value;

        $this
            ->rule('array')
            ->rule(function (): string {
                $value = $this->getMaxFiles();

                return "max:{$value}";
            });

        return $this;
    }

    public function minFiles($value): static
    {
        $this->minFiles = $value;

        $this->rule(function (): callable {
            $minFiles = $this->getMinFiles();
            $label = $this->getValidationAttribute();

            return function($attribute, $value, $fail) use ($minFiles, $label) {
                if (count(array_filter($value, fn($item) => !is_null($item['file']))) < $minFiles) {
                    $fail(trans('validation.min.array', [
                        'attribute' => $label,
                        'min' => $minFiles
                    ]));
                }
            } ;
        });

        return $this;
    }

    public function getMaxFiles()
    {
        return $this->evaluate($this->maxFiles);
    }

    public function getMinFiles()
    {
        return $this->evaluate($this->minFiles);
    }

    protected function reachedMaxFiles($files): bool
    {
        return $this->getMaxFiles() !== null && count($files) >= $this->getMaxFiles();
    }

    public function appendNewUploadField(): void
    {
        $files = $this->getState();

        if ($this->reachedMaxFiles($files)) {
            return;
        }

        $files[(string) Str::uuid()] = [
            'file' => null,
        ];

        $this->state($files);
    }

    public function removeUploadedFile(string $uuid): void
    {
        $files = $this->getState();

        unset($files[$uuid]);

        if (!in_array(['file' => null], $files)) {
            $files[(string)Str::uuid()] = [
                'file' => null,
            ];
        }

        $this->state($files);
    }

    public function uploadComponent(Component | callable $component): static
    {
        $this->uploadComponent = $component;

        return $this;
    }

    public function getChildComponentContainers(): array
    {
        return collect($this->getState())
            ->map(function ($item, $index): ComponentContainer {
                return $this
                    ->getChildComponentContainer()
                    ->getClone()
                    ->statePath($index);
            })->toArray();
    }

    public function getChildComponents(): array
    {
        return [
            $this->getUploadComponent(),
        ];
    }

    public function getUploadComponent(): Component
    {
        return $this->evaluate($this->uploadComponent) ?? $this->getDefaultUploadComponent();
    }

    protected function getDefaultUploadComponent(): Component
    {
        return FileUpload::make('file');
    }
}
