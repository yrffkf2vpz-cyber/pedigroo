<?php

namespace App\Services\Ingest;

use App\Models\PedrooDog;

class SandboxDogSaver
{
    /**
     * A NormalizePipelineService által összeállított $dog tömb mentése
     * a pedroo_dogs sandbox táblába.
     */
    public function save(array $dog): PedrooDog
    {
        // Ha van már ilyen sandbox rekord (pl. reg_no alapján), frissítjük
        $sandbox = PedrooDog::firstOrNew([
            'real_reg_no' => $dog['real_reg_no'] ?? null,
        ]);

        // --- NÉV ---
        $sandbox->real_name      = $dog['real_name']      ?? null;
        $sandbox->real_prefix    = $dog['real_prefix']    ?? null;
        $sandbox->real_lastname  = $dog['real_lastname']  ?? null;
        $sandbox->real_firstname = $dog['real_firstname'] ?? null;

        // --- REGISZTRÁCIÓ ---
        $sandbox->real_reg_no    = $dog['real_reg_no'] ?? null;
        $sandbox->reg_prefix     = $dog['reg_prefix']  ?? null;
        $sandbox->reg_number     = $dog['reg_number']  ?? null;
        $sandbox->reg_year       = $dog['reg_year']    ?? null;
        $sandbox->reg_country    = $dog['reg_country'] ?? null;
        $sandbox->reg_issuer     = $dog['reg_issuer']  ?? null;

        // --- SZÍNEK ---
        $sandbox->real_color     = $dog['real_color']     ?? null; // nyers
        $sandbox->color          = $dog['color']          ?? null; // normalizált
        $sandbox->birth_color    = $dog['birth_color']    ?? null; // születési szín
        $sandbox->official_color = $dog['official_color'] ?? null; // törzskönyvi szín

        // --- ORSZÁG, FAJTA ---
        $sandbox->real_country   = $dog['real_country'] ?? null;
        $sandbox->breed_id       = $dog['breed_id']     ?? null;

        // --- SZÜLŐK ---
        $sandbox->father_id      = $dog['father_id'] ?? null;
        $sandbox->mother_id      = $dog['mother_id'] ?? null;

        // --- EGYÉB ---
        $sandbox->titles         = $dog['titles']     ?? [];
        $sandbox->owners         = $dog['owners']     ?? [];
        $sandbox->ai_used        = $dog['ai_used']    ?? false;
        $sandbox->confidence     = $dog['confidence'] ?? 0;
        $sandbox->debug          = $dog['debug']      ?? [];

        $sandbox->save();

        return $sandbox;
    }
}