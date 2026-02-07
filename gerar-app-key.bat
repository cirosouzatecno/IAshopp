@echo off
REM Script para gerar APP_KEY do Laravel
REM Uso: gerar-app-key.bat

setlocal enabledelayedexpansion

title Laravel - Gerar APP_KEY
color 0A

cls
echo.
echo ================================================================================
echo                   Laravel - Geracao de APP_KEY
echo ================================================================================
echo.

REM Verificar se estamos no diretório correto
if not exist "artisan" (
    color 0C
    echo [ERROR] Arquivo artisan nao encontrado!
    echo.
    echo Execute este script a partir de: c:\xampp\htdocs\IAshopp
    echo.
    pause
    exit /b 1
)

REM Verificar se .env existe
if not exist ".env" (
    color 0E
    echo [WARNING] Arquivo .env nao encontrado!
    echo.
    echo Copiando de .env.example...
    copy .env.example .env
    if errorlevel 1 (
        color 0C
        echo [ERROR] Erro ao copiar .env.example!
        pause
        exit /b 1
    )
    echo [OK] .env criado com sucesso
    echo.
)

REM Gerar APP_KEY
color 0A
echo.
echo ================================================================================
echo [OK] Gerando APP_KEY...
echo ================================================================================
echo.

php artisan key:generate

if errorlevel 1 (
    color 0C
    echo.
    echo [ERROR] Erro ao gerar APP_KEY!
    echo.
    echo Verifique se:
    echo   1. PHP esta instalado e no PATH
    echo   2. Arquivo .env existe
    echo   3. Laravel esta configurado corretamente
    echo.
    pause
    exit /b 1
)

echo.
echo ================================================================================
echo [SUCCESS] APP_KEY gerada com sucesso!
echo ================================================================================
echo.
echo A chave foi adicionada automaticamente ao arquivo .env
echo Voce pode agora executar o projeto Laravel.
echo.
echo Proximos passos:
echo   1. php artisan serve (para iniciar o servidor)
echo   2. Acessar http://localhost:8000
echo.
pause

endlocal
