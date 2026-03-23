<?php

namespace App\Http\Controllers;

use App\Services\Kennel\KennelService;

class KennelController extends Controller
{
    public function show(int $id, KennelService $service)
    {
        return view('admin.kennel.show', [
            'kennel' => $service->get($id),
        ]);
    }
}
