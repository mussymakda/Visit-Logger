@echo off
echo Starting Laravel development server for emulator access...
echo Server will be accessible at http://0.0.0.0:8000
echo For Android emulator, use: http://10.0.2.2:8000
echo For iOS simulator, use: http://YOUR_LOCAL_IP:8000
echo.
php artisan serve --host=0.0.0.0 --port=8000