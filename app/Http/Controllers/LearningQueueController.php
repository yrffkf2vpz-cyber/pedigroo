<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LearningQueue\LearningQueueService;
use Illuminate\Support\Facades\Log;

class LearningQueueController extends Controller
{
    public function __construct(
        protected LearningQueueService $service
    ) {
        // csak admin vagy superadmin érheti el
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        $domain = $request->get('domain');
        $status = $request->get('status', 'NEW');

        $items   = $this->service->getItems($domain, $status, 50);
        $domains = $this->service->getDomains();

        return view('admin.learning_queue.index', compact(
            'items', 'domains', 'domain', 'status'
        ));
    }

    public function domain(string $domain, Request $request)
    {
        $status = $request->get('status', 'NEW');

        $items = $this->service->getItems($domain, $status, 50);

        return view('admin.learning_queue.domain', compact(
            'items', 'domain', 'status'
        ));
    }

    public function update(int $id, Request $request)
    {
        $data = $request->validate([
            'normalized_input' => 'nullable|string|max:255',
            'status'           => 'required|in:NEW,CONFIRMED,REJECTED',
        ]);

        try {
            $this->service->updateItem($id, $data);

            Log::info('LearningQueue item updated', [
                'user_id' => $request->user()?->id,
                'item_id' => $id,
                'status'  => $data['status'],
            ]);

            return back()->with('status', 'Updated.');
        } catch (\Throwable $e) {
            Log::error('LearningQueue update failed', [
                'user_id' => $request->user()?->id,
                'item_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('status', 'Error during update.');
        }
    }

    public function acceptAISuggestion(int $id, Request $request)
    {
        try {
            $this->service->acceptAISuggestion($id);

            Log::info('AI suggestion accepted', [
                'user_id' => $request->user()?->id,
                'item_id' => $id,
            ]);

            return back()->with('status', 'AI suggestion accepted.');
        } catch (\Throwable $e) {
            Log::error('AI suggestion accept failed', [
                'user_id' => $request->user()?->id,
                'item_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('status', 'Error accepting AI suggestion.');
        }
    }
}

