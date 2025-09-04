<!DOCTYPE html>
<html>
<head>
    <title>QR Test Codes</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; }
        .qr-item { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .qr-code { text-align: center; margin: 20px 0; }
        .qr-data { font-family: monospace; background: #f0f0f0; padding: 10px; border-radius: 4px; margin: 10px 0; }
        img { max-width: 200px; height: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 QR Test Codes for Development</h1>
        <p>Use these QR codes to test the scanner functionality:</p>
        
        @foreach($sponsors as $sponsor)
        <div class="qr-item">
            <h3>{{ $sponsor->name }} (ID: {{ $sponsor->id }})</h3>
            
            <div class="qr-data">
                <strong>QR Data:</strong> sponsor={{ $sponsor->id }}
            </div>
            
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=sponsor={{ $sponsor->id }}" alt="QR Code for {{ $sponsor->name }}">
            </div>
            
            <p><strong>Company:</strong> {{ $sponsor->company_name ?? 'N/A' }}</p>
            <p><strong>Location:</strong> {{ $sponsor->location ?? 'N/A' }}</p>
            <p><strong>Contact:</strong> {{ $sponsor->contact_person ?? 'N/A' }}</p>
            
            <div style="margin-top: 15px; padding: 10px; background: #e3f2fd; border-radius: 4px;">
                <strong>Manual Test:</strong> Enter "{{ $sponsor->id }}" or "sponsor={{ $sponsor->id }}" in the scanner
            </div>
        </div>
        @endforeach
        
        <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px;">
            <h3>📱 Testing Instructions</h3>
            <ol>
                <li>Go to <a href="{{ url('/designer') }}" target="_blank">{{ url('/designer') }}</a></li>
                <li>Scan any QR code above with your camera</li>
                <li>Or manually type the sponsor ID in the input field</li>
                <li>Verify the sponsor info appears correctly</li>
                <li>Take a photo and submit the visit</li>
                <li>Check visit history at <a href="{{ url('/designer/visit-history') }}" target="_blank">{{ url('/designer/visit-history') }}</a></li>
            </ol>
        </div>
    </div>
</body>
</html>
