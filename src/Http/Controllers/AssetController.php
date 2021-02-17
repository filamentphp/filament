<?php

namespace Filament\Http\Controllers;

class AssetController
{
    public function __invoke($path)
    {
        switch ($path) {
            case 'css/filament.css':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/css/filament.css', 'text/css; charset=utf-8');
            case 'css/filament.css.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/css/filament.css.map', 'text/css; charset=utf-8');
            case 'js/filament.js':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/js/filament.js', 'application/javascript; charset=utf-8');
            case 'js/filament.js.map':
                return $this->pretendResponseIsFile(__DIR__ . '/../../../dist/js/filament.js.map', 'application/json; charset=utf-8');
            default:
                abort(404);
                break;
        }
    }

    protected function pretendResponseIsFile($path, $contentType)
    {
        abort_if(! file_exists($path), 404);

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

    protected function getHttpDate($timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }
}
