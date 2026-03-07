<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use League\Flysystem\UnableToRetrieveMetadata;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\NormalizeLivewireUploadFilenames::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (UnableToRetrieveMetadata $e, $request) {
            if (! $request->is('livewire/update') && ! $request->is('livewire/upload-file')) {
                return null;
            }
            if (str_contains($e->getMessage(), 'livewire-tmp')) {
                throw ValidationException::withMessages([
                    'data.image' => [
                        'The image file could not be read. It may be corrupt or in an unsupported format. Please try another file.',
                    ],
                ]);
            }

            return null;
        });
    })->create();
