<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingest\PdfImport;
use App\Jobs\ProcessPdfImportJob;
use Illuminate\Support\Facades\Storage;

class PdfImportController extends Controller
{
    /**
     * PDF feltöltő űrlap megjelenítése (opcionális).
     */
    public function index()
    {
        $imports = PdfImport::orderByDesc('id')->paginate(20);

        return view('admin.pdf-import.index', [
            'imports' => $imports,
        ]);
    }

    /**
     * PDF import indítása.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:health,event,pedigree',
            'source' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        // PDF mentése storage-ba
        $path = $request->file('file')->store('pdf_imports');

        // Import rekord létrehozása
        $import = PdfImport::create([
            'user_id'   => auth()->id(),
            'type'      => $request->input('type'),
            'source'    => $request->input('source'),
            'file_path' => $path,
            'status'    => 'pending',
            'stats'     => null,
            'log'       => [],
        ]);

        // Queue indítása
        ProcessPdfImportJob::dispatch($import->id);

        return redirect()
            ->back()
            ->with('status', "PDF import elindítva (#{$import->id})");
    }

    /**
     * Egy import részletes nézete (log + stats).
     */
    public function show(int $id)
    {
        $import = PdfImport::findOrFail($id);

        return view('admin.pdf-import.show', [
            'import' => $import,
        ]);
    }

    /**
     * PDF fájl letöltése (admin ellenőrzéshez).
     */
    public function download(int $id)
    {
        $import = PdfImport::findOrFail($id);

        if (!Storage::exists($import->file_path)) {
            abort(404, 'A PDF fájl nem található.');
        }

        return Storage::download($import->file_path);
    }
}
