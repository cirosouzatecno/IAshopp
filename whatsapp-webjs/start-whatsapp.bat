@echo off
REM Script para iniciar o serviço WhatsApp Web.js no Windows
REM Uso: Duplo clique ou: start-whatsapp.bat

setlocal enabledelayedexpansion

title WhatsApp Web.js Service
color 0A

cls
echo.
echo ================================================================================
echo                   WhatsApp Web.js - Serviço de Integração
echo ================================================================================
echo.

REM Verificar se estamos no diretório correto
if not exist "index.js" (
    color 0C
    echo [ERROR] Arquivo index.js nao encontrado!
    echo.
    echo Execute este script a partir de: c:\xampp\htdocs\IAshopp\whatsapp-webjs
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
    echo [OK] .env criado com sucesso
    echo.
    echo Verifique e configure as variaveis em: .env
    echo.
    pause
)

REM Verificar se node_modules existe
if not exist "node_modules" (
    color 0E
    echo [WARNING] Dependencias nao instaladas!
    echo.
    echo Instalando via npm...
    call npm install
    if errorlevel 1 (
        color 0C
        echo [ERROR] Erro ao instalar dependencias!
        pause
        exit /b 1
    )
    echo.
)

REM Iniciar serviço
color 0A
echo.
echo ================================================================================
echo [OK] Iniciando serviço WhatsApp Web.js...
echo ================================================================================
echo.
echo Base URL: http://127.0.0.1:3001
echo QR Code: http://127.0.0.1:3001/qr
echo Status: http://127.0.0.1:3001/status
echo.
echo Pressione CTRL+C para parar o serviço
echo.

call npm start

REM Se npm start retornar erro
if errorlevel 1 (
    color 0C
    echo.
    echo [ERROR] Erro ao iniciar o serviço!
    echo.
    pause
    exit /b 1
)

endlocal
