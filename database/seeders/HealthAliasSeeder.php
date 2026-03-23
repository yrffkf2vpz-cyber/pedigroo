<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthAlias;

class HealthAliasSeeder extends Seeder
{
    public function run()
    {
        $data = [

            // ---------------------------------------------------------
            // ???????????????????????? FCI – HD A–E (HU, RO, DE, PL, SK, CZ, etc.)
            // ---------------------------------------------------------
            ['HD', 'A', 'A', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['HD', 'B', 'B', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['HD', 'C', 'C', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['HD', 'D', 'D', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['HD', 'E', 'E', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],

            // Magyar saját jelölések
            ['HD', 'H*', 'A', ['HU']],
            ['HD', 'Normális', 'A', ['HU']],

            // ---------------------------------------------------------
            // ???????????? Finnország (FCI + saját jelölések)
            // ---------------------------------------------------------
            ['HD', 'Operoitu', 'E', ['FI']],
            ['HD', 'BVA yli 12', 'E', ['FI']],

            // ---------------------------------------------------------
            // ???????????? UK – BVA rendszer
            // ---------------------------------------------------------
            ['HD', 'BVA 0', 'A', ['UK','IE']],
            ['HD', 'BVA 1', 'A', ['UK','IE']],
            ['HD', 'BVA 2', 'A', ['UK','IE']],
            ['HD', 'BVA 3', 'A', ['UK','IE']],
            ['HD', 'BVA 0-3', 'A', ['UK','IE']],

            ['HD', 'BVA 4', 'B', ['UK','IE']],
            ['HD', 'BVA 5', 'B', ['UK','IE']],
            ['HD', 'BVA 6', 'B', ['UK','IE']],
            ['HD', 'BVA 4-6', 'B', ['UK','IE']],

            ['HD', 'BVA 7', 'C', ['UK','IE']],
            ['HD', 'BVA 8', 'C', ['UK','IE']],
            ['HD', 'BVA 9', 'C', ['UK','IE']],
            ['HD', 'BVA 10', 'C', ['UK','IE']],
            ['HD', 'BVA 11', 'C', ['UK','IE']],
            ['HD', 'BVA 12', 'C', ['UK','IE']],
            ['HD', 'BVA 7-12', 'C', ['UK','IE']],

            // ---------------------------------------------------------
            // ???????????? USA – OFA rendszer
            // ---------------------------------------------------------
            ['HD', 'OFA Exc', 'A', ['US','CA']],
            ['HD', 'OFA Excellent', 'A', ['US','CA']],

            ['HD', 'OFA Good', 'B', ['US','CA']],

            ['HD', 'OFA Fair', 'C', ['US','CA']],

            ['HD', 'OFA Border', 'D', ['US','CA']],
            ['HD', 'OFA Borderline', 'D', ['US','CA']],

            ['HD', 'OFA Mild', 'D', ['US','CA']],
            ['HD', 'OFA Moderate', 'E', ['US','CA']],
            ['HD', 'OFA Severe', 'E', ['US','CA']],

            // ---------------------------------------------------------
            // ED – könyökdiszplázia (FCI + OFA)
            // ---------------------------------------------------------
            ['ED', '0/0', '0', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['ED', '1/1', '1', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['ED', '2/2', '2', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['ED', '3/3', '3', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],

            ['ED', 'Normal', '0', ['US','CA']],
            ['ED', 'Grade 1', '1', ['US','CA']],
            ['ED', 'Grade 2', '2', ['US','CA']],
            ['ED', 'Grade 3', '3', ['US','CA']],

            // ---------------------------------------------------------
            // DM / PRA / HUU – genetikai tesztek (világszerte egységes)
            // ---------------------------------------------------------
            ['DM', 'N/N', 'CLEAR', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['DM', 'N/DM', 'CARRIER', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['DM', 'DM/DM', 'AFFECTED', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['DM', 'Clear', 'CLEAR', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['DM', 'Carrier', 'CARRIER', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['DM', 'Affected', 'AFFECTED', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],

            ['PRA', 'Clear', 'CLEAR', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['PRA', 'Carrier', 'CARRIER', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['PRA', 'Affected', 'AFFECTED', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],

            ['HUU', 'Clear', 'CLEAR', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['HUU', 'Carrier', 'CARRIER', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],
            ['HUU', 'Affected', 'AFFECTED', ['HU','RO','DE','US','UK','FI','SE','NO','DK','CA']],

            // ---------------------------------------------------------
            // PL – Patella (0–4)
            // ---------------------------------------------------------
            ['PL', '0/0', '0', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['PL', '1/1', '1', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['PL', '2/2', '2', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['PL', '3/3', '3', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
            ['PL', '4/4', '4', ['HU','RO','DE','PL','SK','CZ','AT','CH','ES','IT','FR','BE','NL','SE','NO','DK','FI']],
        ];

        foreach ($data as $row) {
            HealthAlias::create([
                'test_type' => $row[0],
                'alias'     => $row[1],
                'canonical' => $row[2],
                'countries' => $row[3],
            ]);
        }
    }
}