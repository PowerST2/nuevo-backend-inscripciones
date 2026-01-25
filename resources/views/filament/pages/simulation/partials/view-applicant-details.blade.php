<div>
    <table style="width: 100%;">
        <tr>
            <td style="width: 120px; vertical-align: top; padding-right: 15px;">
                @if($applicant->hasPhoto())
                    <img src="{{ $applicant->photo_url }}" alt="Foto" style="width: 100px; height: 130px; object-fit: cover;" />
                @else
                    <div style="width: 100px; height: 130px; background: #e5e7eb; display: flex; align-items: center; justify-content: center;">Sin foto</div>
                @endif
            </td>
            <td style="vertical-align: top;">
                <p><strong>Código:</strong> {{ $applicant->code ?? 'Sin asignar' }}</p>
                <p><strong>UUID:</strong> <code style="font-size: 11px; background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">{{ $applicant->uuid }}</code></p>
                <p><strong>DNI:</strong> {{ $applicant->dni }}</p>
                <p><strong>Nombre:</strong> {{ $applicant->full_name }}</p>
                <p><strong>Email:</strong> {{ $applicant->email }}</p>
                <p><strong>Teléfono:</strong> {{ $applicant->phone_mobile ?? 'No registrado' }}</p>
                <p><strong>Foto:</strong> {{ $applicant->hasPhoto() ? 'Cargada' : ($applicant->requiresPhoto() ? 'Requerida - No cargada' : 'No requerida') }}</p>
                <p><strong>Aula Asignada:</strong> {{ $applicant->classroom ?? 'No asignada' }}</p>
            </td>
        </tr>
    </table>

    @if($applicant->simulationProcess)
        <hr style="margin: 15px 0;">
        <p><strong>Estado del Proceso:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Pre-inscripción: {{ $applicant->simulationProcess->pre_registration_at ? $applicant->simulationProcess->pre_registration_at->format('d/m/Y H:i') : 'Pendiente' }}</li>
            <li>Pago: {{ $applicant->simulationProcess->payment_at ? $applicant->simulationProcess->payment_at->format('d/m/Y H:i') : 'Pendiente' }}</li>
            <li>
                Confirmación de Foto:
                @php
                    $photoStatus = $applicant->simulationProcess->photo_status;

                    $statusMap = [
                        'pending'  => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ];
                @endphp

                {{ $statusMap[$photoStatus] ?? 'Pendiente' }}
            </li>            
            <li>Confirmación: {{ $applicant->simulationProcess->data_confirmation_at ? $applicant->simulationProcess->data_confirmation_at->format('d/m/Y H:i') : 'Pendiente' }}</li>
        </ul>
    @endif

    <hr style="margin: 15px 0;">
    <p style="font-size: 12px;"><strong>Registrado:</strong> {{ $applicant->created_at->format('d/m/Y H:i') }} | <strong>Simulacro:</strong> {{ $applicant->examSimulation->description ?? 'N/A' }}</p>
</div>
