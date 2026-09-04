<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = ['Rīga', 'Cēsis', 'līgatne', 'Sigulda', 'Jūrmala', 'Valmiera'];

        foreach (range(1, 8) as $i) {
            \App\Models\PortfolioInfo::create([
                'bruga_veids_id' => rand(1, 2),
                'title' => 'Objekts Nr. ' . $i,
                'description' => 'Piemēra apraksts izstrādes vajadzībām.',
                'city' => $cities[array_rand($cities)],
                'area_m2' => rand(20, 200),
                'completed_year' => rand(2018, 2025),
            ]);
        }
    }
}
