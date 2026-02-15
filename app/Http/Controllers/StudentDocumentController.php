<?php

namespace App\Http\Controllers;

use App\Models\StudentDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class StudentDocumentController extends Controller
{
    use AuthorizesRequests;

    public function download(StudentDocument $studentDocument): SymfonyResponse
    {
        $this->authorize('view', $studentDocument);

        $path = Storage::disk('local')->path($studentDocument->file_path);

        if (! file_exists($path)) {
            abort(404);
        }

        $mime = match (strtolower(pathinfo($studentDocument->file_path, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'application/octet-stream',
        };

        $filename = $studentDocument->original_name ?? basename($studentDocument->file_path);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.basename($filename).'"',
        ]);
    }
}
