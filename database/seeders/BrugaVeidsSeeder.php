<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrugaVeidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BrugaVeids::create([
            'name' => 'Betona bruģis',
            'price_per_m2' => 25.00,
            'description' => 'Izturīgs un pieejams bruģa veids, plašs formu un krāsu klāsts.',
        ]);

        \App\Models\BrugaVeids::create([
            'name' => 'Granīta bruģakmens',
            'price_per_m2' => 45.00,
            'description' => 'Dabīgais akmens ar augstu izturību un klasisku izskatu.',
        ]);
    }
}
