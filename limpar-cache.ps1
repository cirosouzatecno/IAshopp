# Script PowerShell para limpar cache do Laravel
# Uso: .\limpar-cache.ps1

Write-Host "`n════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "     Laravel - Limpando Cache" -ForegroundColor Cyan
Write-Host "════════════════════════════════════════════════════`n" -ForegroundColor Cyan

Write-Host "Limpando config cache..." -ForegroundColor Yellow
php artisan config:clear
Write-Host "✓ Config cache limpo`n" -ForegroundColor Green

Write-Host "Limpando application cache..." -ForegroundColor Yellow
php artisan cache:clear
Write-Host "✓ Application cache limpo`n" -ForegroundColor Green

Write-Host "Limpando route cache..." -ForegroundColor Yellow
php artisan route:clear
Write-Host "✓ Route cache limpo`n" -ForegroundColor Green

Write-Host "Limpando view cache..." -ForegroundColor Yellow
php artisan view:clear
Write-Host "✓ View cache limpo`n" -ForegroundColor Green

Write-Host "════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host "✓ Todos os caches foram limpos!" -ForegroundColor Green
Write-Host "════════════════════════════════════════════════════`n" -ForegroundColor Green

Write-Host "Agora você pode executar:" -ForegroundColor Cyan
Write-Host "  php artisan serve`n" -ForegroundColor White
