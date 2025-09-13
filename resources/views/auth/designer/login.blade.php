<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Login - {{ $settings->app_name ?? config('app.name') }}</title>
    @if($settings && $settings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            @if($settings && $settings->app_logo)
                <div class="mx-auto h-16 w-16 flex items-center justify-center">
                    <img src="{{ asset('storage/' . $settings->app_logo) }}" alt="{{ $settings->app_name }}" class="h-16 w-auto">
                </div>
            @else
                <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                </div>
            @endif
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Designer Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Search your name and enter your password
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('designer.login') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Your Name or Email</label>
                    <div class="mt-1 relative">
                        <input 
                            id="search" 
                            name="search" 
                            type="text" 
                            required 
                            value="{{ old('search') }}"
                            autocomplete="off"
                            class="appearance-none relative block w-full px-3 py-3 pl-10 border @error('search') border-red-300 @else border-gray-300 @enderror placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Type your name or email..."
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        
                        <!-- Search suggestions dropdown -->
                        <div id="searchSuggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden">
                            <!-- Suggestions will be populated here -->
                        </div>
                    </div>
                    @error('search')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="appearance-none relative block w-full px-3 py-3 pl-10 border @error('password') border-red-300 @else border-gray-300 @enderror placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="Enter your password"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            checked
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Sign in
                </button>
            </div>

            <div class="text-center space-y-3">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('designer.register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Register here
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing search...');
            
            const searchInput = document.getElementById('search');
            const suggestionsDiv = document.getElementById('searchSuggestions');
            let debounceTimer;

            console.log('Search input element:', searchInput);
            console.log('Suggestions div element:', suggestionsDiv);

            searchInput.addEventListener('input', function() {
                console.log('Input event triggered, value:', this.value);
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 2) {
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                console.log('Making search request for:', query);
                debounceTimer = setTimeout(() => {
                    const url = `{{ route('designer.search') }}?q=${encodeURIComponent(query)}`;
                    console.log('Fetching from URL:', url);
                    
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search results:', data);
                        suggestionsDiv.innerHTML = '';
                        
                        if (data.length === 0) {
                            suggestionsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No designers found</div>';
                        } else {
                            data.forEach(designer => {
                                const item = document.createElement('div');
                                item.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                                item.innerHTML = `
                                    <div class="font-medium text-gray-900">${designer.name}</div>
                                    <div class="text-sm text-gray-500">${designer.email}</div>
                                `;
                                item.addEventListener('click', () => {
                                    searchInput.value = designer.name;
                                    suggestionsDiv.classList.add('hidden');
                                });
                                suggestionsDiv.appendChild(item);
                            });
                        }
                        
                        suggestionsDiv.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        suggestionsDiv.classList.add('hidden');
                    });
                }, 300);
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });

            // Show suggestions when focusing on search input
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 2 && suggestionsDiv.children.length > 0) {
                    suggestionsDiv.classList.remove('hidden');
                }
            });
        });
    </script>
</body>
</html>
