<x-filament-panels::page>
    @if($this->getActiveSimulation())
        @php
            $simulation = $this->getActiveSimulation();
            $stats = $this->getPortfolioStats();
        @endphp

        {{-- Información del Simulacro --}}
        <x-filament::section compact>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Código</span>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $simulation->code }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Descripción</span>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $simulation->description }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tarifa</span>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            @if($simulation->tariff)
                                S/ {{ number_format($simulation->tariff->amount, 2) }}
                            @else
                                <span class="text-danger-600">Sin tarifa</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Tabs --}}
        <x-filament::tabs class="mt-4">
            <x-filament::tabs.item 
                :active="$activeTab === 'upload'" 
                wire:click="$set('activeTab', 'upload')"
                icon="heroicon-o-arrow-up-tray"
            >
                Cargar Pagos BCP
            </x-filament::tabs.item>
            <x-filament::tabs.item 
                :active="$activeTab === 'portfolio'" 
                wire:click="$set('activeTab', 'portfolio')"
                icon="heroicon-o-document-arrow-down"
            >
                Cartera de Pagos
                @if($stats['pending'] > 0)
                    <x-filament::badge color="warning" size="sm" class="ml-2">
                        {{ $stats['pending'] }}
                    </x-filament::badge>
                @endif
            </x-filament::tabs.item>
        </x-filament::tabs>

        {{-- Tab: Cargar Pagos BCP --}}
        @if($activeTab === 'upload')
            <div class="mt-6 space-y-6">
                <x-filament::section>
                    <x-slot name="heading">
                        Subir archivo de pagos
                    </x-slot>
                    <x-slot name="description">
                        Selecciona el archivo CSV exportado del BCP para registrar los pagos de los postulantes.
                    </x-slot>

                    <form wire:submit="processFile" class="space-y-4">
                        {{ $this->form }}

                        <x-filament::button type="submit" color="success" icon="heroicon-o-arrow-up-tray">
                            Procesar Pagos
                        </x-filament::button>
                    </form>
                </x-filament::section>

                @if($this->processResults)
                    <x-filament::section>
                        <x-slot name="heading">
                            Resultados del Procesamiento
                        </x-slot>

                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                            <x-filament::section compact>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->processResults['total'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Total registros</p>
                                </div>
                            </x-filament::section>
                            <x-filament::section compact>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-success-600 dark:text-success-400">{{ $this->processResults['processed'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Procesados</p>
                                </div>
                            </x-filament::section>
                            <x-filament::section compact>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-info-600 dark:text-info-400">{{ $this->processResults['already_paid'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Ya pagados</p>
                                </div>
                            </x-filament::section>
                            <x-filament::section compact>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-warning-600 dark:text-warning-400">{{ $this->processResults['not_found'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No encontrados</p>
                                </div>
                            </x-filament::section>
                            <x-filament::section compact>
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-danger-600 dark:text-danger-400">{{ $this->processResults['errors'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Errores</p>
                                </div>
                            </x-filament::section>
                        </div>

                        @if(count($this->processResults['details']) > 0)
                            <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                                <div class="fi-ta-content overflow-x-auto">
                                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                                        <thead class="bg-gray-50 dark:bg-white/5">
                                            <tr>
                                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">DNI</th>
                                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Nombre</th>
                                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Estado</th>
                                                <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Mensaje</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                            @foreach($this->processResults['details'] as $detail)
                                                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-950 dark:text-white font-medium">{{ $detail['dni'] }}</td>
                                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $detail['name'] ?? '-' }}</td>
                                                    <td class="fi-ta-cell px-4 py-3">
                                                        @if($detail['status'] === 'processed')
                                                            <x-filament::badge color="success">Procesado</x-filament::badge>
                                                        @elseif($detail['status'] === 'already_paid')
                                                            <x-filament::badge color="info">Ya pagado</x-filament::badge>
                                                        @elseif($detail['status'] === 'not_found')
                                                            <x-filament::badge color="warning">No encontrado</x-filament::badge>
                                                        @else
                                                            <x-filament::badge color="danger">Error</x-filament::badge>
                                                        @endif
                                                    </td>
                                                    <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $detail['message'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </x-filament::section>
                @endif
            </div>
        @endif

        {{-- Tab: Cartera de Pagos --}}
        @if($activeTab === 'portfolio')
            <div class="mt-6 space-y-6">
                {{-- Estadísticas de Cartera --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Registros</p>
                        </div>
                    </x-filament::section>
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['pending'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pendientes</p>
                        </div>
                    </x-filament::section>
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['sent'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Enviados</p>
                        </div>
                    </x-filament::section>
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $stats['paid'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pagados</p>
                        </div>
                    </x-filament::section>
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($stats['total_amount'], 2) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Monto Total</p>
                        </div>
                    </x-filament::section>
                    <x-filament::section compact>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">S/ {{ number_format($stats['pending_amount'], 2) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Monto Pendiente</p>
                        </div>
                    </x-filament::section>
                </div>

                {{-- Tabla de Cartera --}}
                <x-filament::section>
                    <x-slot name="heading">
                        Listado de Cartera
                    </x-slot>
                    <x-slot name="description">
                        Descarga la cartera de pagos para enviar al banco.
                    </x-slot>

                    <div class="flex gap-2 mb-4">
                        <x-filament::button 
                            color="success" 
                            icon="heroicon-o-arrow-down-tray"
                            wire:click="downloadPendingPortfolios"
                        >
                            Descargar Pendientes
                        </x-filament::button>
                        <x-filament::button 
                            color="gray" 
                            icon="heroicon-o-document-arrow-down"
                            wire:click="downloadAllPortfolios"
                        >
                            Descargar Todo
                        </x-filament::button>
                    </div>

                    {{ $this->table }}
                </x-filament::section>
            </div>
        @endif
    @else
        <x-filament::section>
            <div class="text-center py-12">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-500/20">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-warning-600 dark:text-warning-400" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-950 dark:text-white">No hay simulacro activo</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Configure un simulacro con fechas que incluyan el día de hoy para poder gestionar pagos.
                </p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
