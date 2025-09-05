# Quick Deploy Checklist

When you make changes, only upload these files/folders:

## Always Upload After npm run build:
- public/build/ (entire folder)

## Upload When You Change Code:
- resources/views/filament/designer/pages/dashboard.blade.php
- app/Http/Controllers/Api/ApiController.php
- app/Models/Sponsor.php
- app/Filament/ (any files you changed)

## Upload When You Change Routes:
- routes/api.php
- routes/web.php

## Upload When You Change Database:
- database/migrations/ (new files only)

## After Upload, Run on Server:
```bash
php artisan view:clear
php artisan cache:clear
```

That's it! No need to upload everything every time.
