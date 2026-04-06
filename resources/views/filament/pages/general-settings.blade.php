<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div style="margin-top: 10px; padding-top: 20px;" class="flex items-center justify-start pb-40">
            <x-filament::button type="submit" size="xl" class="px-16 shadow-2xl">
                <span class="font-black uppercase tracking-widest text-xs">Save Settings</span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
