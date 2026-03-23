<?php

namespace App\Services\Normalizers\Jobs;

use App\Models\PedrooDog;
use App\Services\Normalizers\NormalizePipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NormalizeDogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $dogId;
    public bool $debug;

    /**
     * @param int  $dogId  A pedroo_dogs rekord ID-je
     * @param bool $debug  Debug mód bekapcsolása
     */
    public function __construct(int $dogId, bool $debug = false)
    {
        $this->dogId = $dogId;
        $this->debug = $debug;

        // opcionális: külön queue
        $this->onQueue('normalize');
    }

    /**
     * A job futtatja a teljes NormalizePipelineService-t.
     */
    public function handle(NormalizePipelineService $pipeline)
    {
        $dog = PedrooDog::find($this->dogId);

        if (!$dog) {
            return;
        }

        // ---------------------------------------------------------
        // Állapot: normalizing
        // ---------------------------------------------------------
        $dog->update([
            'status' => 'normalizing',
        ]);

        // ---------------------------------------------------------
        // Pipeline futtatása
        // ---------------------------------------------------------
        $result = $pipeline->process($dog->toArray(), $this->debug);

        // ---------------------------------------------------------
        // Pipeline eredmény mentése
        // ---------------------------------------------------------
        $dog->update([
            'pipeline_result' => $result,
        ]);
    }

    /**
     * Hibakezelés – retry + log
     */
    public function failed(\Throwable $e)
    {
        \Log::error("NormalizeDogJob failed for dog {$this->dogId}: {$e->getMessage()}");

        // retry 10 másodperc múlva
        $this->release(10);
    }
}