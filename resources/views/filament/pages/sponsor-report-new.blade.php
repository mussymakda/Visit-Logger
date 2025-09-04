<x-filament-panels::page>
    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ $summary['total_sponsors'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Sponsors</div>
            </div>
        </x-filament::section>
        
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ $summary['active_sponsors'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Active Sponsors</div>
            </div>
        </x-filament::section>
        
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-info-600">{{ $summary['total_visits'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Visits</div>
            </div>
        </x-filament::section>
        
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ $summary['avg_visits'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Avg Visits/Sponsor</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Export Button --}}
    <div class="mb-6">
        <x-filament::button
            color="success"
            icon="heroicon-o-document-arrow-down"
            tag="a"
            :href="route('admin.reports.sponsor.export')"
        >
            Export Detailed Report
        </x-filament::button>
    </div>

    {{-- Sponsor Details --}}
    <div class="space-y-6">
        @forelse($sponsors as $sponsor)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $sponsor->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $sponsor->company_name }} • {{ $sponsor->location }}</p>
                        </div>
                        <div class="flex gap-2">
                            <x-filament::badge color="primary" size="lg">
                                {{ $sponsor->total_visits }} Visits
                            </x-filament::badge>
                            <x-filament::badge color="success" size="lg">
                                {{ $sponsor->unique_designers_count }} Designers
                            </x-filament::badge>
                        </div>
                    </div>
                </x-slot>

                @if($sponsor->designers->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designer</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Visits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Visit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @foreach($sponsor->designers as $designer)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $designer->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $designer->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <x-filament::badge color="primary">
                                                {{ $designer->visit_count }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $designer->first_visit ? \Carbon\Carbon::parse($designer->first_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $designer->last_visit ? \Carbon\Carbon::parse($designer->last_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-filament::icon icon="heroicon-o-users" class="h-12 w-12 text-gray-400 mx-auto mb-2"/>
                        <p class="text-gray-500">No designers have visited this sponsor yet</p>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center py-8">
                    <x-filament::icon icon="heroicon-o-building-office" class="h-12 w-12 text-gray-400 mx-auto mb-2"/>
                    <p class="text-gray-500">No sponsors found</p>
                </div>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
