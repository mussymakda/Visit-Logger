<x-filament-panels::page>
    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ $summary['total_sponsors'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Sponsors</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ $summary['active_sponsors'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Active Sponsors</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-info-600">{{ $summary['total_visits'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Visits</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ $summary['avg_visits'] }}</div>
                <div class="text-sm text-gray-500 font-medium">Avg Visits/Sponsor</div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Sponsors</h3>
            <x-filament::button
                color="success"
                icon="heroicon-o-document-arrow-down"
                tag="a"
                :href="route('admin.reports.sponsor.export')"
                size="sm"
            >
                Export Excel
            </x-filament::button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Sponsor Details
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Total Visits
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Unique Designers
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Last Visit
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                    @forelse($sponsors as $sponsor)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $sponsor->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $sponsor->company_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $sponsor->contact }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $sponsor->location }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-filament::badge color="primary" size="md">
                                    {{ $sponsor->total_visits }}
                                </x-filament::badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-filament::badge color="success" size="md">
                                    {{ $sponsor->unique_designers_count }}
                                </x-filament::badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $sponsor->last_visit ? \Carbon\Carbon::parse($sponsor->last_visit)->format('M j, Y g:i A') : 'No visits yet' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-o-building-office" class="h-12 w-12 mx-auto mb-2 text-gray-400"/>
                                    <p>No sponsors found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
