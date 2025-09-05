<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Application Settings
            </x-slot>
            
            <x-slot name="description">
                Customize your application's appearance and branding.
            </x-slot>
            
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>
