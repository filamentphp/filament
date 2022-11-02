<?php

namespace Filament\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

class AssetController
{
    public function __invoke(string $file)
    {
        switch ($file) {
            case 'app.css':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/app.css', 'text/css; charset=utf-8');
            case 'app.css.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/app.css.map', 'text/css; charset=utf-8');
            case 'app.js':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/app.js', 'application/javascript; charset=utf-8');
            case 'app.js.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/app.js.map', 'application/json; charset=utf-8');
            case 'echo.js':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/echo.js', 'application/javascript; charset=utf-8');
            case 'echo.js.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/echo.js.map', 'application/json; charset=utf-8');
        }

        if (Str::endsWith($file, '.js')) {
            $name = Str::beforeLast($file, '.js');

            if (array_key_exists($name, Filament::getScripts())) {
                return $this->pretendResponseIsFile(Filament::getScripts()[$name], 'application/javascript; charset=utf-8');
            } elseif (array_key_exists($name, Filament::getBeforeCoreScripts())) {
                return $this->pretendResponseIsFile(Filament::getBeforeCoreScripts()[$name], 'application/javascript; charset=utf-8');
            } else {
                abort(404);
            }
        }

        if (Str::endsWith($file, '.css')) {
            $name = Str::beforeLast($file, '.css');

            abort_unless(
                array_key_exists($name, Filament::getStyles()),
                404,
            );

            return $this->pretendResponseIsFile(Filament::getStyles()[$name], 'text/css; charset=utf-8');
        }

        abort(404);
    }

    protected function getHttpDate(int $timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }

    protected function pretendResponseIsFile(string $path, string $contentType)
    {
        abort_unless(
            file_exists($path) || file_exists($path = base_path($path)),
            404,
        );

        $cacheControl = 'public, max-age=31536000';
        $expires = strtotime('+1 year');

        $lastModified = filemtime($path);

        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '') === $lastModified) {
            return response()->noContent(304, [
                'Expires' => $this->getHttpDate($expires),
                'Cache-Control' => $cacheControl,
            ]);
        }

        return response()->file($path, [
            'Content-Type' => $contentType,
            'Expires' => $this->getHttpDate($expires),
            'Cache-Control' => $cacheControl,
            'Last-Modified' => $this->getHttpDate($lastModified),
        ]);
    }
}
