<x-filament-panels::page>
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-primary-600">{{ $summary['total_sponsors'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Sponsors</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-success-600">{{ $summary['active_sponsors'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Active Sponsors</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-info-600">{{ $summary['total_visits'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Visits</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-warning-600">{{ number_format($summary['avg_visits'], 1) }}</div>
            <div class="text-sm text-gray-500 mt-1">Avg Visits/Sponsor</div>
        </x-filament::section>
    </div>

    <!-- Export Button -->
    <div class="mb-6 flex justify-end">
        <x-filament::button
            color="success"
            icon="heroicon-o-document-arrow-down"
            tag="a"
            :href="route('admin.reports.sponsor.export')"
        >
            Export Excel
        </x-filament::button>
    </div>

    <!-- Sponsors List -->
    <div class="space-y-6">
        @forelse($sponsors as $sponsor)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $sponsor->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $sponsor->company_name }} • {{ $sponsor->location }}</p>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-primary-600">{{ $sponsor->total_visits }}</div>
                                <div class="text-gray-500">Total Visits</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-success-600">{{ $sponsor->unique_designers_count }}</div>
                                <div class="text-gray-500">Designers</div>
                            </div>
                        </div>
                    </div>
                </x-slot>

                @if($sponsor->designers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Visit Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">First Visit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Visit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sponsor->designers as $designer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $designer->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $designer->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <x-filament::badge color="primary" size="sm">
                                                {{ $designer->visit_count }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $designer->first_visit ? \Carbon\Carbon::parse($designer->first_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $designer->last_visit ? \Carbon\Carbon::parse($designer->last_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-sm">No designers have visited this sponsor yet</div>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center py-8 text-gray-500">
                    <div class="text-lg font-medium mb-2">No sponsors found</div>
                    <div class="text-sm">Create some sponsors to see reports</div>
                </div>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
