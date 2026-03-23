<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ha még nincs ilyen szerep, beszúrjuk
        $exists = DB::table('roles')
            ->where('name', 'community_user')
            ->exists();

        if (! $exists) {
            DB::table('roles')->insert([
                'name' => 'community_user',
                'created_at' => now(),
                'updated_at' => now(),   // <-- EZ ITT A JAVÍTOTT SOR
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'community_user')
            ->delete();
    }
};
