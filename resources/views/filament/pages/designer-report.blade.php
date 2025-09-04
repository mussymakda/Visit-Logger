<x-filament-panels::page>
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-primary-600">{{ $summary['total_designers'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Designers</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-success-600">{{ $summary['active_designers'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Active Designers</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-info-600">{{ $summary['total_visits'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Visits</div>
        </x-filament::section>

        <x-filament::section class="text-center">
            <div class="text-2xl font-bold text-warning-600">{{ number_format($summary['avg_visits'], 1) }}</div>
            <div class="text-sm text-gray-500 mt-1">Avg Visits/Designer</div>
        </x-filament::section>
    </div>

    <!-- Export Button -->
    <div class="mb-6 flex justify-end">
        <x-filament::button
            color="success"
            icon="heroicon-o-document-arrow-down"
            tag="a"
            :href="route('admin.reports.designer.export')"
        >
            Export Excel
        </x-filament::button>
    </div>

    <!-- Designers List -->
    <div class="space-y-6">
        @forelse($designers as $designer)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $designer->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $designer->email }}</p>
                        </div>
                        <div class="flex gap-4 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-primary-600">{{ $designer->total_visits }}</div>
                                <div class="text-gray-500">Total Visits</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-success-600">{{ $designer->unique_sponsors_count }}</div>
                                <div class="text-gray-500">Sponsors</div>
                            </div>
                        </div>
                    </div>
                </x-slot>

                @if($designer->sponsors->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sponsor Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Visit Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">First Visit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Visit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($designer->sponsors as $sponsor)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $sponsor->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sponsor->company_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sponsor->contact }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <x-filament::badge color="primary" size="sm">
                                                {{ $sponsor->visit_count }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sponsor->first_visit ? \Carbon\Carbon::parse($sponsor->first_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $sponsor->last_visit ? \Carbon\Carbon::parse($sponsor->last_visit)->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-sm">This designer hasn't visited any sponsors yet</div>
                    </div>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section>
                <div class="text-center py-8 text-gray-500">
                    <div class="text-lg font-medium mb-2">No designers found</div>
                    <div class="text-sm">Create some interior designers to see reports</div>
                </div>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
