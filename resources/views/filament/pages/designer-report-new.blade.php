<x-filament-panels::page>
    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ $summary['total_designers'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Designers</div>
            </div>
        </x-filament::section>
        
        <x-filament::section class="p-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ $summary['active_designers'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Active Designers</div>
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
                <div class="text-3xl font-bold text-warning-600">{{ number_format($summary['avg_visits'], 1) }}</div>
                <div class="text-sm text-gray-500 font-medium">Avg Visits/Designer</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Export Button --}}
    <div class="mb-6">
        <x-filament::button
            color="success"
            icon="heroicon-o-document-arrow-down"
            tag="a"
            :href="route('admin.reports.designer.export')"
        >
            Export Detailed Report
        </x-filament::button>
    </div>

    {{-- Designer Details --}}
    <div class="space-y-6">
        @forelse($designers as $designer)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $designer->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $designer->email }}</p>
                        </div>
                        <div class="flex gap-2">
                            <x-filament::badge color="primary" size="lg">
                                {{ $designer->total_visits }} Visits
                            </x-filament::badge>
                            <x-filament::badge color="success" size="lg">
                                {{ $designer->unique_sponsors_count }} Sponsors
                            </x-filament::badge>
                        </div>
                    </div>
                </x-slot>

                @if($designer->sponsors->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sponsor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Visits</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Visit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @foreach($designer->sponsors as $sponsor)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $sponsor->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ $sponsor->company_name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">{{ $sponsor->location }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <x-filament::badge color="primary">
                                                {{ $sponsor->visit_count }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $sponsor->first_visit ? \Carbon\Carbon::parse($sponsor->first_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $sponsor->last_visit ? \Carbon\Carbon::parse($sponsor->last_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-filament::icon icon="heroicon-o-building-office" class="h-12 w-12 text-gray-400 mx-auto mb-2"/>
                        <p class="text-gray-500">This designer hasn't visited any sponsors yet</p>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center py-8">
                    <x-filament::icon icon="heroicon-o-user-group" class="h-12 w-12 text-gray-400 mx-auto mb-2"/>
                    <p class="text-gray-500">No designers found</p>
                </div>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
