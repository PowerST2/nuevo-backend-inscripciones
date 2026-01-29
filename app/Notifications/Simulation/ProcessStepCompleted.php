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
        $tariffAmount = $this->applicant->tariff?->amount ?? 'Ver sistema';
        
        $message = (new MailMessage)
            ->subject("✅ Pre-inscripción completada - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu pre-inscripción al simulacro **{$simulation->description}** ha sido registrada exitosamente.")
            ->line("**Modalidad:** {$simulation->modality_text}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Realiza el pago de la inscripción (S/ {$tariffAmount})")
            ->line("2. Una vez realizado el pago, espera la confirmación (puede tomar como máximo 1 hora)");
        
        if (!$simulation->is_virtual) {
            $message->line("3. Después del pago, deberás subir tu fotografía para el carnet");
        }
        
        return $message
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function paymentMessage($simulation): MailMessage
    {
        $examDate = $simulation->exam_date ? \Carbon\Carbon::parse($simulation->exam_date)->format('d/m/Y') : 'Por confirmar';
        
        $message = (new MailMessage)
            ->subject("✅ Pago confirmado - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu pago para el simulacro **{$simulation->description}** ha sido confirmado.")
            ->line("**Modalidad:** {$simulation->modality_text}");
        
        if ($simulation->is_virtual) {
            // Simulacro virtual - no requiere foto
            $message
                ->line("### 📋 Siguiente paso:")
                ->line("1. Confirma tus datos personales en el sistema")
                ->line("2. Completa tu inscripción al simulacro")
                ->action('Continuar inscripción', config('app.url_simulacro') . '/intranet/personal-data-confirm')
                ->line("### ℹ️ Información importante:")
                ->line("- El examen estará disponible durante todo el día de la evaluación (**Fecha:** {$examDate})")
                ->line("- Tendrás **un solo intento** de **3 horas** para completar el examen")
                ->line("- Asegúrate de tener una conexión a internet estable");
        } else {
            // Simulacro presencial - requiere foto
            $message
                ->line("### 📋 Siguiente paso:")
                ->line("1. Sube tu fotografía siguiendo las indicaciones del sistema")
                ->line("2. Tu foto será revisada por nuestro equipo")
                ->line("3. Recibirás una notificación cuando sea aprobada")
                ->line("### ⚠️ Requisitos de la fotografía:")
                ->line("- Fondo blanco")
                ->line("- Sin lentes ni accesorios en la cara")
                ->line("- No selfie ni foto de DNI")
                ->line("- Foto clara mirando al frente")
                ->action('Subir foto ahora', config('app.url_simulacro') . '/intranet/personal-photo');
        }
        
        return $message
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoApprovedMessage($simulation): MailMessage
    {
        // Este mensaje solo aplica para simulacros presenciales
        return (new MailMessage)
            ->subject("✅ Foto aprobada - {$simulation->description}")
            ->greeting("¡Hola, {$this->applicant->first_names}!")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** ha sido aprobada.")
            ->line("**Modalidad:** {$simulation->modality_text}")
            ->line("### 📋 Siguiente paso:")
            ->line("1. Confirma tus datos personales en el sistema")
            ->line("2. Completa tu inscripción al simulacro")
            ->line("3. Descarga e imprime tu ficha de inscripción")
            ->action('Continuar inscripción', config('app.url_simulacro') . '/intranet/personal-data-confirm')
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function photoRejectedMessage($simulation): MailMessage
    {
        $reason = $this->additionalInfo ?: 'No especificado';
        
        // Este mensaje solo aplica para simulacros presenciales
        return (new MailMessage)
            ->subject("⚠️ Foto rechazada - {$simulation->description}")
            ->greeting("Hola, {$this->applicant->first_names}")
            ->line("Tu fotografía para el simulacro **{$simulation->description}** fue rechazada.")
            ->line("**Modalidad:** {$simulation->modality_text}")
            ->line("**Motivo del rechazo:** {$reason}")
            ->line("### 📋 Siguiente paso:")
            ->line("Por favor, sube una nueva fotografía cumpliendo los siguientes requisitos:")
            ->line("- Fondo blanco")
            ->line("- Sin lentes ni accesorios en la cara")
            ->line("- No selfie ni foto de DNI")
            ->line("- Foto clara mirando al frente")
            ->action('Subir nueva foto', config('app.url_simulacro') . '/intranet/personal-photo')
            ->line("### 📞 ¿Necesitas ayuda?")
            ->line("Escríbenos a informes.admision@uni.edu.pe")
            ->line("Llamadas y Whatsapp: 981 606 955 - 981 600 816 - 981 609 170")
            ->salutation('Saludos, DIAD-UNI Admisión');
    }

    protected function registrationMessage($simulation): MailMessage
    {
        $examDate = $simulation->exam_date ? \Carbon\Carbon::parse($simulation->exam_date)->format('d/m/Y') : 'Por confirmar';
        
        $message = (new MailMessage)
            ->subject("🎉 Inscripción completada - {$simulation->description}")
            ->greeting("¡Felicidades, {$this->applicant->first_names}!")
            ->line("Tu inscripción al simulacro **{$simulation->description}** ha sido completada exitosamente.")
            ->line("**Código de inscripción:** {$this->applicant->code}")
            ->line("### 📅 Información del simulacro:")
            ->line("- **Fecha:** {$examDate}")
            ->line("- **Modalidad:** {$simulation->modality_text}");
        
        if ($simulation->include_vocational && $this->applicant->include_vocational) {
            $message->line("- **Incluye:** Examen vocacional");
        }
        
        $message->line("### 📋 Importante:");
        
        if ($simulation->is_virtual) {
            $message
                ->line("- Guarda tu código de inscripción")
                ->line("- Tus credenciales de acceso te serán enviadas un día antes del examen a tu correo registrado")
                ->line("- El examen estará disponible durante todo el día {$examDate}")
                ->line("- Tendrás **un solo intento** de **3 horas** para completar el examen")
                ->line("- Asegúrate de tener una conexión a internet estable")
                ->line("- Busca un lugar tranquilo sin interrupciones");
        } else {
            $message
                ->line("- Guarda tu código de inscripción")
                ->line("- Presenta tu ficha impresa el día del simulacro")
                ->line("- Llega con **1 hora de anticipación** al local asignado")
                ->line("- Porta tu DNI vigente");
            
            if ($this->applicant->classroom) {
                $message->line("- **Aula asignada:** {$this->applicant->classroom->code}");
            }
        }
        
        return $message
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
            ->line("Tu proceso de inscripción al simulacro **{$simulation->description}** ha sido actualizado.")
            ->line("**Modalidad:** {$simulation->modality_text}")
            ->action('Ver mi estado', config('app.url_simulacro') . '/intranet')
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
