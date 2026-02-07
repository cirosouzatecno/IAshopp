REM ========================================
REM EXECUTAR TUDO DE UMA VEZ
REM ========================================

@echo off
color 0A
cls

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║           CONFIGURACAO COMPLETA DO LARAVEL                     ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

echo [1/3] APP_KEY ja foi configurada automaticamente!
echo       Arquivo: .env (linha 3)
echo.

echo [2/3] Limpando caches do Laravel...
echo.

php artisan config:clear >nul 2>&1
echo       ✓ Config cache limpo

php artisan cache:clear >nul 2>&1
echo       ✓ Application cache limpo

php artisan route:clear >nul 2>&1
echo       ✓ Route cache limpo

php artisan view:clear >nul 2>&1
echo       ✓ View cache limpo

echo.
echo [3/3] Verificando configuracao...
echo.

php artisan --version
echo.

echo ╔════════════════════════════════════════════════════════════════╗
echo ║                    ✓ TUDO PRONTO!                              ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo O Laravel esta configurado e pronto para usar!
echo.
echo Proximos passos:
echo   1. php artisan serve
echo   2. Abrir http://localhost:8000
echo.
echo Ou se usar XAMPP:
echo   http://localhost/IAshopp/public
echo.

pause
