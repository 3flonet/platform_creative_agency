<x-filament-panels::page>
    <style>
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.02); }
        }
        .pulse-active { animation: pulse-soft 3s infinite ease-in-out; }
    </style>

    <form wire:submit="{{ $isConfigured ? 'activate' : 'saveConfig' }}" class="relative">
        <div class="max-w-4xl mb-12">
            {{ $this->form }}
        </div>

        <div style="margin-top: 28px; display: flex; flex-direction: column; align-items: flex-start; gap: 12px;">
            <x-filament::button 
                type="submit" 
                size="lg" 
                color="{{ $isConfigured ? 'warning' : 'primary' }}" 
                class="shadow-xl transition-all duration-300 rounded-xl px-8 py-3 {{ $isConfigured ? 'bg-amber-500 hover:bg-amber-400' : 'bg-primary-600 hover:bg-primary-500' }}"
            >
                <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 8px; white-space: nowrap;">
                    @if($isConfigured)
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <span style="font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; font-size: 13px;">Activate Core</span>
                    @else
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <span style="font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; font-size: 13px;">Save & Connect</span>
                    @endif
                </div>
            </x-filament::button>

            @if(\App\Models\Setting::get('license_status') === 'active')
                <x-filament::button 
                    color="gray" 
                    variant="link"
                    size="sm"
                    wire:click="deactivateLicense"
                    wire:confirm="Deactivation will restrict access to all modules. Are you sure?"
                    class="text-gray-400 hover:text-danger-600 transition-colors"
                >
                    Deactivate System
                </x-filament::button>
            @endif

            @if($isConfigured && \App\Models\Setting::get('license_status') !== 'active')
                <button type="button" wire:click="$set('isConfigured', false)" style="font-size: 11px; color: #9ca3af; text-decoration: underline; margin-top: 4px;">
                    Edit Connection Settings
                </button>
            @endif
        </div>
    </form>
</x-filament-panels::page>
