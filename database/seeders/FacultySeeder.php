<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty; // <-- Importa el modelo

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de Facultades de la UNI (Perú)
        $faculties = [
            [
                'code' => 'FAUA',
                'name' => 'Facultad de Arquitectura, Urbanismo y Artes',
                'acronym' => 'FAUA'
            ],
            [
                'code' => 'FC',
                'name' => 'Facultad de Ciencias',
                'acronym' => 'FC'
            ],
            [
                'code' => 'FIA',
                'name' => 'Facultad de Ingeniería Ambiental',
                'acronym' => 'FIA'
            ],
            [
                'code' => 'FIC',
                'name' => 'Facultad de Ingeniería Civil',
                'acronym' => 'FIC'
            ],
            [
                'code' => 'FIEECS',
                'name' => 'Facultad de Ingeniería Económica, Estadística y Ciencias Sociales',
                'acronym' => 'FIEECS'
            ],
            [
                'code' => 'FIEE',
                'name' => 'Facultad de Ingeniería Eléctrica y Electrónica',
                'acronym' => 'FIEE'
            ],
            [
                'code' => 'FIGMM',
                'name' => 'Facultad de Ingeniería Geológica, Minera y Metalúrgica',
                'acronym' => 'FIGMM'
            ],
            [
                'code' => 'FIIS',
                'name' => 'Facultad de Ingeniería Industrial y de Sistemas',
                'acronym' => 'FIIS'
            ],
            [
                'code' => 'FIM',
                'name' => 'Facultad de Ingeniería Mecánica',
                'acronym' => 'FIM'
            ],
            [
                'code' => 'FIP',
                'name' => 'Facultad de Ingeniería de Petróleo, Gas Natural y Petroquímica',
                'acronym' => 'FIP'
            ],
            [
                'code' => 'FIQT',
                'name' => 'Facultad de Ingeniería Química y Textil',
                'acronym' => 'FIQT'
            ],
        ];

        // Itera sobre el array y crea o actualiza cada facultad
        foreach ($faculties as $faculty) {
            Faculty::updateOrCreate(
                ['code' => $faculty['code']], // Busca por 'code'
                [ // Inserta o actualiza con esta data
                    'name' => $faculty['name'],
                    'acronym' => $faculty['acronym']
                ]
            );
        }
    }
}
