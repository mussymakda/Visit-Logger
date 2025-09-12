<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Search Test</h1>
    <input type="text" id="searchInput" placeholder="Type to search..." />
    <div id="results"></div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value;
            const resultsDiv = document.getElementById('results');
            
            if (query.length < 2) {
                resultsDiv.innerHTML = '';
                return;
            }

            fetch(`{{ route('designer.search') }}?q=${encodeURIComponent(query)}`, {
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
                resultsDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                console.error('Search error:', error);
                resultsDiv.innerHTML = 'Error: ' + error.message;
            });
        });
    </script>
</body>
</html>
