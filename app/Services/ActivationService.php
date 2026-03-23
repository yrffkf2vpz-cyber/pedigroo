<?php

namespace App\Services;

class ActivationService
{
    public function activate($id)
    {
        // Itt késobb jöhet a valódi logika:
        // - ingestelt rekord aktiválása
        // - státusz frissítés
        // - pipeline trigger
        // - audit log

        return [
            'id' => $id,
            'activated' => true,
        ];
    }
}