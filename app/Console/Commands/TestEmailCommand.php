<?php

namespace App\Console\Commands;

use App\Models\Simulation\SimulationApplicant;
use App\Notifications\Simulation\ProcessStepCompleted;
use Illuminate\Console\Command;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {applicant_id}';
    protected $description = 'Test email notification for a simulation applicant';

    public function handle()
    {
        $applicantId = $this->argument('applicant_id');
        $applicant = SimulationApplicant::find($applicantId);

        if (!$applicant) {
            $this->error("Applicant with ID {$applicantId} not found");
            return 1;
        }

        if (!$applicant->email) {
            $this->error("Applicant has no email address");
            return 1;
        }

        $this->info("Sending test email to: {$applicant->email}");
        $this->info("Applicant: {$applicant->full_name}");
        
        try {
            $applicant->notify(new ProcessStepCompleted('payment', $applicant));
            $this->info("✅ Notification queued successfully!");
            $this->info("Check your queue worker to process the job");
            $this->info("Run: php artisan queue:work");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}
