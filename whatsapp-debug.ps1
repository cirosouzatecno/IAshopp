#!/usr/bin/env powershell
# Script de Diagnóstico Automático - QRCode WhatsApp Web
# Uso: .\whatsapp-debug.ps1

$ErrorActionPreference = "Continue"

function Write-Header {
    param([string]$Text)
    Write-Host "`n" -NoNewline
    Write-Host "╔" + ("═" * ($Text.Length + 2)) + "╗" -ForegroundColor Cyan
    Write-Host "║ $Text ║" -ForegroundColor Cyan
    Write-Host "╚" + ("═" * ($Text.Length + 2)) + "╝" -ForegroundColor Cyan
}

function Write-Step {
    param([string]$Text, [string]$Color = "White")
    Write-Host "`n→ " -ForegroundColor Yellow -NoNewline
    Write-Host $Text -ForegroundColor $Color
}

function Write-Success {
    param([string]$Text)
    Write-Host "  ✓ $Text" -ForegroundColor Green
}

function Write-Error {
    param([string]$Text)
    Write-Host "  ✗ $Text" -ForegroundColor Red
}

function Write-Warning {
    param([string]$Text)
    Write-Host "  ⚠ $Text" -ForegroundColor Yellow
}

function Write-Info {
    param([string]$Text)
    Write-Host "  • $Text" -ForegroundColor Cyan
}

# ===== INÍCIO DO SCRIPT =====

Write-Header "WhatsApp Web QRCode - Diagnóstico Automático"

# NÍVEL 1: Verificações Básicas
Write-Header "NÍVEL 1: Infraestrutura"

# 1.1 Verificar Node.js na porta 3001
Write-Step "Verificando se Node.js está rodando na porta 3001..." "White"
$port3001 = Get-NetTCPConnection -LocalPort 3001 -ErrorAction SilentlyContinue
if ($port3001) {
    Write-Success "Porta 3001 está ABERTA"
    Write-Info "PID: $($port3001.OwningProcess)"
} else {
    Write-Error "Porta 3001 está FECHADA - Node.js não está rodando!"
}

# 1.2 Verificar Laravel na porta 8000
Write-Step "Verificando se Laravel está rodando na porta 8000..." "White"
$port8000 = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
if ($port8000) {
    Write-Success "Porta 8000 está ABERTA"
} else {
    Write-Warning "Porta 8000 está FECHADA"
}

# 1.3 Verificar .env no whatsapp-webjs
Write-Step "Verificando arquivo .env em whatsapp-webjs..." "White"
$envPath = "c:\xampp\htdocs\IAshopp\whatsapp-webjs\.env"
if (Test-Path $envPath) {
    Write-Success ".env existe"
    $envContent = Get-Content $envPath
    if ($envContent -match "PORT=3001") {
        Write-Success "PORT=3001 configurado"
    } else {
        Write-Warning "PORT não está 3001"
    }
} else {
    Write-Error ".env NÃO existe em whatsapp-webjs"
    Write-Info "Copiar .env.example para .env"
}

# 1.4 Verificar .env do Laravel
Write-Step "Verificando arquivo .env do Laravel..." "White"
$laravelEnv = "c:\xampp\htdocs\IAshopp\.env"
if (Test-Path $laravelEnv) {
    Write-Success ".env Laravel existe"
    $content = Get-Content $laravelEnv | Select-String "WHATSAPP"
    if ($content) {
        Write-Success "Configurações WhatsApp encontradas:"
        foreach ($line in $content) {
            Write-Info $line.Line
        }
    } else {
        Write-Error "Nenhuma configuração WHATSAPP encontrada"
    }
} else {
    Write-Error ".env NÃO existe no Laravel"
}

# NÍVEL 2: Testar Conectividade
Write-Header "NÍVEL 2: Conectividade"

function Test-Endpoint {
    param(
        [string]$Url,
        [string]$Description
    )
    Write-Step "Testando: $Description" "Cyan"
    Write-Info "URL: $Url"

    try {
        $response = Invoke-RestMethod -Uri $Url -Method Get -TimeoutSec 5
        Write-Success "Resposta 200 OK"
        return $response
    } catch [System.Net.Http.HttpRequestException] {
        Write-Error "Erro HTTP: $($_.Exception.Message)"
        return $null
    } catch [System.Net.WebException] {
        Write-Error "Erro de conexão: $($_.Exception.Message)"
        return $null
    } catch {
        Write-Error "Erro: $($_.Exception.Message)"
        return $null
    }
}

# 2.1 Teste GET /
$rootResponse = Test-Endpoint "http://127.0.0.1:3001/" "GET / (Root)"
if ($rootResponse) {
    Write-Info "Serviço: $($rootResponse.service)"
    Write-Info "Versão: $($rootResponse.version)"
    Write-Info "Status: $($rootResponse.status)"
}

# 2.2 Teste GET /status
$statusResponse = Test-Endpoint "http://127.0.0.1:3001/status" "GET /status"
if ($statusResponse) {
    Write-Info "Status atual: $($statusResponse.status)"
    if ($statusResponse.lastError) {
        Write-Warning "Último erro: $($statusResponse.lastError)"
    }
}

# 2.3 Teste GET /qr
$qrResponse = Test-Endpoint "http://127.0.0.1:3001/qr" "GET /qr"
if ($qrResponse) {
    Write-Info "QR Status: $($qrResponse.status)"
    if ($qrResponse.qrImage) {
        $qrLength = $qrResponse.qrImage.Length
        Write-Success "QRImage gerado! (tamanho: $qrLength bytes)"
    } else {
        Write-Warning "QRImage é NULL"
        if ($qrResponse.status -eq "ready") {
            Write-Info "Cliente já autenticado (status='ready')"
        } elseif ($qrResponse.status -eq "starting") {
            Write-Info "Cliente ainda inicializando"
        }
    }
}

# NÍVEL 3: Verificar Estrutura de Arquivos
Write-Header "NÍVEL 3: Estrutura de Arquivos"

Write-Step "Verificando arquivos críticos..." "White"

$files = @(
    @{Path = "c:\xampp\htdocs\IAshopp\whatsapp-webjs\index.js"; Desc = "Servidor Node.js" },
    @{Path = "c:\xampp\htdocs\IAshopp\whatsapp-webjs\package.json"; Desc = "Dependencies Node.js" },
    @{Path = "c:\xampp\htdocs\IAshopp\resources\views\admin\whatsapp-web\index.blade.php"; Desc = "Frontend Blade" },
    @{Path = "c:\xampp\htdocs\IAshopp\app\Http\Controllers\Admin\WhatsAppWebController.php"; Desc = "Controller Laravel" }
)

foreach ($file in $files) {
    if (Test-Path $file.Path) {
        Write-Success "$($file.Desc)"
    } else {
        Write-Error "$($file.Desc) - NÃO ENCONTRADO"
    }
}

# NÍVEL 4: Verificar node_modules
Write-Step "Verificando dependências Node.js..." "White"
$nodeModules = "c:\xampp\htdocs\IAshopp\whatsapp-webjs\node_modules"
if (Test-Path $nodeModules) {
    Write-Success "node_modules existe"
    $requiredPackages = @("express", "cors", "qrcode", "whatsapp-web.js", "axios")
    foreach ($pkg in $requiredPackages) {
        $pkgPath = Join-Path $nodeModules $pkg
        if (Test-Path $pkgPath) {
            Write-Info "✓ $pkg"
        } else {
            Write-Warning "✗ $pkg - não encontrado"
        }
    }
} else {
    Write-Warning "node_modules NÃO existe - executar: npm install"
}

# NÍVEL 5: Verificar Sessão WhatsApp
Write-Header "NÍVEL 5: Sessão de Autenticação"

Write-Step "Verificando pasta de sessão .wwebjs_auth..." "White"
$sessionPath = "c:\xampp\htdocs\IAshopp\whatsapp-webjs\.wwebjs_auth"
if (Test-Path $sessionPath) {
    $files = (Get-ChildItem -Path $sessionPath -Recurse).Count
    Write-Success "Pasta de sessão existe com $files arquivos"
    Write-Info "Sessão anterior será reutilizada se válida"
} else {
    Write-Info "Pasta de sessão não existe - será criada na primeira autenticação"
}

# RESUMO FINAL
Write-Header "RESUMO E PRÓXIMOS PASSOS"

Write-Host @"
📋 CHECKLIST:

1. [ ] Node.js rodando? (porta 3001)
2. [ ] Laravel rodando? (porta 8000)
3. [ ] .env em whatsapp-webjs configurado?
4. [ ] .env do Laravel configurado?
5. [ ] Endpoints Node.js respondem? (/, /status, /qr)
6. [ ] QRImage não é NULL? (se status = 'qr')
7. [ ] node_modules instalado?

🚀 PRÓXIMOS PASSOS:

Se Node.js não está rodando:
  cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
  npm install
  npm start

Se .env não existe:
  copy .env.example .env
  Editar variáveis conforme necessário

Se nenhum QR aparece:
  1. Limpar sessão: rmdir /s /q .wwebjs_auth
  2. Reiniciar Node.js
  3. Abrir DevTools (F12) e verificar Network tab

📖 Documentação:
  Abrir: WHATSAPP_QRCODE_DEBUG.md
"@ -ForegroundColor White

Write-Host "`n" -ForegroundColor White
