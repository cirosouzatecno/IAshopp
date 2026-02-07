# WhatsApp Web.js - Guia Rápido de Início

## 🚀 Início Rápido

### 1. Configurar Ambiente

#### Laravel (.env)
```bash
cd c:\xampp\htdocs\IAshopp
copy .env.example .env
```

Adicionar/verificar as linhas:
```env
WHATSAPP_PROVIDER=webjs
WHATSAPP_WEBJS_BASE_URL=http://127.0.0.1:3001
```

#### Serviço WhatsApp Node.js
```bash
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
copy .env.example .env
npm install
```

### 2. Iniciar Serviços

#### Terminal 1: Node.js (WhatsApp Web.js)
```bash
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
npm start
```

**Esperado na saída:**
```
[SERVER] ✓ WhatsApp Web service rodando em http://127.0.0.1:3001
[INIT] Iniciando cliente WhatsApp Web.js...
[QR EVENT] QR recebido...
[QR EVENT] ✓ QRImage gerado com sucesso!
```

#### Terminal 2: Laravel (se usando dev server)
```bash
cd c:\xampp\htdocs\IAshopp
php artisan serve
```

Ou via XAMPP (http://localhost/IAshopp/public)

### 3. Acessar QRCode

Abrir no navegador:
```
http://localhost/IAshopp/public/admin/whatsapp-web
```

**O que você vai ver:**
- Status: "qr" (esperando autenticação)
- QRCode renderizado como imagem
- Mensagem: "Escaneie com o WhatsApp no celular para conectar"

### 4. Autenticar WhatsApp

1. Abrir WhatsApp no celular
2. Ir em: **Configurações > Aparelhos Conectados > Conectar um aparelho**
3. Escanear o QRCode na tela

**Após autenticar:**
- Status mudará para: "authenticated" → "ready"
- QRCode desaparecerá
- Página exibirá: "Conectado"

---

## 🔧 Troubleshooting

### ❌ QRCode não aparece

#### Passo 1: Verificar se Node.js está rodando
```powershell
# PowerShell (recomendado)
.\whatsapp-debug.ps1
```

#### Passo 2: Verificar logs do Node.js
Procure por uma destas linhas na saída do `npm start`:

**✓ Correto:**
```
[QR EVENT] QR recebido...
[QR EVENT] ✓ QRImage gerado com sucesso!
```

**✗ Problema:**
```
[QR EVENT] ✗ Erro ao gerar QRImage: ...
```

#### Passo 3: Limpar sessão e reiniciar
```bash
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
rmdir /s /q .wwebjs_auth
npm start
```

#### Passo 4: Abrir DevTools e verificar
1. Abrir http://localhost/IAshopp/public/admin/whatsapp-web
2. Pressionar `F12` (DevTools)
3. Aba **Console** - procurar por:
   - `[fetchQr] Resposta:` com `hasQrImage: true`
   - Erros tipo `CORS` ou `Connection refused`

### ❌ "Serviço offline"

**Solução 1: Node.js não iniciado**
```bash
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
npm start
```

**Solução 2: node_modules não instalado**
```bash
cd c:\xampp\htdocs\IAshopp\whatsapp-webjs
npm install
npm start
```

**Solução 3: Porta 3001 já em uso**
```powershell
# Encontrar processo na porta 3001
Get-NetTCPConnection -LocalPort 3001

# Matar processo (se PID for 12345)
Stop-Process -Id 12345 -Force

# Reiniciar Node.js
npm start
```

### ❌ "Cliente ainda inicializando"

Status: `starting` por mais de 30 segundos

**Causa possível:** Puppeteer (navegador headless) falhando

**Solução:**
```bash
# Limpar cache do Puppeteer
rmdir /s /q "%APPDATA%\Local\Chromium"

# Limpar sessão WhatsApp
rmdir /s /q .wwebjs_auth

# Reiniciar
npm start
```

### ❌ Erro "ECONNREFUSED"

Não consegue conectar em `http://127.0.0.1:3001`

**Verificar:**
1. Node.js está rodando? `netstat -ano | findstr :3001`
2. `WHATSAPP_WEBJS_BASE_URL` está correto?
3. Firewall não está bloqueando?

---

## 📊 Estados Possíveis

| Status | Descrição | Ação |
|--------|-----------|------|
| `starting` | Iniciando cliente | Aguardar 10-30s |
| `qr` | Esperando autenticação | **Escanear QR com WhatsApp** |
| `authenticated` | Autenticado | Aguardar estado `ready` |
| `ready` | Pronto para usar | ✓ Sistema funcional |
| `disconnected` | Desconectado | Verificar logs de erro |
| `auth_failure` | Falha de autenticação | Limpar `.wwebjs_auth` e tentar novamente |
| `offline` | Serviço não responde | Reiniciar Node.js |

---

## 📝 Logs Úteis

### Ver logs em tempo real
```bash
npm start
```

### Procurar por erros
```bash
# Procurar linhas com ✗ (erro)
npm start | findstr /C:"✗"
```

### Exemplo de log correto
```
[INIT] Iniciando cliente WhatsApp Web.js...
[INIT] SESSION_PATH: .wwebjs_auth
[INIT] ✓ Cliente inicializado com sucesso
[QR EVENT] QR recebido, tamanho: 145 bytes
[QR EVENT] Iniciando geração de QRImage via qrcode.toDataURL()...
[QR EVENT] ✓ QRImage gerado com sucesso! Tamanho: 4521 bytes
[SERVER] ✓ WhatsApp Web service rodando em http://127.0.0.1:3001
```

---

## 🔍 Documentação Completa

Para diagnóstico avançado, ver: [WHATSAPP_QRCODE_DEBUG.md](./WHATSAPP_QRCODE_DEBUG.md)

---

## ✅ Checklist de Deploy

- [ ] `.env` em `whatsapp-webjs/` configurado
- [ ] `.env` do Laravel com `WHATSAPP_PROVIDER=webjs`
- [ ] `npm install` executado em `whatsapp-webjs/`
- [ ] Node.js rodando: `npm start`
- [ ] Laravel acessível
- [ ] QRCode aparecendo na página
- [ ] WhatsApp autenticado
- [ ] Mensagens recebidas aparecem no sistema

---

## 📞 Suporte

Se problema persistir:
1. Executar script de debug: `.\whatsapp-debug.ps1`
2. Verificar arquivo de log completo: `WHATSAPP_QRCODE_DEBUG.md`
3. Procurar por mensagens `[ERROR]` ou `[FAIL]` nos logs do Node.js
4. Limpar tudo e recomeçar do zero (remover `node_modules`, `.wwebjs_auth`, `package-lock.json`)
