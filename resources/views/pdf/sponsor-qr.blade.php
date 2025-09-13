<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $sponsor->name }} - QR Code</title>
    <style>
        @page {
            margin: 5mm;
            size: 100mm 156mm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 6px;
        }
        
        .logo {
            max-height: 25px;
            margin-bottom: 4px;
        }
        
        .app-name {
            font-size: 12px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }
        
        .main-content {
            margin-bottom: 8px;
            text-align: center;
        }
        
        .sponsor-info {
            text-align: center;
            margin-bottom: 8px;
        }
        
        .qr-section {
            text-align: center;
            margin-bottom: 8px;
        }
        
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 2px;
        }
        
        .sponsor-name {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 4px;
        }
        
        .sponsor-company {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .qr-code {
            width: 90mm;
            height: 90mm;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin: 0 auto 8px auto;
            display: block;
        }
        
        .qr-text {
            font-size: 7px;
            color: #6b7280;
            word-break: break-all;
            margin-bottom: 6px;
        }
        
        .instructions {
            background-color: #f3f4f6;
            padding: 6px;
            border-radius: 4px;
            border-left: 2px solid #2563eb;
            text-align: left;
            margin: 0 auto;
        }
        
        .instructions h3 {
            margin: 0 0 4px 0;
            color: #1e40af;
            font-size: 9px;
        }
        
        .instructions ol {
            margin: 0;
            padding-left: 12px;
        }
        
        .instructions li {
            margin-bottom: 3px;
            color: #374151;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = null;
            
            // First priority: Sponsor logo
            if($sponsor->logo && file_exists(public_path('storage/' . $sponsor->logo))) {
                $logoPath = public_path('storage/' . $sponsor->logo);
            }
            // Second priority: App logo
            elseif($settings->app_logo && file_exists(public_path('storage/' . $settings->app_logo))) {
                $logoPath = public_path('storage/' . $settings->app_logo);
            }
        @endphp
        
        @if($logoPath)
            <img src="{{ $logoPath }}" alt="Logo" class="logo">
        @endif
        <div class="app-name">{{ $settings->app_name ?? 'Visit Logger' }}</div>
    </div>

    <div class="main-content">
        <div class="sponsor-info">
            @if($sponsor->company_name)
                <div class="sponsor-company">{{ $sponsor->company_name }}</div>
            @endif
            <div class="sponsor-name">{{ $sponsor->name }}</div>
        </div>

        <div class="qr-section">
            <img src="{{ $sponsor->qr_code_path }}" alt="QR Code" class="qr-code">
        </div>
    </div>

    <div class="instructions">
        <h3>For Interior Designers</h3>
        <ol>
            <li><strong>Scan this QR code</strong> with your mobile device camera</li>
            <li><strong>Take a selfie</strong> to verify your visit</li>
            <li><strong>Submit your visit</strong> - that's it!</li>
        </ol>
    </div>
</body>
</html>
