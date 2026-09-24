<?php

namespace Database\Seeders;

use App\Models\BrugaVeids;
use App\Models\PortfolioInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PortfolioInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $betonaBrugis = BrugaVeids::where('name', 'Betona bruģis')->firstOrFail();
        $granitaBrugakmens = BrugaVeids::where('name', 'Granīta bruģakmens')->firstOrFail();

        PortfolioInfo::where('title', 'like', 'Objekts Nr. %')->get()->each(function (PortfolioInfo $portfolio): void {
            foreach ($portfolio->bildes as $bilde) {
                Storage::disk('public')->delete($bilde->image_path);
            }

            $portfolio->delete();
        });

        $projects = [
            ['title' => 'Pakāpieni pie mājas', 'description' => 'Pakāpienu un ieejas zonas izbūve ar tumšu betona bruģi.', 'city' => 'Rīga', 'area_m2' => 28, 'completed_year' => 2025, 'type_id' => $betonaBrugis->id, 'image' => 'brugis5.jpg'],
            ['title' => 'Mājas pagalms', 'description' => 'Plašs un praktisks pagalms ikdienas auto novietošanai.', 'city' => 'Mārupe', 'area_m2' => 145, 'completed_year' => 2024, 'type_id' => $betonaBrugis->id, 'image' => 'brugis1.jpg'],
            ['title' => 'Dārza celiņš', 'description' => 'Krāsains bruģa celiņš ar sakoptām apmalēm.', 'city' => 'Sigulda', 'area_m2' => 42, 'completed_year' => 2024, 'type_id' => $betonaBrugis->id, 'image' => 'brugis6.jpg'],
            ['title' => 'Iebraucamais ceļš', 'description' => 'Izturīgs iebraucamais ceļš privātmājas teritorijā.', 'city' => 'Jūrmala', 'area_m2' => 210, 'completed_year' => 2023, 'type_id' => $betonaBrugis->id, 'image' => 'brugis3.jpg'],
            ['title' => 'Taisnais pagalms', 'description' => 'Vienmērīgs bruģējums lielai atpūtas un auto novietošanas zonai.', 'city' => 'Cēsis', 'area_m2' => 180, 'completed_year' => 2023, 'type_id' => $betonaBrugis->id, 'image' => 'brugis2.jpg'],
            ['title' => 'Vakara terase', 'description' => 'Terases bruģējums ar dekoratīvu apgaismojumu gar malu.', 'city' => 'Rīga', 'area_m2' => 76, 'completed_year' => 2022, 'type_id' => $betonaBrugis->id, 'image' => 'brugis9.jpg'],
            ['title' => 'Lauku piebraucamais ceļš', 'description' => 'Plašs piebraucamais ceļš ar klasisku klājumu.', 'city' => 'Valmiera', 'area_m2' => 260, 'completed_year' => 2022, 'type_id' => $betonaBrugis->id, 'image' => 'brugis7.jpg'],
            ['title' => 'Tumšais pagalms', 'description' => 'Moderns tumša bruģa risinājums pie garāžas un mājas.', 'city' => 'Ogre', 'area_m2' => 118, 'completed_year' => 2021, 'type_id' => $betonaBrugis->id, 'image' => 'brugis4.jpg'],
            ['title' => 'Ieejas zona', 'description' => 'Gaišs bruģējums ap māju ar noapaļotām dobju malām.', 'city' => 'Jelgava', 'area_m2' => 95, 'completed_year' => 2021, 'type_id' => $betonaBrugis->id, 'image' => 'brugis8.jpg'],
            ['title' => 'Klasiska bruģa zona', 'description' => 'Kārtīgs divu toņu bruģējums plašai privātmājas teritorijai.', 'city' => 'Līgatne', 'area_m2' => 165, 'completed_year' => 2020, 'type_id' => $granitaBrugakmens->id, 'image' => 'brugis10.jpg'],
        ];

        foreach ($projects as $project) {
            $portfolio = PortfolioInfo::updateOrCreate(
                ['title' => $project['title']],
                [
                    'bruga_veids_id' => $project['type_id'],
                    'description' => $project['description'],
                    'city' => $project['city'],
                    'area_m2' => $project['area_m2'],
                    'completed_year' => $project['completed_year'],
                ],
            );

            $portfolio->bildes()->updateOrCreate(
                ['image_path' => 'portfolio/' . $project['image']],
                [],
            );
        }
    }
}
