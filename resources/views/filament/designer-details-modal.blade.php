<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Designer Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Designer Profile
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Name:</span> {{ $designer->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Email:</span> {{ $designer->email }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Role:</span> Interior Designer</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Account Status:</span> 
                    @if($designer->email_verified_at)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                            Verified
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">
                            Unverified
                        </span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Activity Statistics --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Activity Statistics
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Total Visits:</span> {{ $designer->visits_count ?? $designer->visits()->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">This Month:</span> {{ $designer->visits()->whereMonth('created_at', now()->month)->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Last 30 Days:</span> {{ $designer->visits()->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">This Week:</span> {{ $designer->visits()->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Timeline Information --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Timeline Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Joined:</span> {{ $designer->created_at->format('F j, Y') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $designer->created_at->diffForHumans() }}</p>
            </div>
            <div>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">First Visit:</span> 
                    @if($designer->visits()->oldest()->first())
                        {{ $designer->visits()->oldest()->first()->created_at->format('M j, Y') }}
                    @else
                        No visits yet
                    @endif
                </p>
            </div>
            <div>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Last Visit:</span> 
                    @if($designer->visits()->latest()->first())
                        {{ $designer->visits()->latest()->first()->created_at->diffForHumans() }}
                    @else
                        No visits yet
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Recent Visits --}}
    @if($designer->visits()->with('sponsor')->latest()->limit(5)->get()->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Recent Visits
        </h3>
        <div class="space-y-2">
            @foreach($designer->visits()->with('sponsor')->latest()->limit(5)->get() as $visit)
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $visit->sponsor->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $visit->sponsor->company_name ?? 'No company' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $visit->created_at->format('M j, Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $visit->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Performance Insights --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Performance Insights
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @php
                $totalVisits = $designer->visits()->count();
                $avgVisitsPerMonth = $designer->created_at->diffInMonths(now()) > 0 ? 
                    round($totalVisits / max(1, $designer->created_at->diffInMonths(now())), 1) : $totalVisits;
                $uniqueSponsors = $designer->visits()->distinct('sponsor_id')->count();
            @endphp
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Avg Visits/Month:</span> {{ $avgVisitsPerMonth }}</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Unique Sponsors:</span> {{ $uniqueSponsors }}</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Activity Level:</span> 
                @if($totalVisits >= 20)
                    <span class="text-green-600 font-medium">Very Active</span>
                @elseif($totalVisits >= 10)
                    <span class="text-blue-600 font-medium">Active</span>
                @elseif($totalVisits >= 5)
                    <span class="text-yellow-600 font-medium">Moderate</span>
                @elseif($totalVisits >= 1)
                    <span class="text-gray-600 font-medium">Low Activity</span>
                @else
                    <span class="text-red-600 font-medium">Inactive</span>
                @endif
            </p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Platform Tenure:</span> {{ $designer->created_at->diffInDays(now()) }} days</p>
        </div>
    </div>
</div>
