<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header con filtros --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                {{-- Selector de simulacro --}}
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Simulacro
                    </label>
                    <select 
                        wire:model.live="selectedSimulationId"
                        class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Seleccionar simulacro...</option>
                        @foreach($this->simulations as $id => $description)
                            <option value="{{ $id }}">{{ $description }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Búsqueda por DNI --}}
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Buscar por DNI
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchDni"
                            placeholder="Ingrese DNI..."
                            class="w-full rounded-lg border-gray-300 bg-white pl-10 pr-10 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                        @if($searchDni)
                            <button 
                                wire:click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <x-filament::icon icon="heroicon-m-x-mark" class="h-4 w-4" />
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Contador --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 rounded-lg bg-warning-50 dark:bg-warning-500/10 px-4 py-2.5">
                        <x-filament::icon icon="heroicon-m-clock" class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                        <span class="text-lg font-bold text-warning-600 dark:text-warning-400">{{ $this->pendingCount }}</span>
                        <span class="text-sm text-warning-600/80 dark:text-warning-400/80">pendientes</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visor de foto actual --}}
        @if($this->currentPhoto)
            <div wire:key="photo-{{ $this->currentPhoto['id'] }}" class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="flex flex-col lg:flex-row">
                    {{-- Imagen grande --}}
                    <div class="lg:w-1/2 xl:w-2/5 bg-gray-100 dark:bg-gray-800 p-6 flex items-center justify-center min-h-[400px] lg:min-h-[500px]">
                        <div class="relative">
                            <img 
                                src="{{ $this->currentPhoto['photo_url'] }}" 
                                alt="Foto de {{ $this->currentPhoto['full_name'] }}"
                                class="max-h-[450px] w-auto rounded-xl shadow-2xl"
                            >
                            {{-- Badge de posición --}}
                            <div class="absolute -top-3 -right-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 text-white font-bold shadow-lg">
                                {{ $currentIndex + 1 }}
                            </div>
                        </div>
                    </div>

                    {{-- Panel de información --}}
                    <div class="lg:w-1/2 xl:w-3/5 p-6 flex flex-col">
                        {{-- Datos del postulante --}}
                        <div class="flex-1 space-y-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $this->currentPhoto['full_name'] }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Postulante pendiente de revisión
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-500/20">
                                            <x-filament::icon icon="heroicon-m-identification" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">DNI</p>
                                            <p class="font-mono font-bold text-gray-900 dark:text-white">{{ $this->currentPhoto['dni'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-500/20">
                                            <x-filament::icon icon="heroicon-m-envelope" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                                            <p class="font-medium text-gray-900 dark:text-white truncate text-sm">{{ $this->currentPhoto['email'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($this->currentPhoto['phone'])
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-500/20">
                                            <x-filament::icon icon="heroicon-m-phone" class="h-5 w-5 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Teléfono</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $this->currentPhoto['phone'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-500/20">
                                            <x-filament::icon icon="heroicon-m-calendar" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Fecha de subida</p>
                                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $this->currentPhoto['photo_at'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <x-filament::button 
                                    color="success" 
                                    size="xl"
                                    wire:click="approveCurrent"
                                    wire:loading.attr="disabled"
                                    class="flex-1 justify-center"
                                >
                                    <x-filament::loading-indicator wire:loading wire:target="approveCurrent" class="h-5 w-5" />
                                    <span wire:loading.remove wire:target="approveCurrent" class="flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5" />
                                        Aprobar Foto
                                    </span>
                                </x-filament::button>

                                <x-filament::button 
                                    color="danger" 
                                    size="xl"
                                    wire:click="rejectCurrent"
                                    class="flex-1 justify-center"
                                >
                                    <x-filament::icon icon="heroicon-m-x-circle" class="h-5 w-5" />
                                    Rechazar Foto
                                </x-filament::button>
                            </div>

                            {{-- Atajos de teclado --}}
                            <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
                                Usa las flechas <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 font-mono">←</kbd> <kbd class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 font-mono">→</kbd> para navegar
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Navegación inferior --}}
                @if(count($pendingPhotos) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            {{-- Botón anterior --}}
                            <x-filament::button 
                                color="gray" 
                                wire:click="previousPhoto"
                                :disabled="$currentIndex === 0"
                            >
                                <x-filament::icon icon="heroicon-m-chevron-left" class="h-5 w-5" />
                                Anterior
                            </x-filament::button>

                            {{-- Indicadores de posición --}}
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $currentIndex + 1 }} de {{ count($pendingPhotos) }} en esta página
                                </span>
                                
                                {{-- Mini thumbnails --}}
                                <div class="hidden sm:flex items-center gap-1 ml-4">
                                    @foreach(array_slice($pendingPhotos, max(0, $currentIndex - 2), 5) as $index => $photo)
                                        @php $realIndex = max(0, $currentIndex - 2) + $index; @endphp
                                        <button 
                                            wire:click="goToPhoto({{ $realIndex }})"
                                            class="relative w-8 h-8 rounded overflow-hidden ring-2 transition-all {{ $realIndex === $currentIndex ? 'ring-primary-500 scale-110' : 'ring-transparent opacity-60 hover:opacity-100' }}"
                                        >
                                            <img 
                                                src="{{ $photo['photo_url'] }}" 
                                                alt=""
                                                class="w-full h-full object-cover"
                                            >
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Botón siguiente --}}
                            <x-filament::button 
                                color="gray" 
                                wire:click="nextPhoto"
                                :disabled="$currentIndex >= count($pendingPhotos) - 1"
                            >
                                Siguiente
                                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5" />
                            </x-filament::button>
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-4 flex items-center justify-center gap-3">
                            <x-filament::button color="gray" wire:click="previousPagePhotos" :disabled="$this->currentPage <= 1">
                                <x-filament::icon icon="heroicon-m-chevron-left" class="h-5 w-5" />
                                Página anterior
                            </x-filament::button>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Página {{ $this->currentPage }} de {{ $this->lastPage }}
                            </span>
                            <x-filament::button color="gray" wire:click="nextPagePhotos" :disabled="$this->currentPage >= $this->lastPage">
                                Siguiente página
                                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5" />
                            </x-filament::button>
                        </div>
                    </div>
                @endif
            </div>
        @else
            {{-- Estado vacío --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-12">
                <div class="text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-success-100 dark:bg-success-500/20">
                        <x-filament::icon icon="heroicon-o-check-badge" class="h-10 w-10 text-success-600 dark:text-success-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        @if(!$selectedSimulationId)
                            Selecciona un simulacro
                        @elseif($searchDni)
                            No se encontraron resultados
                        @else
                            ¡Todas las fotos revisadas!
                        @endif
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        @if(!$selectedSimulationId)
                            Elige un simulacro del selector para ver las fotos pendientes de revisión.
                        @elseif($searchDni)
                            No hay fotos pendientes para el DNI "{{ $searchDni }}".
                            <button wire:click="clearSearch" class="text-primary-600 hover:underline ml-1">Limpiar búsqueda</button>
                        @else
                            No hay fotos pendientes de revisión para este simulacro. ¡Excelente trabajo!
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- Modal de rechazo --}}
        <x-filament::modal id="reject-photo-modal" width="md">
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-500/20">
                        <x-filament::icon icon="heroicon-m-x-mark" class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                    </div>
                    <span>Rechazar Foto</span>
                </div>
            </x-slot>

            <x-slot name="description">
                @if($this->rejectingApplicant)
                    {{ $this->rejectingApplicant['full_name'] }} ({{ $this->rejectingApplicant['dni'] }})
                @endif
            </x-slot>

            <div class="space-y-4">
                <div class="rounded-lg bg-warning-50 dark:bg-warning-500/10 p-4">
                    <div class="flex gap-3">
                        <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5 text-warning-600 dark:text-warning-400 flex-shrink-0 mt-0.5" />
                        <div class="text-sm">
                            <p class="font-medium text-warning-800 dark:text-warning-200">El postulante recibirá este mensaje</p>
                            <p class="mt-1 text-warning-700 dark:text-warning-300">Deberá subir una nueva foto para poder continuar.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo del rechazo <span class="text-danger-500">*</span>
                        </label>
                        <select 
                            wire:model.live="rejectReasonSelected"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="">Seleccione un motivo...</option>
                            @foreach($rejectReasons as $reason)
                                <option value="{{ $reason }}">{{ $reason }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Detalle adicional (opcional)
                        </label>
                        <textarea 
                            wire:model="rejectReason"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Agregar detalle específico si es necesario..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <x-slot name="footerActions">
                <x-filament::button color="gray" wire:click="cancelReject">
                    Cancelar
                </x-filament::button>
                <x-filament::button 
                    color="danger" 
                    wire:click="confirmReject"
                    wire:loading.attr="disabled"
                >
                    <x-filament::loading-indicator wire:loading wire:target="confirmReject" class="h-4 w-4" />
                    <span wire:loading.remove wire:target="confirmReject">Confirmar Rechazo</span>
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>

    {{-- Atajos de teclado --}}
    @if($this->currentPhoto)
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            
            if (e.key === 'ArrowLeft') {
                @this.previousPhoto();
            } else if (e.key === 'ArrowRight') {
                @this.nextPhoto();
            }
        });
    </script>
    @endif
</x-filament-panels::page>
