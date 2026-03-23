<?php

namespace App\Http\Controllers;

use App\Services\Pipeline\PipelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PipelineController extends Controller
{
    public function __construct(
        protected PipelineService $pipeline
    ) {
        // csak bejelentkezett, superadmin jogosultságú felhasználó érheti el
        $this->middleware(['auth', 'can:superadmin']);
    }

    public function run(Request $request): JsonResponse
    {
        // 1) FELADATOK FELVÉTELE SORRENDBEN

        // BLOKK 0: old_laravel + DB
        $this->pipeline->addTask('file_migrate');   // storage/old_laravel beolvasás
        $this->pipeline->addTask('db_sync');        // adatbázis szinkron / előkészítés

        // BLOKK 1: Normalize
        $this->pipeline->addTask('normalize:diagnosis');
        $this->pipeline->addTask('normalize:country-codes-full');
        $this->pipeline->addTask('normalize:endpoint');
        $this->pipeline->addTask('normalize:tests');

        // BLOKK 2: Ingest
        $this->pipeline->addTask('ingest:excel-ui');
        $this->pipeline->addTask('ingest:csv-ui');
        $this->pipeline->addTask('ingest:json-ui');
        $this->pipeline->addTask('ingest:api-endpoints');
        $this->pipeline->addTask('ingest:web-scrapers');
        $this->pipeline->addTask('ingest:log-system');
        $this->pipeline->addTask('ingest:error-handling');
        $this->pipeline->addTask('ingest:tests');

        // BLOKK 3: Dogs
        $this->pipeline->addTask('dogs:excel-import');
        $this->pipeline->addTask('dogs:regno-advanced');
        $this->pipeline->addTask('dogs:country-codes-full');
        $this->pipeline->addTask('dogs:duplicate-detection');
        $this->pipeline->addTask('dogs:merge-ui');
        $this->pipeline->addTask('dogs:audit-module');

        // BLOKK 4: Audit
        $this->pipeline->addTask('audit:ui');
        $this->pipeline->addTask('audit:error-detection');
        $this->pipeline->addTask('audit:fix-suggestions');
        $this->pipeline->addTask('audit:fix-pipeline');
        $this->pipeline->addTask('audit:export');
        $this->pipeline->addTask('audit:tests');

        // BLOKK 5: Events
        $this->pipeline->addTask('events:meta-normalize');
        $this->pipeline->addTask('events:ingest');
        $this->pipeline->addTask('events:calendar-integration');
        $this->pipeline->addTask('events:eventbrite');
        $this->pipeline->addTask('events:google-events');
        $this->pipeline->addTask('events:list-ui');
        $this->pipeline->addTask('events:details-ui');
        $this->pipeline->addTask('events:link-results');

        // BLOKK 6: Breeding
        $this->pipeline->addTask('breeding:coi-engine');
        $this->pipeline->addTask('breeding:color-genetics');
        $this->pipeline->addTask('breeding:health-risk');
        $this->pipeline->addTask('breeding:plan-generator');
        $this->pipeline->addTask('breeding:plan-ui');
        $this->pipeline->addTask('breeding:genetics-ui');

        // BLOKK 7: i18n
        $this->pipeline->addTask('i18n:keylist');
        $this->pipeline->addTask('i18n:hu');
        $this->pipeline->addTask('i18n:en');
        $this->pipeline->addTask('i18n:de');
        $this->pipeline->addTask('i18n:ro');
        $this->pipeline->addTask('i18n:es');
        $this->pipeline->addTask('i18n:pt');
        $this->pipeline->addTask('i18n:switcher-ui');
        $this->pipeline->addTask('i18n:auto-detect');

        // BLOKK 8: Final pipeline
        $this->pipeline->addTask('final:promotion-rules');
        $this->pipeline->addTask('final:promotion-errors');
        $this->pipeline->addTask('final:promotion-log');
        $this->pipeline->addTask('final:promotion-rollback');
        $this->pipeline->addTask('final:tests');

        // BLOKK 9: Kennel activity
        $this->pipeline->addTask('kennel:activity-check');
        $this->pipeline->addTask('kennel:status-update');
        $this->pipeline->addTask('kennel:audit-report');
        $this->pipeline->addTask('kennel:ui');

        // BLOKK 10: Clubs
        $this->pipeline->addTask('clubs:db');
        $this->pipeline->addTask('clubs:ui');
        $this->pipeline->addTask('clubs:location');
        $this->pipeline->addTask('clubs:activities');

        // BLOKK 11: Pedigree PDF
        $this->pipeline->addTask('pdf:layout');
        $this->pipeline->addTask('pdf:3gen');
        $this->pipeline->addTask('pdf:4gen');
        $this->pipeline->addTask('pdf:color-modes');
        $this->pipeline->addTask('pdf:export-button');
        $this->pipeline->addTask('pdf:engine');

        // BLOKK 12: Doctors
        $this->pipeline->addTask('doctor:db');
        $this->pipeline->addTask('doctor:clinic-db');
        $this->pipeline->addTask('doctor:specialists');
        $this->pipeline->addTask('doctor:location');
        $this->pipeline->addTask('doctor:distance');
        $this->pipeline->addTask('doctor:filters');
        $this->pipeline->addTask('doctor:profiles');
        $this->pipeline->addTask('doctor:call-route');

        // 13. Meghívásos + token modul: MOST KIMARAD

        try {
            // 2) MINDENT LEFUTTATUNK SORBAN
            while ($this->pipeline->runNextTask()) {
                // addig fut, amíg van pending task
            }

            // minimális audit log – később mehet külön táblába
            Log::info('Master pipeline successfully run', [
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Pedroo pipeline lefutott (old_laravel + DB + összes modul).',
            ]);
        } catch (\Throwable $e) {
            Log::error('Master pipeline failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'A Pedroo pipeline futása közben hiba történt.',
            ], 500);
        }
    }
}
