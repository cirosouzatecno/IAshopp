# Script de Teste de Integração - WhatsApp QRCode

Este arquivo contém testes que você pode executar para verificar cada componente.

## 1️⃣ Teste de Conectividade Básica

### Teste 1.1: Node.js está ouvindo na porta 3001?

**Windows CMD:**
```cmd
netstat -ano | findstr :3001
```

**Esperado:**
```
TCP    127.0.0.1:3001         0.0.0.0:0              LISTENING       12345
```

---

## 2️⃣ Testes de Endpoints REST

### Teste 2.1: Endpoint GET /

```powershell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:3001/" -Method Get
$response | ConvertTo-Json
```

**Esperado:**
```json
{
  "service": "WhatsApp Web.js API",
  "version": "1.0.0",
  "status": "starting|qr|ready|...",
  "endpoints": { ... }
}
```

### Teste 2.2: Endpoint GET /status

```powershell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:3001/status" -Method Get
$response | ConvertTo-Json
```

**Esperado:**
```json
{
  "status": "starting|qr|authenticated|ready|disconnected|...",
  "lastError": null,
  "lastErrorAt": null
}
```

### Teste 2.3: Endpoint GET /qr (quando esperando QR)

```powershell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:3001/qr" -Method Get
if ($response.qrImage) {
    Write-Host "✓ QRImage disponível!"
    Write-Host "Tamanho: $($response.qrImage.Length) bytes"
} else {
    Write-Host "✗ QRImage é null"
    Write-Host "Status atual: $($response.status)"
}
```

**Esperado (quando status='qr'):**
```json
{
  "status": "qr",
  "qr": "2@...",
  "qrImage": "data:image/png;base64,iVBORw0K..."
}
```

---

## 3️⃣ Teste de Frontend

### Teste 3.1: Verificar se página carrega

```
URL: http://localhost/IAshopp/public/admin/whatsapp-web
Status: Deve carregar sem erros 404
```

### Teste 3.2: Abrir DevTools e verificar Console

1. Pressionar `F12`
2. Ir em **Console**
3. Procurar por linhas como:

```
[WhatsApp QR] Iniciando... { baseUrl: "http://127.0.0.1:3001" }
[fetchStatus] Status recebido: { status: "qr", lastError: null, lastErrorAt: null }
[fetchQr] Resposta: { status: "qr", hasQrImage: true, qrImageSize: 4521 }
[fetchQr] ✓ Renderizando QRImage
```

### Teste 3.3: Verificar Network Requests

1. Em DevTools, ir em **Network**
2. Recarregar página
3. Procurar por requests:
   - `GET http://127.0.0.1:3001/status` - Status 200 ✓
   - `GET http://127.0.0.1:3001/qr` - Status 200 ✓

---

## 4️⃣ Teste de QRImage

### Teste 4.1: Verificar se qrImage é Base64 válido

```powershell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:3001/qr" -Method Get

if ($response.qrImage -match "^data:image/png;base64,") {
    Write-Host "✓ QRImage é um data URI válido"
    $dataLength = $response.qrImage.Length
    Write-Host "Tamanho: $dataLength bytes"
    if ($dataLength -gt 1000 -and $dataLength -lt 50000) {
        Write-Host "✓ Tamanho razoável para um QRCode"
    } else {
        Write-Host "⚠ Tamanho suspeito"
    }
} else {
    Write-Host "✗ QRImage não é data URI válido"
    Write-Host "Valor: $($response.qrImage)"
}
```

---

## 5️⃣ Teste de Fluxo Completo

### Teste 5.1: Verificar Fluxo Inteiro

Executar nesta ordem:

```powershell
# 1. Verificar se Node.js está rodando
Write-Host "1. Verificando Node.js..."
$port = Get-NetTCPConnection -LocalPort 3001 -ErrorAction SilentlyContinue
if ($port) {
    Write-Host "✓ Node.js está rodando"
} else {
    Write-Host "✗ Node.js não está rodando"
    exit
}

# 2. Teste GET /status
Write-Host "`n2. Testando GET /status..."
try {
    $status = Invoke-RestMethod -Uri "http://127.0.0.1:3001/status" -Method Get
    Write-Host "✓ Status: $($status.status)"
} catch {
    Write-Host "✗ Erro: $($_.Exception.Message)"
    exit
}

# 3. Teste GET /qr
Write-Host "`n3. Testando GET /qr..."
try {
    $qr = Invoke-RestMethod -Uri "http://127.0.0.1:3001/qr" -Method Get
    if ($qr.qrImage) {
        Write-Host "✓ QRImage disponível ($(($qr.qrImage.Length / 1024).ToString('F2')) KB)"
    } else {
        Write-Host "⚠ QRImage é null (status: $($qr.status))"
    }
} catch {
    Write-Host "✗ Erro: $($_.Exception.Message)"
    exit
}

# 4. Teste Frontend
Write-Host "`n4. Teste Frontend manual:"
Write-Host "Abrir: http://localhost/IAshopp/public/admin/whatsapp-web"
Write-Host "Pressionar F12 e verificar Console"
Write-Host "✓ Tudo OK!"
```

---

## 6️⃣ Teste de Erro Esperado (POST /send)

### Teste 6.1: Tentar enviar mensagem antes de autenticar

```powershell
$payload = @{
    to = "+5511999999999"
    text = "Teste"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod `
        -Uri "http://127.0.0.1:3001/send" `
        -Method Post `
        -Body $payload `
        -ContentType "application/json"
} catch {
    Write-Host "Erro esperado (cliente não autenticado): $($_.Exception.Message)"
}
```

---

## 7️⃣ Teste de Autenticação Completa

### Teste 7.1: Monitorar Fluxo de Autenticação

1. **Terminal 1:** Iniciar Node.js
   ```bash
   npm start
   ```
   Procurar por logs de transição de estado:
   ```
   starting → qr → authenticated → ready
   ```

2. **Terminal 2:** Monitorar status a cada segundo
   ```powershell
   while ($true) {
       $status = (Invoke-RestMethod -Uri "http://127.0.0.1:3001/status").status
       Write-Host "$(Get-Date -Format 'HH:mm:ss') - Status: $status"
       Start-Sleep -Seconds 1
   }
   ```

3. **Navegador:** Abrir página e escanear QR
   ```
   http://localhost/IAshopp/public/admin/whatsapp-web
   ```

4. **Resultado esperado:**
   ```
   [Terminal 1]
   [QR EVENT] QR recebido...
   [QR EVENT] ✓ QRImage gerado com sucesso!
   [AUTH] ✓ Cliente autenticado com sucesso
   [READY] ✓ Cliente pronto para usar!
   
   [Terminal 2]
   12:34:56 - Status: qr
   12:34:57 - Status: qr
   12:35:01 - Status: authenticated
   12:35:02 - Status: ready
   ```

---

## 🎯 Checklist de Testes

- [ ] Teste 1.1: Porta 3001 respondendo
- [ ] Teste 2.1: GET / retorna info
- [ ] Teste 2.2: GET /status retorna status
- [ ] Teste 2.3: GET /qr retorna qrImage
- [ ] Teste 3.1: Página carrega
- [ ] Teste 3.2: Console shows [WhatsApp QR] logs
- [ ] Teste 3.3: Network shows GET requests com Status 200
- [ ] Teste 4.1: QRImage é data URI válido
- [ ] Teste 5.1: Fluxo completo OK
- [ ] Teste 7.1: Autenticação muda de estado

---

## 📊 Estados Esperados por Teste

| Teste | Status esperado |
|-------|-----------------|
| Teste 2.2 antes de QR | `status: "starting"` |
| Teste 2.2 durante QR | `status: "qr"` |
| Teste 2.3 durante QR | `qrImage: "data:image/png..."` |
| Teste 2.3 autenticado | `qrImage: null, status: "ready"` |
| Teste 4.1 | data URI com 1KB-10KB |

---

## 🚨 Erros Comuns nos Testes

| Erro | Causa | Solução |
|------|-------|---------|
| `ECONNREFUSED` | Node.js offline | npm start |
| `CORS policy blocked` | CORS não habilitado | Verificar app.use(cors()) |
| `404 Not Found` | Endpoint errado | Ver rota em index.js |
| `qrImage: null` | QR ainda não gerado | Aguardar evento 'qr' |
| `Timeout` | Servidor lento | Aumentar timeout ou reiniciar |

---

## 💾 Salvar Resultados dos Testes

Para debug futuro, copiar saída dos testes:

```powershell
# Salvar resultado do teste em arquivo
$result = @"
Data: $(Get-Date)
Node.js: $(if (Get-NetTCPConnection -LocalPort 3001 -EA 0) { "OK" } else { "FAIL" })
Status: $($(Invoke-RestMethod http://127.0.0.1:3001/status -EA 0).status)
QRImage size: $($(Invoke-RestMethod http://127.0.0.1:3001/qr -EA 0).qrImage.Length)
"@

$result | Out-File -FilePath "test-results-$(Get-Date -Format 'yyyyMMdd-HHmmss').txt"
Write-Host "Resultado salvo em test-results-*.txt"
```

---

## ℹ️ Referência de Estados

```
starting (0-30s)
  └─> qr (esperando escanear)
      └─> authenticated (QR escaneado)
          └─> ready (pronto para usar) ✓
          
  Ou se erro:
      └─> auth_failure / disconnected / error
```

---

## 📞 Se Teste Falhar

1. Procurar nome do teste neste arquivo
2. Verificar "Erro Esperado Tipo X"
3. Seguir "Solução" indicada
4. Reexecutar teste
5. Se persistir, ver WHATSAPP_QRCODE_DEBUG.md
