<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty; // <-- Importa el modelo Faculty
use App\Models\Major;   // <-- Importa el modelo Major (App con mayúscula)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define las especialidades agrupadas por el CÓDIGO de su facultad
        $majorsByFaculty = [
            'FAUA' => [
                ['code' => 'A1', 'name' => 'Arquitectura', 'channel' => 1],
                ['code' => 'A2', 'name' => 'Urbanismo', 'channel' => 1], // Código 'A2' inferido
            ],
            'FC' => [
                ['code' => 'N1', 'name' => 'Física', 'channel' => 3],
                ['code' => 'N2', 'name' => 'Matemática', 'channel' => 3],
                ['code' => 'N3', 'name' => 'Química', 'channel' => 3],
                ['code' => 'N5', 'name' => 'Ingeniería Física', 'channel' => 3],
                ['code' => 'N6', 'name' => 'Ciencia de la Computación', 'channel' => 3],
            ],
            'FIA' => [
                ['code' => 'S1', 'name' => 'Ingeniería Sanitaria', 'channel' => 2],
                ['code' => 'S2', 'name' => 'Ingeniería de Higiene y Seguridad Industrial', 'channel' => 2],
                ['code' => 'S3', 'name' => 'Ingeniería Ambiental', 'channel' => 2],
            ],
            'FIC' => [
                ['code' => 'C1', 'name' => 'Ingeniería Civil', 'channel' => 2],
            ],
            'FIEECS' => [
                ['code' => 'E1', 'name' => 'Ingeniería Económica', 'channel' => 1],
                ['code' => 'E3', 'name' => 'Ingeniería Estadística', 'channel' => 1],
            ],
            'FIEE' => [
                ['code' => 'L1', 'name' => 'Ingeniería Eléctrica', 'channel' => 2],
                ['code' => 'L2', 'name' => 'Ingeniería Electrónica', 'channel' => 2],
                ['code' => 'L3', 'name' => 'Ingeniería de Telecomunicaciones', 'channel' => 2],
                ['code' => 'L4', 'name' => 'Ingeniería de Ciberseguridad', 'channel' => 2],
            ],
            'FIGMM' => [
                ['code' => 'G1', 'name' => 'Ingeniería Geológica', 'channel' => 3],
                ['code' => 'G2', 'name' => 'Ingeniería Metalúrgica', 'channel' => 3],
                ['code' => 'G3', 'name' => 'Ingeniería de Minas', 'channel' => 3],
            ],
            'FIIS' => [
                ['code' => 'I1', 'name' => 'Ingeniería Industrial', 'channel' => 2],
                ['code' => 'I2', 'name' => 'Ingeniería de Sistemas', 'channel' => 2],
                ['code' => 'I3', 'name' => 'Ingeniería de Software', 'channel' => 2],
            ],
            'FIM' => [
                ['code' => 'M3', 'name' => 'Ingeniería Mecánica', 'channel' => 2],
                ['code' => 'M4', 'name' => 'Ingeniería Mecánica Eléctrica', 'channel' => 2],
                ['code' => 'M5', 'name' => 'Ingeniería Naval', 'channel' => 2],
                ['code' => 'M6', 'name' => 'Ingeniería Mecatrónica', 'channel' => 2],
                ['code' => 'M7', 'name' => 'Ingeniería Aeroespacial', 'channel' => 2], // Código 'M7' inferido
            ],
            'FIP' => [
                ['code' => 'P2', 'name' => 'Ingeniería Petroquímica', 'channel' => 1],
                ['code' => 'P3', 'name' => 'Ingeniería de Petróleo y Gas Natural', 'channel' => 1],
            ],
            'FIQT' => [
                ['code' => 'Q1', 'name' => 'Ingeniería Química', 'channel' => 1],
                ['code' => 'Q2', 'name' => 'Ingeniería Textil', 'channel' => 1],
            ],
        ];

        $this->command->info('Iniciando Seeder de Especialidades (Majors)...');
        
        // Usamos una transacción para asegurar que todo se inserte correctamente
        DB::transaction(function () use ($majorsByFaculty) {
            foreach ($majorsByFaculty as $facultyCode => $majors) {
                // 1. Busca la facultad por su CÓDIGO (debe existir por el FacultySeeder)
                $faculty = Faculty::where('code', $facultyCode)->first();

                if (!$faculty) {
                    $this->command->error("  [Error] Facultad con código '$facultyCode' no encontrada. Saltando sus especialidades.");
                    Log::error("FacultySeeder: Facultad '$facultyCode' no encontrada.");
                    continue; // Salta a la siguiente facultad
                }

                $this->command->info("  -> Procesando Facultad: {$faculty->name}");

                // 2. Itera e inserta/actualiza cada especialidad
                foreach ($majors as $majorData) {
                    Major::updateOrCreate(
                        ['code' => $majorData['code']], // Busca por 'code'
                        [ // Inserta o actualiza con esta data
                            'name' => $majorData['name'],
                            'channel' => $majorData['channel'],
                            'faculty_id' => $faculty->id // Asigna el ID de la facultad encontrada
                        ]
                    );
                }
            }
        });

        $this->command->info('Seeder de Especialidades (Majors) completado.');
    }
}
