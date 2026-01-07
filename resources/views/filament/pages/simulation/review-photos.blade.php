<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Header con selector y stats - diseño fluido --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-500/10 via-transparent to-warning-500/10 p-6 dark:from-primary-500/5 dark:to-warning-500/5">
            <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-primary-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-48 w-48 rounded-full bg-warning-500/10 blur-3xl"></div>
            
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                {{-- Selector de simulacro con estilo --}}
                <div class="flex-1">
                    <label class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        Seleccionar Simulacro
                    </label>
                    <div class="relative">
                        <select 
                            wire:model.live="selectedSimulationId"
                            class="w-full lg:max-w-lg appearance-none rounded-xl border-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-4 py-3 pr-10 text-base font-medium text-gray-900 dark:text-white shadow-lg ring-1 ring-gray-900/5 dark:ring-white/10 focus:ring-2 focus:ring-primary-500 transition-all"
                        >
                            <option value="">Elegir simulacro...</option>
                            @foreach($this->simulations as $id => $description)
                                <option value="{{ $id }}">{{ $description }}</option>
                            @endforeach
                        </select>
                        <x-filament::icon icon="heroicon-m-chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                    </div>
                </div>

                {{-- Contador animado --}}
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-warning-500 to-orange-500 blur-lg opacity-50 group-hover:opacity-75 transition-opacity"></div>
                        <div class="relative flex items-center gap-3 rounded-2xl bg-gradient-to-r from-warning-500 to-orange-500 px-6 py-4 text-white shadow-xl">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                                <x-filament::icon icon="heroicon-o-clock" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-3xl font-bold">{{ $this->pendingCount }}</p>
                                <p class="text-sm text-white/80">pendientes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid de fotos --}}
        @if(count($pendingPhotos) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
                @foreach($pendingPhotos as $index => $photo)
                    <div 
                        class="group relative"
                        style="animation: fadeInUp 0.5s ease-out {{ $index * 0.05 }}s both"
                    >
                        {{-- Card con efecto 3D sutil --}}
                        <div class="relative rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 hover:ring-primary-500/50">
                            {{-- Imagen --}}
                            <div 
                                class="relative aspect-[3/4] bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden cursor-pointer"
                                x-data=""
                                x-on:click="$dispatch('open-modal', { id: 'preview-modal-{{ $photo['id'] }}' })"
                            >
                                @if($photo['photo_url'])
                                    <img 
                                        src="{{ $photo['photo_url'] }}" 
                                        alt="Foto de {{ $photo['full_name'] }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy"
                                    >
                                    {{-- Overlay gradiente --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    {{-- Icono de zoom --}}
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <div class="h-14 w-14 rounded-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center justify-center shadow-xl transform scale-50 group-hover:scale-100 transition-transform duration-300">
                                            <x-filament::icon icon="heroicon-m-eye" class="h-7 w-7 text-primary-600 dark:text-primary-400" />
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-filament::icon icon="heroicon-o-user" class="h-16 w-16 text-gray-300 dark:text-gray-600" />
                                    </div>
                                @endif

                                {{-- Badge pendiente con pulso --}}
                                <div class="absolute top-3 left-3">
                                    <span class="relative inline-flex items-center gap-1.5 rounded-full bg-warning-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                        </span>
                                        Revisar
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Info del postulante --}}
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate text-sm" title="{{ $photo['full_name'] }}">
                                    {{ $photo['full_name'] }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                                    {{ $photo['dni'] }}
                                </p>
                                
                                {{-- Botones de acción --}}
                                <div class="flex gap-2 mt-3">
                                    <button 
                                        wire:click="approvePhoto({{ $photo['id'] }})"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 px-3 py-2 text-xs font-semibold text-white shadow-md hover:shadow-lg hover:from-emerald-600 hover:to-green-600 transition-all duration-200 active:scale-95"
                                    >
                                        <x-filament::loading-indicator wire:loading wire:target="approvePhoto({{ $photo['id'] }})" class="h-4 w-4" />
                                        <span wire:loading.remove wire:target="approvePhoto({{ $photo['id'] }})">
                                            <x-filament::icon icon="heroicon-m-check" class="h-4 w-4" />
                                        </span>
                                        <span class="hidden sm:inline" wire:loading.remove wire:target="approvePhoto({{ $photo['id'] }})">Aprobar</span>
                                    </button>
                                    
                                    <button 
                                        wire:click="openRejectModal({{ $photo['id'] }})"
                                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-500 px-3 py-2 text-xs font-semibold text-white shadow-md hover:shadow-lg hover:from-rose-600 hover:to-red-600 transition-all duration-200 active:scale-95"
                                    >
                                        <x-filament::icon icon="heroicon-m-x-mark" class="h-4 w-4" />
                                        <span class="hidden sm:inline">Rechazar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal de preview --}}
                    <x-filament::modal id="preview-modal-{{ $photo['id'] }}" width="4xl" :close-by-clicking-away="true">
                        <div class="flex flex-col lg:flex-row gap-6">
                            {{-- Imagen grande --}}
                            <div class="flex-1 flex items-center justify-center rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 p-4 min-h-[400px]">
                                <img 
                                    src="{{ $photo['photo_url'] }}" 
                                    alt="Foto de {{ $photo['full_name'] }}"
                                    class="max-h-[65vh] w-auto rounded-xl shadow-2xl"
                                >
                            </div>
                            
                            {{-- Panel lateral con info y acciones --}}
                            <div class="lg:w-80 flex flex-col">
                                {{-- Info del postulante --}}
                                <div class="space-y-4 flex-1">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $photo['full_name'] }}
                                        </h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Postulante
                                        </p>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                            <div class="h-10 w-10 rounded-lg bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                                                <x-filament::icon icon="heroicon-m-identification" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">DNI</p>
                                                <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ $photo['dni'] }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                                <x-filament::icon icon="heroicon-m-envelope" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                                                <p class="font-medium text-gray-900 dark:text-white truncate text-sm">{{ $photo['email'] }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                            <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                                                <x-filament::icon icon="heroicon-m-calendar" class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Fecha de subida</p>
                                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $photo['photo_at'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Botones de acción grandes --}}
                                <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                                    <button 
                                        wire:click="approvePhoto({{ $photo['id'] }})"
                                        x-on:click="$dispatch('close-modal', { id: 'preview-modal-{{ $photo['id'] }}' })"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 px-4 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl hover:from-emerald-600 hover:to-green-600 transition-all duration-200"
                                    >
                                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5" />
                                        Aprobar Foto
                                    </button>
                                    
                                    <button 
                                        wire:click="openRejectModal({{ $photo['id'] }})"
                                        x-on:click="$dispatch('close-modal', { id: 'preview-modal-{{ $photo['id'] }}' })"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-500 px-4 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl hover:from-rose-600 hover:to-red-600 transition-all duration-200"
                                    >
                                        <x-filament::icon icon="heroicon-m-x-circle" class="h-5 w-5" />
                                        Rechazar Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-filament::modal>
                @endforeach
            </div>

            {{-- Tips flotante --}}
            <div class="flex justify-center">
                <div class="inline-flex items-center gap-6 rounded-full bg-gray-100 dark:bg-gray-800 px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center gap-2">
                        <x-filament::icon icon="heroicon-m-cursor-arrow-rays" class="h-4 w-4 text-primary-500" />
                        Clic para ampliar
                    </span>
                    <span class="h-4 w-px bg-gray-300 dark:bg-gray-600"></span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        Aprobar
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                        Rechazar
                    </span>
                </div>
            </div>
        @else
            {{-- Estado vacío con diseño dinámico --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500/5 via-transparent to-blue-500/5 p-12">
                <div class="absolute -top-32 -right-32 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
                <div class="absolute -bottom-32 -left-32 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
                
                <div class="relative text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-emerald-400 to-green-500 shadow-xl shadow-emerald-500/25">
                        <x-filament::icon icon="heroicon-o-check-badge" class="h-12 w-12 text-white" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if($selectedSimulationId)
                            ¡Todo revisado! 🎉
                        @else
                            Selecciona un simulacro
                        @endif
                    </h3>
                    <p class="mt-3 text-base text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                        @if($selectedSimulationId)
                            No hay fotos pendientes de revisión. ¡Excelente trabajo!
                        @else
                            Usa el selector de arriba para ver las fotos pendientes de cada simulacro.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- Modal de rechazo con diseño mejorado --}}
        <x-filament::modal id="reject-photo-modal" width="lg">
            <div class="space-y-6">
                {{-- Header visual --}}
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-red-500 shadow-lg shadow-rose-500/25">
                        <x-filament::icon icon="heroicon-m-camera" class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Rechazar Foto</h2>
                        @if($this->rejectingApplicant)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $this->rejectingApplicant['full_name'] }} · {{ $this->rejectingApplicant['dni'] }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Alerta informativa con estilo --}}
                <div class="rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-500/10 dark:to-orange-500/10 p-4 ring-1 ring-amber-200 dark:ring-amber-500/20">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-500/20">
                                <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-amber-800 dark:text-amber-200">El postulante verá este mensaje</h3>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                Deberá subir una nueva foto que cumpla los requisitos para continuar.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Campo de motivo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">
                        Motivo del rechazo <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        wire:model="rejectReason"
                        rows="4"
                        class="w-full rounded-xl border-0 bg-gray-50 dark:bg-gray-800 px-4 py-3 text-gray-900 dark:text-white ring-1 ring-gray-200 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 transition-all resize-none"
                        placeholder="Describe el problema de la foto para que el postulante pueda corregirlo..."
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Sé específico: menciona si está borrosa, mal encuadrada, con poca luz, etc.
                    </p>
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button 
                        wire:click="cancelReject"
                        class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button 
                        wire:click="confirmReject"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl hover:from-rose-600 hover:to-red-600 transition-all duration-200 disabled:opacity-50"
                    >
                        <x-filament::loading-indicator wire:loading wire:target="confirmReject" class="h-4 w-4" />
                        <x-filament::icon wire:loading.remove wire:target="confirmReject" icon="heroicon-m-x-circle" class="h-4 w-4" />
                        <span wire:loading.remove wire:target="confirmReject">Confirmar Rechazo</span>
                        <span wire:loading wire:target="confirmReject">Procesando...</span>
                    </button>
                </div>
            </div>
        </x-filament::modal>
    </div>

    {{-- Estilos de animación --}}
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-filament-panels::page>
