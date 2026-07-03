<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3 justify-start">
            <x-filament::button type="submit" color="warning">
                Guardar Configuración
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
