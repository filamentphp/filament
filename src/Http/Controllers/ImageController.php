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
            // Validate HTTP signature
            SignatureFactory::create(config('app.key'))->validateRequest($path, request()->all());

            $server = ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => Storage::disk(config('filament.storage_disk'))->getDriver(),
                'cache' => Storage::disk(config('filament.cache_disk'))->getDriver(),
                'cache_path_prefix' => config('filament.cache_path_prefix'),
                'base_url' => 'image',
            ]);

            return $server->getImageResponse($path, request()->all());

        } catch (SignatureException $e) {
            // Handle error
            abort(403, $e->getMessage());
        }
    }
}
