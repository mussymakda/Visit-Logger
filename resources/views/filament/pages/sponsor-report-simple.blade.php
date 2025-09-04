<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Sponsor Reports
        </x-slot>
        
        <x-slot name="headerEnd">
            <x-filament::button
                color="success"
                icon="heroicon-o-document-arrow-down"
                tag="a"
                :href="route('admin.reports.sponsor.export')"
            >
                Download All
            </x-filament::button>
        </x-slot>

        <div class="grid gap-4">
            @forelse($sponsors as $sponsor)
                <div class="border rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
                     x-data="{ open: false }"
                     @click="open = !open">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $sponsor->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $sponsor->company_name }} • {{ $sponsor->location }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-filament::badge color="primary">
                                {{ $sponsor->total_visits }} visits
                            </x-filament::badge>
                            <x-filament::badge color="success">
                                {{ $sponsor->unique_designers_count }} designers
                            </x-filament::badge>
                            <x-filament::icon 
                                icon="heroicon-s-chevron-down" 
                                class="h-5 w-5 transition-transform"
                                x-bind:class="{ 'rotate-180': open }"
                            />
                        </div>
                    </div>

                    <div x-show="open" x-transition class="mt-4 border-t pt-4">
                        @if($sponsor->designers->count() > 0)
                            <div class="space-y-2">
                                @foreach($sponsor->designers as $designer)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <div>
                                            <span class="font-medium">{{ $designer->name }}</span>
                                            <span class="text-sm text-gray-500 ml-2">{{ $designer->email }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <x-filament::badge size="sm">{{ $designer->visit_count }} visits</x-filament::badge>
                                            <span class="text-gray-500 ml-2">
                                                Last: {{ $designer->last_visit ? \Carbon\Carbon::parse($designer->last_visit)->format('M j') : 'Never' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No visitors yet</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No sponsors found</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-panels::page>
