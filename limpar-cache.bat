@echo off
REM Script para limpar cache do Laravel após configurar APP_KEY
REM Uso: limpar-cache.bat

echo.
echo ========================================================
echo     Laravel - Limpando Cache
echo ========================================================
echo.

php artisan config:clear
echo [OK] Config cache limpo

php artisan cache:clear
echo [OK] Application cache limpo

php artisan route:clear
echo [OK] Route cache limpo

php artisan view:clear
echo [OK] View cache limpo

echo.
echo ========================================================
echo [SUCCESS] Todos os caches foram limpos!
echo ========================================================
echo.
echo Agora você pode executar:
echo   php artisan serve
echo.
pause
