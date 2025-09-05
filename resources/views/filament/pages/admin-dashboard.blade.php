<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Welcome Section --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome to Admin Dashboard</h1>
                    <p class="text-blue-100 text-lg">Monitor and manage your Visit Logger platform</p>
                </div>
                <div class="hidden md:block">
                    <x-heroicon-o-chart-bar class="w-16 h-16 text-blue-200" />
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-4">
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-sm text-blue-100">Today's Date</span>
                    <p class="font-semibold">{{ now()->format('F j, Y') }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-sm text-blue-100">Platform Status</span>
                    <p class="font-semibold text-green-300">🟢 Online</p>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Manage Users --}}
            <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Manage Users</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Add, edit, and manage user accounts</p>
                    <a href="{{ route('filament.admin.resources.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Manage Users
                    </a>
                </div>
            </div>

            {{-- Manage Sponsors --}}
            <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-building-office class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Manage Sponsors</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Add and manage sponsor information</p>
                    <a href="{{ route('filament.admin.resources.sponsors.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Manage Sponsors
                    </a>
                </div>
            </div>

            {{-- View Reports --}}
            <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-chart-bar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">View Reports</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Access comprehensive analytics</p>
                    <a href="{{ \App\Filament\Pages\Reports::getUrl() }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        View Reports
                    </a>
                </div>
            </div>

            {{-- Settings --}}
            <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Settings</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Configure platform settings</p>
                    <a href="{{ \App\Filament\Pages\Settings::getUrl() }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Settings
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Activity Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <x-heroicon-o-clock class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                Recent Platform Activity
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Today's Visits --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Today's Visits</h3>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Visit::whereDate('created_at', today())->count() }}</p>
                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ \App\Models\Visit::whereDate('created_at', today())->distinct('user_id')->count() }} unique designers</p>
                </div>

                {{-- This Week --}}
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                    <h3 class="font-semibold text-green-900 dark:text-green-100 mb-2">This Week</h3>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Models\Visit::where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                    <p class="text-sm text-green-700 dark:text-green-300">visits recorded</p>
                </div>

                {{-- New Registrations --}}
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                    <h3 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">New This Month</h3>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ \App\Models\User::whereMonth('created_at', now()->month)->count() }}</p>
                    <p class="text-sm text-purple-700 dark:text-purple-300">new registrations</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
