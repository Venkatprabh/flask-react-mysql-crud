@echo off
echo Starting PHP API Server...
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo Error: PHP is not installed or not in PATH
    echo Download PHP from: https://www.php.net/downloads.php
    pause
    exit /b 1
)

REM Create logs directory if it doesn't exist
if not exist logs mkdir logs

echo PHP API Server starting on http://localhost:8080
echo Press Ctrl+C to stop
echo.

REM Start PHP built-in server
php -S localhost:8080 -t api/