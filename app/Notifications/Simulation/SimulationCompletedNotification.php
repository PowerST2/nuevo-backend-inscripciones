<?php

namespace App\Notifications\Simulation;

use App\Models\Simulation\SimulationApplicant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SimulationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected SimulationApplicant $applicant;

    /**
     * Create a new notification instance.
     */
    public function __construct(SimulationApplicant $applicant)
    {
        $this->applicant = $applicant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $exam = $this->applicant->examSimulation;
        $examDescription = $exam->description ?? 'Simulacro de Examen';

        return (new MailMessage)
            ->subject('🎉 ¡Inscripción Completada! - ' . $examDescription)
            ->greeting('¡Felicidades ' . $this->applicant->first_names . '!')
            ->line('Tu inscripción al simulacro ha sido completada exitosamente.')
            ->line('---')
            ->line('## 📋 Tu Código de Inscripción')
            ->line('# **' . $this->applicant->code . '**')
            ->line('---')
            ->line('**Datos del Postulante:**')
            ->line('• **DNI:** ' . $this->applicant->dni)
            ->line('• **Nombre Completo:** ' . $this->applicant->full_name)
            ->line('• **Email:** ' . $this->applicant->email)
            ->line('• **Teléfono:** ' . ($this->applicant->phone_mobile ?? 'No registrado'))
            ->line('---')
            ->line('**Datos del Simulacro:**')
            ->line('• **Código:** ' . ($exam->code ?? 'N/A'))
            ->line('• **Descripción:** ' . $examDescription)
            ->line('• **Fecha de Inicio:** ' . ($exam->exam_date_start?->format('d/m/Y') ?? 'N/A'))
            ->line('• **Fecha de Fin:** ' . ($exam->exam_date_end?->format('d/m/Y') ?? 'N/A'))
            ->line('---')
            ->line('⚠️ **Importante:**')
            ->line('• Guarda este código, lo necesitarás el día del examen')
            ->line('• Tus credenciales para el examen serán tu DNI y el código de inscripción')
            ->line('• Prepara tus dispositivos y conexión a internet con anticipación')
            ->line('---')
            ->salutation('¡Te deseamos mucho éxito en tu simulacro!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'applicant_id' => $this->applicant->id,
            'dni' => $this->applicant->dni,
            'code' => $this->applicant->code,
            'type' => 'simulation_completed',
        ];
    }
}
