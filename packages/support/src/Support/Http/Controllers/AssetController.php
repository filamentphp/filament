<?php

namespace Filament\Support\Http\Controllers;

use Filament\Filament;
use Filament\Support;
use Illuminate\Support\Str;

class AssetController
{
    public function __invoke($filename)
    {
        $scripts = Support::getScripts();

        if (array_key_exists($filename, $scripts)) {
            return $this->pretendResponseIsFile($scripts[$filename], 'application/javascript; charset=utf-8');
        }

        $styles = Support::getStyles();

        if (array_key_exists($filename, $styles)) {
            return $this->pretendResponseIsFile($styles[$filename], 'text/css; charset=utf-8');
        }
    }

    protected function pretendResponseIsFile($path, $contentType)
    {
        if (! file_exists($path)) {
            abort(404);
        }

        $cacheControl = 'public, max-age=31536000';
        $expires = strtotime('+1 year');

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
            'Last-Modified' => $this->getHttpDate(filemtime($path)),
        ]);
    }

    protected function getHttpDate($timestamp)
    {
        return sprintf('%s GMT', gmdate('D, d M Y H:i:s', $timestamp));
    }
}
