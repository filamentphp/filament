<?php

namespace Filament;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class Filament {

    /**
     * `base` path
     *
     * @param string $path
     * @return string
     */
    public function basePath($path = '')
    {
        return __DIR__.'/../'.ltrim($path, '/');
    }

    /**
     * `route` path
     *
     * @param string $path
     * @return string
     */
    public function routePath($path = '')
    {
        return $this->basePath('routes/'.ltrim($path, '/'));
    }

    /**
     * `resource` path
     *
     * @param string $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->basePath('resources/'.ltrim($path, '/'));
    }

    /**
     * `dist` path
     *
     * @param string $path
     * @return string
     */
    public function distPath($path = '')
    {
        return $this->basePath('dist/'.ltrim($path, '/'));
    }

    /**
     * Check if user has requested an filament path.
     *
     * @return boolean
     */
    public function handling()
    {
        return preg_match('#^'.config('filament.path').'($|/)'.'#i', Request::path());
    }

    /**
     * Generate markup needed for CP assets.
     * 
     * @return Illuminate\Support\HtmlString
     */
    public function assets()
    {
        $manifest = json_decode(file_get_contents($this->distPath('mix-manifest.json')), true);
        parse_str(parse_url($manifest['/css/app.css'], PHP_URL_QUERY), $cssInfo);
        parse_str(parse_url($manifest['/js/app.js'], PHP_URL_QUERY), $jsInfo);

        return new HtmlString('
            <!-- Filament Styles -->
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
            <link rel="stylesheet" href="'.route('filament.assets.css', $cssInfo).'" />
                 
            <!-- Filament Scripts -->
            <script src="'.route('filament.assets.js', $jsInfo).'" defer></script>
        ');
    }

    /**
     * SVG's
     *
     * @param string $fileName
     * @param string $class
     * @return mixed
     */
    public function svg($fileName, $class = null)
    {
        $path = (string) $fileName;
        $file = $this->resourcePath("svg/$path.svg");
        if (file_exists($file)) {
            $contents = file_get_contents($file);
            $contents = preg_replace('#\s(id|class)="[^"]+"#', '', $contents); // remove ID's and classes
            $result = (is_null($class)) ? $contents : str_replace('viewBox', 'class="'.$class.'" viewBox', $contents);
            return new HtmlString($result);
        }
    }

    /**
     * Format bytes to kb, mb, gb, tb
     *
     * @param  integer $size
     * @param  integer $precision
     * @return integer
     */
    public function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array('bytes', 'KB', 'MB', 'GB', 'TB');
            return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}
