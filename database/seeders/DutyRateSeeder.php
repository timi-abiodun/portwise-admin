<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DutyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['8517.13', 'ad_valorem', 20, null, 'Backend doc worked example (illustrative)'],
            ['6109.10', 'ad_valorem', 10, null, 'Backend doc fabric example (illustrative)'],
            ['8413.70', 'per_kg', null, 150, 'TEST ONLY - delete before demo'],
        ];

        foreach ($rows as [$code, $type, $pct, $perKg, $source]) {
            $hsId = \App\Models\HsCode::where('code', $code)->value('id');

            \App\Models\DutyRate::updateOrCreate(
                ['hs_code_id' => $hsId, 'effective_to' => null],
                ['rate_type' => $type, 'duty_pct' => $pct, 'rate_per_kg' => $perKg,
                'effective_from' => now()->toDateString(), 'source_ref' => $source]
            );
        }
    }
}
