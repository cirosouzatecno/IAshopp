# Referência Rápida - WhatsApp Web QRCode

## 🔄 Fluxo de Funcionamento

```
┌─────────────────────────────────────────────────────────────────────┐
│                        NAVEGADOR (Frontend)                         │
│                                                                      │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ /admin/whatsapp-web                                         │   │
│  │                                                             │   │
│  │  JavaScript:                                              │   │
│  │  setInterval(() => {                                      │   │
│  │    fetch('/qr')  ─────────┐                               │   │
│  │  }, 5s)                    │                               │   │
│  └────────────────────────────┼───────────────────────────────┘   │
└─────────────────────────────────┼─────────────────────────────────┘
                                  │ HTTP GET
                                  │
                                  v
                    ┌─────────────────────────┐
                    │   LARAVEL CONTROLLER    │
                    │  WhatsAppWebController  │
                    │   (retorna baseUrl)     │
                    └─────────────────────────┘
                                  │
                                  v
                    ┌──────────────────────────────┐
                    │  NODE.JS (Port 3001)         │
                    │  ┌────────────────────────┐  │
                    │  │ GET /qr                │  │
                    │  │ return {               │  │
                    │  │   qrImage: base64,     │  │
                    │  │   status: 'qr'         │  │
                    │  │ }                      │  │
                    │  └────────────────────────┘  │
                    │                              │
                    │  ┌────────────────────────┐  │
                    │  │ whatsapp-web.js Event  │  │
                    │  │                        │  │
                    │  │ client.on('qr', async  │  │
                    │  │   qr => {              │  │
                    │  │   lastQrImage =        │  │
                    │  │    qrcode.toDataURL()  │  │
                    │  │ })                     │  │
                    │  └────────────────────────┘  │
                    └──────────────────────────────┘
                                  │
                                  v
                    Response com QRImage (base64)
                                  │
                                  v
                    ┌────────────────────────────────────┐
                    │  JavaScript renderiza:            │
                    │  <img src="data:image/png;base64"/ │
                    └────────────────────────────────────┘
```

## 🔴🟡🟢 Estados do Cliente

```
starting
   ↓
qr ← [Escanear QR com WhatsApp]
   ↓
authenticated
   ↓
ready ✓ [Sistema Funcional]
```

## 📋 Componentes

### 1. Frontend (Blade Template)
- **Arquivo:** `resources/views/admin/whatsapp-web/index.blade.php`
- **Função:** Exibir QRCode e status
- **Freq. Update:** A cada 5 segundos

### 2. Laravel Controller
- **Arquivo:** `app/Http/Controllers/Admin/WhatsAppWebController.php`
- **Função:** Passar baseUrl e provider para a view

### 3. Node.js Server
- **Arquivo:** `whatsapp-webjs/index.js`
- **Portas:** 3001
- **Função:** Gerenciar cliente WhatsApp e expor endpoints

### 4. Modelo Setting
- **Arquivo:** `app/Models/Setting.php`
- **Função:** Armazenar baseUrl em banco de dados

## 🎯 Checklist Funcionalidade

### ✓ Verificação 1: Infraestrutura
```bash
# Node.js rodando?
netstat -ano | findstr :3001

# Arquivo .env existe?
dir whatsapp-webjs\.env
```

### ✓ Verificação 2: Conectividade
```bash
# Endpoint / responde?
curl http://127.0.0.1:3001/

# Endpoint /qr responde?
curl http://127.0.0.1:3001/qr
```

### ✓ Verificação 3: Frontend
```
1. Abrir DevTools (F12)
2. Console tab - procurar por "[WhatsApp QR]"
3. Network tab - procurar GET requests para /qr e /status
```

### ✓ Verificação 4: QRImage
```
Em DevTools → Network:
1. Clicar em request /qr
2. Response tab
3. Verificar campo "qrImage"
```

## 🚨 Problemas Comuns

| Problema | Verificar | Solução |
|----------|-----------|---------|
| QR não aparece | Console do navegador (F12) | Ver WHATSAPP_QRCODE_DEBUG.md |
| "Serviço offline" | Node.js processo | `npm start` em whatsapp-webjs/ |
| CORS error | Headers CORS | app.use(cors()) no index.js |
| Timeout | Logs do Node.js | Limpar .wwebjs_auth e reiniciar |
| QRImage null | Status do cliente | Aguardar evento 'qr' ser disparado |

## 🔗 Rotas Relacionadas

| Rota | Método | Descrição |
|------|--------|-----------|
| `/admin/whatsapp-web` | GET | Página com QRCode |
| `/api/webhook/whatsapp-web` | POST | Recebe mensagens do Node.js |
| (Node.js) `/` | GET | Info do serviço |
| (Node.js) `/status` | GET | Status atual do cliente |
| (Node.js) `/qr` | GET | QRImage e QR data |
| (Node.js) `/send` | POST | Enviar mensagem de texto |
| (Node.js) `/send-image` | POST | Enviar imagem |

## 📊 Status HTTP

### 200 OK - Respostas esperadas

**GET /status**
```json
{
  "status": "qr|authenticated|ready|starting|...",
  "lastError": null,
  "lastErrorAt": null
}
```

**GET /qr**
```json
{
  "status": "qr",
  "qr": "2@...",
  "qrImage": "data:image/png;base64,iVBORw0K..."
}
```

### ❌ Erros

**Connection refused**
- Node.js não está rodando
- Porta 3001 não está ouvindo

**CORS error**
- Falta `app.use(cors())` no index.js
- Verify no navegador (DevTools → Network → Headers)

**404 Not Found**
- Endpoint incorreto
- Verifique URL base em Laravel

## 🎓 Conceitos Importantes

### QRCode
- Gerado quando cliente precisa se autenticar
- Válido por ~5 minutos
- Pode ser escaneado apenas uma vez

### Sessão (.wwebjs_auth)
- Pasta que armazena dados de autenticação
- Permite reutilizar sessão sem novo QR
- Deletar para forçar nova autenticação

### whatsapp-web.js
- Biblioteca que emula cliente WhatsApp Web
- Usa Puppeteer (Chrome headless)
- Eventos: qr, authenticated, ready, message, disconnected

### Base64 (QRImage)
- Formato data URI: `data:image/png;base64,...`
- Pode ser usado diretamente em `<img src="...">`
- Tamanho típico: 3KB-10KB

## 📞 Contato/Suporte

Para debug avançado:
1. Verificar **WHATSAPP_QRCODE_DEBUG.md**
2. Executar **whatsapp-debug.ps1**
3. Ver logs em **console do Node.js** (npm start)
4. Abrir **DevTools** (F12) no navegador
