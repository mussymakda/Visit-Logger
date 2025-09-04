<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit" color="primary">
                Save Settings
            </x-filament::button>
        </div>
    </form>
    
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Information
        </x-slot>
        
        <x-slot name="description">
            Changes will take effect immediately after saving. Uploaded files are stored securely.
        </x-slot>
    </x-filament::section>
</x-filament-panels::page>
