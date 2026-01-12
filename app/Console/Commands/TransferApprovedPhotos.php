<?php

namespace App\Console\Commands;

use App\Models\Simulation\SimulationApplicant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TransferApprovedPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photos:transfer-approved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transferir fotos aprobadas a la carpeta de fotos aprobadas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando transferencia de fotos aprobadas...');

        // Buscar todos los postulantes con fotos aprobadas
        $applicants = SimulationApplicant::whereHas('simulationProcess', function ($query) {
            $query->where('photo_status', 'approved');
        })
        ->whereNotNull('photo_path')
        ->with(['simulationProcess', 'examSimulation'])
        ->get();

        if ($applicants->isEmpty()) {
            $this->warn('No se encontraron fotos aprobadas para transferir.');
            return 0;
        }

        $this->info("Se encontraron {$applicants->count()} fotos aprobadas.");

        $transferred = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($applicants as $applicant) {
            try {
                // Verificar que el archivo original existe
                if (!Storage::disk('public')->exists($applicant->photo_path)) {
                    $this->warn("⚠️  Foto no encontrada: {$applicant->photo_path} (DNI: {$applicant->dni})");
                    $skipped++;
                    continue;
                }

                $simulationCode = $applicant->examSimulation->code ?? $applicant->exam_simulation_id;
                $extension = pathinfo($applicant->photo_path, PATHINFO_EXTENSION);
                $approvedFilename = $applicant->dni . '.' . $extension;
                $approvedPath = 'simulation-photos-approved/' . $simulationCode . '/' . $approvedFilename;

                // Verificar si ya existe en la carpeta de aprobadas
                if (Storage::disk('public')->exists($approvedPath)) {
                    $this->info("✓ Ya existe: {$approvedPath}");
                    $skipped++;
                    continue;
                }

                // Copiar archivo a la carpeta de aprobadas
                Storage::disk('public')->copy($applicant->photo_path, $approvedPath);
                
                $this->info("✓ Transferida: {$applicant->dni} → {$approvedPath}");
                $transferred++;
            } catch (\Exception $e) {
                $this->error("✗ Error transfiriendo {$applicant->dni}: {$e->getMessage()}");
                $failed++;
            }
        }

        // Resumen
        $this->info("\n" . str_repeat('=', 50));
        $this->line("📊 Resumen de transferencia:");
        $this->line("  Transferidas: <fg=green>{$transferred}</>");
        $this->line("  Ya existentes: <fg=yellow>{$skipped}</>");
        $this->line("  Errores: <fg=red>{$failed}</>");
        $this->info(str_repeat('=', 50));

        return 0;
    }
}
