<x-filament-panels::page>
    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ $visits->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Visits</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ $visits->unique('user_id')->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Unique Designers</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-info-600">{{ $visits->unique('sponsor_id')->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Unique Sponsors</div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ $visits->where('created_at', '>=', today())->count() }}</div>
                <div class="text-sm text-gray-500 font-medium">Today's Visits</div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-900 dark:border-gray-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Visit Records</h3>
            <x-filament::button
                color="success"
                icon="heroicon-o-document-arrow-down"
                tag="a"
                :href="route('admin.reports.visits.export')"
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
                            Visit Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Designer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Sponsor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Company & Location
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">{{ $visit->created_at->format('M j, Y') }}</div>
                                <div class="text-gray-500 text-xs">{{ $visit->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $visit->user->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $visit->user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $visit->sponsor->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $visit->sponsor->company_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $visit->sponsor->location }}</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-o-clipboard-document-list" class="h-12 w-12 mx-auto mb-2 text-gray-400"/>
                                    <p>No visits found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
