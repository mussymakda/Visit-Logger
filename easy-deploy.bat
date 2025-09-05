@echo off
echo 🚀 Building and preparing for deployment...

REM Build the assets
call npm run build

REM Create a deployment package
echo 📦 Creating deployment package...
if exist "deploy-package" rmdir /s /q "deploy-package"
mkdir "deploy-package"

REM Copy all files except node_modules and git
xcopy /e /i /h /exclude:exclude-files.txt . "deploy-package"

REM Copy built assets specifically
xcopy /e /i "public\build" "deploy-package\public\build\"

echo ✅ Deployment package ready in 'deploy-package' folder
echo 📤 Just upload the contents of 'deploy-package' to your server
echo.
echo Next steps:
echo 1. Upload all files from 'deploy-package' folder to your server
echo 2. Run the deploy.sh script on your server
echo.
pause
