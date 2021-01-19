<?php

namespace Filament\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class ImageController extends Controller
{
    public function __invoke($path)
    {
        try {
            SignatureFactory::create(config('app.key'))->validateRequest($path, request()->all());

            $server = ServerFactory::create([
                'base_url' => 'image',
                'cache' => Storage::disk(config('filament.cache_disk'))->getDriver(),
                'cache_path_prefix' => config('filament.cache_path_prefix'),
                'response' => new LaravelResponseFactory(app('request')),
                'source' => Storage::disk(config('filament.storage_disk'))->getDriver(),
            ]);

            return $server->getImageResponse($path, request()->all());
        } catch (SignatureException $e) {
            abort(403, $e->getMessage());
        }
    }
}
