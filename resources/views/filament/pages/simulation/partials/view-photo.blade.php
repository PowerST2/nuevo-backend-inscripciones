<div>
    @if($applicant->hasPhoto())
        <div class="text-center">
            <img src="{{ $applicant->photo_url }}" alt="Foto de {{ $applicant->full_name }}" style="max-height: 300px; margin: 0 auto;" />
            <p class="mt-2"><strong>DNI:</strong> {{ $applicant->dni }} | <strong>Código:</strong> {{ $applicant->code ?? 'Sin asignar' }}</p>
        </div>
    @else
        <p class="text-center">Este postulante no tiene foto registrada.</p>
    @endif
</div>
