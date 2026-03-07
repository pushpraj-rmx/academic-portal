<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class NormalizeLivewireUploadFilenames
{
    /**
     * Normalize uploaded file names (e.g. replace spaces) before Livewire stores them.
     * Prevents path/serialization issues that can cause UnableToRetrieveMetadata.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('livewire/upload-file')) {
            return $next($request);
        }

        $files = $request->file('files');

        if (! is_array($files)) {
            return $next($request);
        }

        $normalized = [];
        foreach ($files as $key => $file) {
            if (! $file instanceof UploadedFile) {
                $normalized[$key] = $file;

                continue;
            }
            $normalized[$key] = new UploadedFile(
                $file->getRealPath(),
                normalize_upload_filename($file->getClientOriginalName()),
                $file->getClientMimeType(),
                $file->getError(),
                true
            );
        }

        $request->files->set('files', $normalized);

        return $next($request);
    }
}
