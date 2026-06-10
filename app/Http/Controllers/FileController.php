<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function serveBuktiUndangan(Request $request, $filename)
    {
        $path = 'bukti_undangan/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}