Write-Host "Starting Laravel development server for emulator access..." -ForegroundColor Green
Write-Host "Server will be accessible at http://0.0.0.0:8000" -ForegroundColor Yellow
Write-Host "For Android emulator, use: http://10.0.2.2:8000" -ForegroundColor Cyan
Write-Host "For iOS simulator, use: http://YOUR_LOCAL_IP:8000" -ForegroundColor Cyan
Write-Host ""

# Get local IP address for reference
$localIP = (Get-NetIPConfiguration | Where-Object { $_.IPv4DefaultGateway -ne $null -and $_.NetAdapter.Status -ne "Disconnected" }).IPv4Address.IPAddress
Write-Host "Your local IP address: $localIP" -ForegroundColor Magenta
Write-Host "For iOS simulator, use: http://${localIP}:8000" -ForegroundColor Cyan
Write-Host ""

php artisan serve --host=0.0.0.0 --port=8000