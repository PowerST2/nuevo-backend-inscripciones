<x-filament-panels::page>
    {{-- Estadísticas --}}
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $stats['total'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total Registros
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-warning-600">
                    {{ $stats['pending'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Pendientes de Envío
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-success-600">
                    {{ $stats['sent'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Enviados
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600">
                    {{ $stats['paid'] }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Pagados
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    S/ {{ number_format($stats['total_amount'], 2) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Monto Total
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-warning-600">
                    S/ {{ number_format($stats['pending_amount'], 2) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Monto Pendiente
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Información del Simulacro --}}
    <x-filament::section>
        <x-slot name="heading">
            Información del Simulacro
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Código:</span>
                <p class="text-gray-900 dark:text-white">{{ $record->code }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción:</span>
                <p class="text-gray-900 dark:text-white">{{ $record->description }}</p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarifa:</span>
                <p class="text-gray-900 dark:text-white">
                    @if($record->tariff)
                        {{ $record->tariff->code }} - S/ {{ number_format($record->tariff->amount, 2) }}
                    @else
                        <span class="text-danger-600">Sin tarifa asignada</span>
                    @endif
                </p>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fechas:</span>
                <p class="text-gray-900 dark:text-white">
                    {{ $record->exam_date_start->format('d/m/Y') }} - {{ $record->exam_date_end->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </x-filament::section>

    {{-- Tabla de Pagos --}}
    <x-filament::section>
        <x-slot name="heading">
            Historial de Pagos / Cartera
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
