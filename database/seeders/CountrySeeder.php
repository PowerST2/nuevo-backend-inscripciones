<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/data/countries.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("El archivo countries.json no se encontró en la ruta especificada.");
            return;
        }

        $json = File::get($jsonPath);
        $countries = json_decode($json, true);

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                ['name' => $country['name']]
            );
        }

        $this->command->info('¡La tabla de países se ha poblado exitosamente!');
    }
}