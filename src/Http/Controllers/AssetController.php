<?php

namespace Filament\Http\Controllers;

use Filament\Filament;

class AssetController
{
    public function css()
    {
        return $this->pretendResponseIsFile('css/filament.css', 'text/css; charset=utf-8');
    }

    public function cssMap()
    {
        return $this->pretendResponseIsFile('css/filament.css.map', 'application/javascript; charset=utf-8');
    }

    public function js()
    {
        return $this->pretendResponseIsFile('js/filament.js', 'application/javascript; charset=utf-8');
    }

    public function jsMap()
    {
        return $this->pretendResponseIsFile('js/filament.js.map', 'application/javascript; charset=utf-8');
    }

    protected function pretendResponseIsFile($file, $contentType)
    {
        $path = Filament::distPath($file);

        if (! file_exists($path)) {
            abort(404);
        }

        $expires = strtotime('+1 year');
        $lastModified = filemtime($path);
        $cacheControl = 'public, max-age=31536000';

        if ($this->matchesCache($lastModified)) {
            return response()->noContent(304, [
                'Expires' => $this->httpDate($expires),
                'Cache-Control' => $cacheControl,
            ]);
        }

        return response()->file($path, [
            'Content-Type' => $contentType,
            'Expires' => $this->httpDate($expires),
            'Cache-Control' => $cacheControl,
            'Last-Modified' => $this->httpDate($lastModified),
        ]);
    }

    protected function matchesCache($lastModified)
    {
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';

        return @strtotime($ifModifiedSince) === $lastModified;
    }

    protected function httpDate($timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }
}
