<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Designer Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Interior Designer
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Name:</span> {{ $visit->user->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Email:</span> {{ $visit->user->email }}</p>
            </div>
        </div>

        {{-- Sponsor Information --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
                Sponsor Details
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Name:</span> {{ $visit->sponsor->name }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Company:</span> {{ $visit->sponsor->company_name ?? 'N/A' }}</p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Location:</span> {{ $visit->sponsor->location ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Visit Details --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Visit Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Date:</span> {{ $visit->created_at->format('F j, Y') }}</p>
            <p class="text-gray-700 dark:text-gray-300"><span class="font-medium">Time:</span> {{ $visit->created_at->format('g:i A') }}</p>
        </div>
    </div>

    {{-- Visit Photo --}}
    @if($visit->photo)
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3">
            Visit Photo
        </h3>
        <div class="flex justify-center">
            <img src="{{ asset('storage/' . $visit->photo) }}" 
                 alt="Visit Photo" 
                 class="max-w-full max-h-64 rounded-lg border border-gray-200 dark:border-gray-600">
        </div>
    </div>
    @endif
</div>
