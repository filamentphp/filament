<?php

namespace Filament\Commands\FileGenerators\Resources\Concerns;

use Filament\Forms\Commands\FileGenerators\Concerns\CanGenerateModelForms;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Nette\PhpGenerator\Literal;

trait CanGenerateResourceForms
{
    use CanGenerateModelForms;

    /**
     * @param  ?class-string<Model>  $model
     * @param  array<string>  $exceptColumns
     */
    public function outputFormComponents(?string $model = null, array $exceptColumns = []): string
    {
        $components = $this->getFormComponents($model, $exceptColumns);

        if (empty($components)) {
            $recordTitleAttribute = $this->getRecordTitleAttribute();

            if (blank($recordTitleAttribute)) {
                return '//';
            }

            $this->importUnlessPartial(TextInput::class);

            return new Literal(<<<PHP
                {$this->simplifyFqn(TextInput::class)}::make(?)
                            ->required()
                            ->maxLength(255),
                PHP, [$recordTitleAttribute]);
        }

        return implode(PHP_EOL . '        ', $components);
    }
}
