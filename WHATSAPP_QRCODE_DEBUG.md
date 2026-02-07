# Guia de Diagnóstico: QRCode WhatsApp Web não aparece

## 🔍 Visão Geral do Fluxo

```
Blade Frontend (PHP)
    ↓
fetch(GET /qr) → Node.js Port 3001
    ↓
client.on('qr') → qrcode.toDataURL()
    ↓
lastQrImage (base64) → JSON Response
    ↓
<img src="data:image/png;base64,..." />
```

## ⚠️ Checklist de Diagnóstico (Por Prioridade)

### NÍVEL 1: Infraestrutura Básica

#### 1.1 Node.js está rodando na porta 3001?

**Windows CMD:**
```cmd
netstat -ano | findstr :3001
```

**Resultado esperado:**
```
  TCP    127.0.0.1:3001         0.0.0.0:0              LISTENING       12345
```

**Se VAZIO:** Serviço não está rodando!

#### 1.2 Verificar se arquivos `.env` existem

```cmd
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
dir .env
```

Se não existe, copiar de `.env.example`:
```cmd
copy .env.example .env
```

#### 1.3 Variáveis de ambiente no `.env` do whatsapp-webjs

```cmd
type .env
```

Deve conter:
```
PORT=3001
LARAVEL_WEBHOOK_URL=http://127.0.0.1:8000/webhook/whatsapp-web
WEBHOOK_TOKEN=
SESSION_PATH=.wwebjs_auth
```

#### 1.4 Variáveis de ambiente no `.env` do Laravel

```cmd
cd c:\xampp\htdocs\IAshopp
findstr "WHATSAPP" .env
```

Deve conter:
```
WHATSAPP_PROVIDER=webjs
WHATSAPP_WEBJS_BASE_URL=http://127.0.0.1:3001
```

---

### NÍVEL 2: Testar Conectividade

#### 2.1 Teste simples com curl

```cmd
curl http://127.0.0.1:3001/
```

**Resultado esperado:**
```json
{
  "service": "WhatsApp Web.js API",
  "version": "1.0.0",
  "status": "starting|qr|authenticated|ready|...",
  ...
}
```

**Se erro `ECONNREFUSED`:** Node.js não está rodando → VÁ PARA SEÇÃO "INICIAR NODE.JS"

#### 2.2 Teste do endpoint `/status`

```cmd
curl http://127.0.0.1:3001/status
```

**Resultado esperado:**
```json
{
  "status": "starting|qr|authenticated|ready|disconnected|...",
  "lastError": null,
  "lastErrorAt": null
}
```

#### 2.3 Teste do endpoint `/qr`

```cmd
curl http://127.0.0.1:3001/qr
```

**Resultado esperado** (quando esperando autenticação):
```json
{
  "status": "qr",
  "qr": "2@...",
  "qrImage": "data:image/png;base64,iVBORw0KGgo..."
}
```

**Resultado possível** (já autenticado):
```json
{
  "status": "ready",
  "qr": null,
  "qrImage": null
}
```

---

### NÍVEL 3: Diagnóstico no Navegador

#### 3.1 Acessar a página de QRCode

1. Ir para: `http://127.0.0.1/IAshopp/public/admin/whatsapp-web`
2. Abrir DevTools: `F12`
3. Ir para aba **Network**
4. Observar as requisições:
   - `GET http://127.0.0.1:3001/status` 
   - `GET http://127.0.0.1:3001/qr`

#### 3.2 Verificar Erros

**Console (F12 → Console):**

- **CORS Error?** → Problema no servidor Node.js
- **404 Not Found?** → Endpoint não existe
- **Connection refused?** → Node.js offline
- **Timeout?** → Node.js lento/travado

#### 3.3 Inspecionar Response

No DevTools → Network:
1. Clique em um dos requests (`/status` ou `/qr`)
2. Aba **Response**
3. Verifique se:
   - Status HTTP é 200
   - Contém `qrImage` com valor válido
   - Sem erros de parsing JSON

---

### NÍVEL 4: Análise de Logs do Node.js

#### 4.1 Iniciar Node.js com Logs Detalhados

Editar `whatsapp-webjs/index.js` e adicionar logs após linha 28:

```javascript
// Adicionar após const client = new Client({

client.on('qr', async (qr) => {
  console.log('[QR EVENT] QR recebido:', qr.substring(0, 50) + '...');
  lastQr = qr;
  status = 'qr';
  try {
    lastQrImage = await qrcode.toDataURL(qr);
    console.log('[QR EVENT] QRImage gerado com sucesso');
  } catch (err) {
    console.error('[QR EVENT] Erro ao gerar QRImage:', err.message);
    recordError(err);
  }
});

client.on('authenticated', () => {
  console.log('[AUTH] Cliente autenticado');
  status = 'authenticated';
  lastError = null;
  lastErrorAt = null;
});

client.on('ready', () => {
  console.log('[READY] Cliente pronto para usar');
  status = 'ready';
  lastQr = null;
  lastQrImage = null;
  lastError = null;
  lastErrorAt = null;
});

client.on('auth_failure', (msg) => {
  console.error('[AUTH_FAIL] Falha na autenticação:', msg);
  status = 'auth_failure';
  lastError = msg;
  lastErrorAt = new Date().toISOString();
});

client.on('disconnected', (reason) => {
  console.error('[DISCONNECT] Cliente desconectado:', reason);
  status = 'disconnected';
  lastError = reason;
  lastErrorAt = new Date().toISOString();
});
```

#### 4.2 Executar e Monitorar

```cmd
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
npm start
```

**Observe os logs ao iniciar. Esperado:**
```
WhatsApp Web service running on port 3001
[QR EVENT] QR recebido: 2@...
[QR EVENT] QRImage gerado com sucesso
```

---

### NÍVEL 5: Verificar Estado da Sessão

#### 5.1 Pasta de Autenticação

```cmd
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
dir .wwebjs_auth
```

Se a pasta existe e contém arquivos → Sessão prévia foi armazenada

#### 5.2 Limpar Sessão (Para força nova autenticação)

```cmd
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
rmdir /s /q .wwebjs_auth
```

Depois reinicie Node.js → deve gerar novo QRCode

---

## 🚀 Erros Comuns e Soluções

| Erro | Causa | Solução |
|------|-------|---------|
| `ECONNREFUSED` | Node.js offline | Iniciar Node.js com `npm start` |
| `CORS error` | Configuração CORS | Verificar `app.use(cors())` em index.js |
| `404 Not Found` | Endpoint errado | Verificar URL base e rota |
| `qrImage: null` | QR não foi gerado | Aguardar evento `qr` ser disparado |
| `status: "starting"` | Cliente inicializando | Aguardar (pode levar 10-30s) |
| `status: "auth_failure"` | Autenticação falhou | Limpar `.wwebjs_auth` e tentar novamente |
| `Puppeteer error` | Navegador não funciona | Ajustar args do Puppeteer |

---

## 🔧 Script de Teste Rápido (PowerShell)

Salvar como `test-whatsapp.ps1`:

```powershell
# Função para testar endpoints
function Test-WhatsAppEndpoint {
    param(
        [string]$Endpoint,
        [string]$BaseUrl = "http://127.0.0.1:3001"
    )
    
    Write-Host "Testando: $BaseUrl$Endpoint" -ForegroundColor Cyan
    try {
        $response = Invoke-RestMethod -Uri "$BaseUrl$Endpoint" -Method Get
        Write-Host "✓ Status: 200 OK" -ForegroundColor Green
        Write-Host $response | ConvertTo-Json -Depth 2
    } catch {
        Write-Host "✗ Erro: $($_.Exception.Message)" -ForegroundColor Red
    }
    Write-Host ""
}

# Executar testes
Write-Host "=== WhatsApp Web QRCode Debug ===" -ForegroundColor Yellow
Write-Host ""

Test-WhatsAppEndpoint "/"
Test-WhatsAppEndpoint "/status"
Test-WhatsAppEndpoint "/qr"

Write-Host "=== Fim do Teste ===" -ForegroundColor Yellow
```

Executar:
```powershell
.\test-whatsapp.ps1
```

---

## 📋 Checklist Final

- [ ] Portas 3001 e 8000 estão livres e ouvindo
- [ ] `.env` existe em `whatsapp-webjs/` com valores corretos
- [ ] `.env` do Laravel tem `WHATSAPP_PROVIDER=webjs`
- [ ] Node.js inicia sem erros (`npm start`)
- [ ] Endpoints respond corretamente (`curl`)
- [ ] Nenhum erro CORS no navegador (DevTools)
- [ ] Página carrega dados via fetch (Network tab)
- [ ] QRImage é um data URI válido
- [ ] QRCode imagem aparece na página

---

## 🔗 Documentos Relacionados

- `whatsapp-webjs/index.js` - Servidor Node.js
- `resources/views/admin/whatsapp-web/index.blade.php` - Frontend
- `app/Http/Controllers/Admin/WhatsAppWebController.php` - Controller Laravel
