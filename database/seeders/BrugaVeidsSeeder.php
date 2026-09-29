<?php

namespace Database\Seeders;

use App\Models\BrugaVeids;
use Illuminate\Database\Seeder;

class BrugaVeidsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Betona bruģis',
                'price_per_m2' => 25.00,
                'description' => 'Izturīgs un daudzpusīgs bruģis ar plašu formu un krāsu izvēli.',
            ],
            [
                'name' => 'Klinkera bruģis',
                'price_per_m2' => 35.00,
                'description' => 'Dekoratīvs un izturīgs bruģis ar izteiksmīgu, klasisku izskatu.',
            ],
            [
                'name' => 'Granīta bruģakmens',
                'price_per_m2' => 45.00,
                'description' => 'Dabīgā akmens bruģis ar augstu izturību un klasisku izskatu.',
            ],
        ];

        foreach ($types as $type) {
            BrugaVeids::updateOrCreate(
                ['name' => $type['name']],
                [
                    'price_per_m2' => $type['price_per_m2'],
                    'description' => $type['description'],
                ],
            );
        }
    }
}
