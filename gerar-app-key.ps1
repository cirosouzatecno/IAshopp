# Script PowerShell para Gerar APP_KEY do Laravel
# Uso: .\gerar-app-key.ps1

$ErrorActionPreference = "Stop"

Write-Host "`n" -NoNewline
Write-Host "╔════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     Laravel - Geração de APP_KEY                  ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Verificar se está no diretório correto
if (-not (Test-Path "artisan")) {
    Write-Host "✗ Arquivo artisan não encontrado!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Execute este script a partir de: c:\xampp\htdocs\IAshopp" -ForegroundColor Yellow
    Write-Host ""
    pause
    exit 1
}

Write-Host "✓ Diretório correto encontrado" -ForegroundColor Green

# Verificar se .env existe
if (-not (Test-Path ".env")) {
    Write-Host "⚠ Arquivo .env não encontrado" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Copiando de .env.example..." -ForegroundColor Cyan

    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "✓ .env criado com sucesso" -ForegroundColor Green
    } else {
        Write-Host "✗ .env.example não encontrado!" -ForegroundColor Red
        Write-Host ""
        pause
        exit 1
    }
} else {
    Write-Host "✓ Arquivo .env encontrado" -ForegroundColor Green
}

# Verificar se APP_KEY está vazia
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=\s*$" -or $envContent -match "APP_KEY=$") {
    Write-Host "⚠ APP_KEY está vazia, gerando..." -ForegroundColor Yellow
} else {
    Write-Host "⚠ APP_KEY já existe, será regenerada" -ForegroundColor Yellow
    Write-Host ""
    $confirm = Read-Host "Deseja continuar? (S/N)"
    if ($confirm -ne "S" -and $confirm -ne "s") {
        Write-Host "Operação cancelada." -ForegroundColor Yellow
        exit 0
    }
}

Write-Host ""
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "Gerando APP_KEY..." -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Gerar APP_KEY
try {
    $output = php artisan key:generate 2>&1
    Write-Host $output

    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "════════════════════════════════════════════════════" -ForegroundColor Green
        Write-Host "✓ APP_KEY gerada com sucesso!" -ForegroundColor Green
        Write-Host "════════════════════════════════════════════════════" -ForegroundColor Green
        Write-Host ""

        # Ler e mostrar a chave gerada
        $envContent = Get-Content ".env"
        $appKeyLine = $envContent | Select-String "APP_KEY="
        if ($appKeyLine) {
            Write-Host "Chave gerada:" -ForegroundColor Cyan
            Write-Host $appKeyLine -ForegroundColor White
        }

        Write-Host ""
        Write-Host "Próximos passos:" -ForegroundColor Cyan
        Write-Host "  1. php artisan serve (para iniciar o servidor)" -ForegroundColor White
        Write-Host "  2. Acessar http://localhost:8000" -ForegroundColor White
        Write-Host ""

        # Perguntar se quer iniciar o servidor
        $startServer = Read-Host "Deseja iniciar o servidor Laravel agora? (S/N)"
        if ($startServer -eq "S" -or $startServer -eq "s") {
            Write-Host ""
            Write-Host "Iniciando servidor Laravel..." -ForegroundColor Cyan
            Write-Host "Pressione Ctrl+C para parar" -ForegroundColor Yellow
            Write-Host ""
            php artisan serve
        }
    } else {
        throw "Erro ao executar php artisan key:generate"
    }
} catch {
    Write-Host ""
    Write-Host "✗ Erro ao gerar APP_KEY!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Verifique se:" -ForegroundColor Yellow
    Write-Host "  1. PHP está instalado e no PATH" -ForegroundColor White
    Write-Host "  2. Arquivo .env existe e tem permissões de escrita" -ForegroundColor White
    Write-Host "  3. Laravel está configurado corretamente" -ForegroundColor White
    Write-Host ""
    Write-Host "Erro: $_" -ForegroundColor Red
    Write-Host ""
    pause
    exit 1
}
