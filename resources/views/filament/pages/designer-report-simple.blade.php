<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Designer Reports
        </x-slot>
        
        <x-slot name="headerEnd">
            <x-filament::button
                color="success"
                icon="heroicon-o-document-arrow-down"
                tag="a"
                :href="route('admin.reports.designer.export')"
            >
                Download All
            </x-filament::button>
        </x-slot>

        <div class="grid gap-4">
            @forelse($designers as $designer)
                <div class="border rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
                     x-data="{ open: false }"
                     @click="open = !open">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $designer->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $designer->email }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="primary">
                                {{ $designer->total_visits }} visits
                            </x-filament::badge>
                            <x-filament::badge color="success">
                                {{ $designer->unique_sponsors_count }} sponsors
                            </x-filament::badge>
                            <x-filament::icon 
                                icon="heroicon-s-chevron-down" 
                                class="h-5 w-5 transition-transform"
                                x-bind:class="{ 'rotate-180': open }"
                            />
                        </div>
                    </div>

                    <div x-show="open" x-transition class="mt-4 border-t pt-4">
                        @if($designer->sponsors->count() > 0)
                            <div class="space-y-2">
                                @foreach($designer->sponsors as $sponsor)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <div>
                                            <span class="font-medium">{{ $sponsor->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">{{ $sponsor->company_name }} • {{ $sponsor->location }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <x-filament::badge size="sm">{{ $sponsor->visit_count }} visits</x-filament::badge>
                                            <span class="text-gray-500 ml-2">
                                                Last: {{ $sponsor->last_visit ? \Carbon\Carbon::parse($sponsor->last_visit)->format('M j') : 'Never' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No sponsor visits yet</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No designers found</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
