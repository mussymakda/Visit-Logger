<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Basic Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Basic Information
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Name:</span> {{ $sponsor->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Company:</span> {{ $sponsor->company_name ?? 'N/A' }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Contact:</span> {{ $sponsor->contact ?? 'N/A' }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Location:</span> {{ $sponsor->location ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Visit Statistics --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Visit Statistics
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Total Visits:</span> {{ $sponsor->visits_count ?? $sponsor->visits()->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Recent Visits (30 days):</span> {{ $sponsor->visits()->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">This Month:</span> {{ $sponsor->visits()->whereMonth('created_at', now()->month)->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Last Visit:</span> 
                    @if($sponsor->visits()->latest()->first())
                        {{ $sponsor->visits()->latest()->first()->created_at->diffForHumans() }}
                    @else
                        No visits yet
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Registration Information --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Registration Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Registered:</span> {{ $sponsor->created_at->format('F j, Y') }}</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Time:</span> {{ $sponsor->created_at->format('g:i A') }}</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Days Active:</span> {{ $sponsor->created_at->diffInDays(now()) }} days</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Last Updated:</span> {{ $sponsor->updated_at->diffForHumans() }}</p>
        </div>
    </div>

    {{-- Recent Designers --}}
    @if($sponsor->visits()->with('user')->latest()->limit(5)->get()->isNotEmpty())
    <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-lg p-6 border border-orange-200 dark:border-orange-700">
        <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-100 mb-4 flex items-center gap-2">
            <x-heroicon-o-users class="w-5 h-5" />
            Recent Visiting Designers
        </h3>
        <div class="space-y-2">
            @foreach($sponsor->visits()->with('user')->latest()->limit(5)->get() as $visit)
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">{{ $visit->user->name }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $visit->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
