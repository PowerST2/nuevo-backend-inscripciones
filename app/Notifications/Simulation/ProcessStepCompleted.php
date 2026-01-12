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
            ->line("### 📋 Siguiente paso:")
            ->line("1. Realiza el pago de la inscripción (S/ {$simulation->tariff->amount})")
            ->line("2. Una vez realizado el pago, espera la confirmación (puede tomar como máximo 1 hora)")
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function paymentMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Pago confirmado - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu pago para el simulacro **{$simulation->description}** ha sido confirmado.")
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
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoApprovedMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("✅ Foto aprobada - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** ha sido aprobada.")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Confirma tus datos personales en el sistema")
            ->line("2. Completa tu inscripción al simulacro")
            ->action('Continuar inscripción', url('/'))
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoRejectedMessage($simulation): MailMessage
    {
        $reason = $this->additionalInfo ?: 'No especificado';
        
        return (new MailMessage)
            ->subject("⚠️ Foto rechazada - {$simulation->description}")
            ->greeting("Hola, {$this->applicant->first_names}")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** fue rechazada.")
            ->line("**Motivo del rechazo:** {$reason}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Por favor, sube una nueva fotografía cumpliendo los siguientes requisitos:")
            ->line("   - Fondo blanco")
            ->line("   - Sin lentes ni accesorios en la cara")
            ->line("   - No selfie ni foto de DNI")
            ->line("   - Foto clara mirando al frente")
            ->action('Subir nueva foto', url('/'))
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
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
            ->line("- Presenta tu ficha el día del simulacro")
            ->line("- Llega con 1 hora de anticipación")
            ->line("¡Te deseamos mucho éxito!")
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function defaultMessage($simulation): MailMessage
    {
        return (new MailMessage)
            ->subject("Actualización de proceso - {$simulation->description}")
            ->greeting("Hola, {$this->applicant->first_names}")
            ->line("Tu proceso de inscripción ha sido actualizado.")
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
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
