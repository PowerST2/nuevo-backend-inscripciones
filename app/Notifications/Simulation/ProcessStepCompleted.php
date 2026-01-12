<?php

namespace App\Notifications\Simulation;

use App\Models\Simulation\SimulationApplicant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProcessStepCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $step;
    protected SimulationApplicant $applicant;
    protected ?string $additionalInfo;

    /**
     * Pasos disponibles:
     * - pre_registration: Pre-inscripción completada
     * - payment: Pago confirmado
     * - photo_approved: Foto aprobada
     * - photo_rejected: Foto rechazada
     * - data_confirmation: Datos confirmados
     * - registration: Inscripción completada
     */
    public function __construct(string $step, SimulationApplicant $applicant, ?string $additionalInfo = null)
    {
        $this->step = $step;
        $this->applicant = $applicant;
        $this->additionalInfo = $additionalInfo;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $simulation = $this->applicant->examSimulation;
        
        return match($this->step) {
            'pre_registration' => $this->preRegistrationMessage($simulation),
            'payment' => $this->paymentMessage($simulation),
            'photo_approved' => $this->photoApprovedMessage($simulation),
            'photo_rejected' => $this->photoRejectedMessage($simulation),
            'data_confirmation' => $this->dataConfirmationMessage($simulation),
            'registration' => $this->registrationMessage($simulation),
            default => $this->defaultMessage($simulation),
        };
    }

    protected function preRegistrationMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Pre-inscripción completada - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu pre-inscripción al simulacro **{$simulation->description}** ha sido registrada exitosamente.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Realiza el pago de la inscripción (S/ {$simulation->tariff->amount})")
            ->line("2. Una vez realizado el pago, espera la confirmación (puede tomar hasta 24 horas)")
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Contacta a la Dirección de Admisión para cualquier consulta.")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function paymentMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Pago confirmado - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu pago para el simulacro **{$simulation->description}** ha sido confirmado.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Sube tu fotografía siguiendo las indicaciones del sistema")
            ->line("2. Tu foto será revisada por nuestro equipo")
            ->line("3. Recibirás una notificación cuando sea aprobada")
            ->line("### ⚠️ Requisitos de la fotografía:")
            ->line("- Fondo blanco")
            ->line("- Sin lentes ni accesorios en la cara")
            ->line("- No selfie ni foto de DNI")
            ->line("- Foto clara mirando al frente")
            ->action('Subir foto ahora', url('/'))
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoApprovedMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Foto aprobada - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** ha sido aprobada.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Confirma tus datos personales en el sistema")
            ->line("2. Completa tu inscripción al simulacro")
            ->action('Continuar inscripción', url('/'))
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoRejectedMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("⚠️ Foto rechazada - {$simulation->description}")
            ->greeting("Hola, {$this->applicant->first_names}")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** fue rechazada.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("**Motivo del rechazo:** {$this->additionalInfo}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Por favor, sube una nueva fotografía cumpliendo los siguientes requisitos:")
            ->line("   - Fondo blanco")
            ->line("   - Sin lentes ni accesorios en la cara")
            ->line("   - No selfie ni foto de DNI")
            ->line("   - Foto clara mirando al frente")
            ->action('Subir nueva foto', url('/'))
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function dataConfirmationMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Datos confirmados - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tus datos para el simulacro **{$simulation->description}** han sido confirmados.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📋 Último paso:")
            ->line("1. Completa tu inscripción final en el sistema")
            ->line("2. Una vez completada, recibirás tu constancia de inscripción")
            ->action('Completar inscripción', url('/'))
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function registrationMessage($simulation): MailMessage
    {
        $examDate = $simulation->exam_date_start ? \Carbon\Carbon::parse($simulation->exam_date_start)->format('d/m/Y') : 'Por confirmar';
        
        return (new MailMessage)
            ->subject("🎉 Inscripción completada - {$simulation->description}")
            ->greeting("¡Felicidades, {$this->applicant->first_names}!")
            ->line("Tu inscripción al simulacro **{$simulation->description}** ha sido completada exitosamente.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📅 Información del simulacro:")
            ->line("- **Fecha:** {$examDate}")
            ->line("- **Modalidad:** " . ($simulation->is_virtual ? 'Virtual' : 'Presencial'))
            ->line("### 📋 Importante:")
            ->line("- Guarda tu código de inscripción")
            ->line("- Presenta tu DNI el día del simulacro")
            ->line("- Llega con 30 minutos de anticipación")
            ->line("¡Te deseamos mucho éxito!")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function defaultMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("Actualización de proceso - {$simulation->description}")
            ->greeting("Hola, {$this->applicant->first_names}")
            ->line("Tu proceso de inscripción ha sido actualizado.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    public function toArray($notifiable): array
    {
        return [
            'step' => $this->step,
            'applicant_id' => $this->applicant->id,
            'applicant_name' => $this->applicant->full_name,
        ];
    }
}
