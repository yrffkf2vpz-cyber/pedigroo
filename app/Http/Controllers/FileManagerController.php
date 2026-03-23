<?php

namespace App\Http\Controllers;

use App\Services\FileManager\FileManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileManagerController extends Controller
{
    public function __construct(
        protected FileManagerService $service
    ) {
        // csak superadmin használhatja
        $this->middleware(['auth', 'can:superadmin']);
    }

    protected function validatePath(string $path): string
    {
        // alap validáció
        if ($path === '') {
            abort(422, 'Path is required.');
        }

        // normalizálás
        $fullPath = realpath($path) ?: $path;

        // csak a Pedroo sandboxon belül engedjük
        $root = realpath(storage_path('pedroo'));

        if (!$root || !str_starts_with($fullPath, $root)) {
            Log::warning('FileManager path outside sandbox', [
                'path' => $path,
                'full' => $fullPath,
            ]);
            abort(403, 'Access denied for this path.');
        }

        return $fullPath;
    }

    public function createFolder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $this->service->createFolder($path);

            Log::info('Folder created', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Folder created.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Folder create failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Folder creation failed.',
            ], 500);
        }
    }

    public function createFile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $this->service->createFile($path);

            Log::info('File created', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'File created.',
            ]);
        } catch (\Throwable $e) {
            Log::error('File create failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'File creation failed.',
            ], 500);
        }
    }

    public function writeFile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path'    => 'required|string',
            'content' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $this->service->writeFile($path, $data['content']);

            Log::info('File written', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'File written.',
            ]);
        } catch (\Throwable $e) {
            Log::error('File write failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'File write failed.',
            ], 500);
        }
    }

    public function readFile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $content = $this->service->readFile($path);

            Log::info('File read', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status'  => 'ok',
                'data'    => [
                    'path'    => $path,
                    'content' => $content,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('File read failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'File read failed.',
            ], 500);
        }
    }

    public function deleteFile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $this->service->deleteFile($path);

            Log::info('File deleted', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'File deleted.',
            ]);
        } catch (\Throwable $e) {
            Log::error('File delete failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'File delete failed.',
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->validatePath($data['path']);

        try {
            $items = $this->service->list($path);

            Log::info('Directory listed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
            ]);

            return response()->json([
                'status' => 'ok',
                'data'   => [
                    'path'  => $path,
                    'items' => $items,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Directory list failed', [
                'user_id' => $request->user()?->id,
                'path'    => $path,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Directory list failed.',
            ], 500);
        }
    }
}
