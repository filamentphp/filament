<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Schemas\Components\Component;

// Pin only the removed declarations for paired execution against the real stack.
spl_autoload_register(static function (string $class): void {
    $declarations = [
        ImportColumn::class => ['packages/actions/src/Imports/ImportColumn.php', 'public function getExample(): mixed { return Arr::first($this->getExamples()); }'],
        Component::class => ['packages/schemas/src/Components/Component.php', 'public function isConcealed(): bool { return false; }'],
    ];

    if ($class === 'Filament\\Schemas\\Components\\Contracts\\CanConcealComponents') {
        eval('namespace Filament\\Schemas\\Components\\Contracts; interface CanConcealComponents {}');

        return;
    }

    if (! isset($declarations[$class])) {
        return;
    }

    [$path, $method] = $declarations[$class];
    $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
    $position = strrpos($source, '}');
    eval(substr($source, 5, $position - 5) . $method . '}');
}, prepend: true);
