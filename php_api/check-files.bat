@echo off
echo Checking PHP API files...
echo.

if exist api\index.php (
    echo ✓ api/index.php - OK
) else (
    echo ✗ api/index.php - Missing
)

if exist api\config\database.php (
    echo ✓ api/config/database.php - OK
) else (
    echo ✗ api/config/database.php - Missing
)

if exist api\v1\users.php (
    echo ✓ api/v1/users.php - OK
) else (
    echo ✗ api/v1/users.php - Missing
)

if exist api\v1\products.php (
    echo ✓ api/v1/products.php - OK
) else (
    echo ✗ api/v1/products.php - Missing
)

if exist .env (
    echo ✓ .env - OK
) else (
    echo ✗ .env - Missing
)

echo.
echo All files should be present for the API to work properly.
pause