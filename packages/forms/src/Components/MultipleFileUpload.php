<?php

namespace Filament\Forms\Components;

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Str;

class MultipleFileUpload extends Field
{
    use Concerns\HasMinItems;
    use Concerns\HasMaxItems;

    protected string $view = 'forms::components.multiple-file-upload';

    protected $uploadComponent = null;

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
              
            if (!$this->reachedMaxItems($files)) {
                $state[(string)Str::uuid()] = [
                    'file' => null,
                ];
            }

            $component->state($state);
            $component->appendNewUploadField();
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

    public function appendNewUploadField(): void
    {
        $files = $this->getState();

        if ($this->reachedMaxItems(count($files))) {
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

        $this->state($files);
    }

    public function minFiles(int | callable $count): static
    {
        $this->minItems($count);
    }

    public function maxFiles(int | callable $count): static
    {
        $this->maxItems($count);
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
