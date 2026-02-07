require('dotenv').config();

const express = require('express');
const cors = require('cors');
const axios = require('axios');
const qrcode = require('qrcode');
const { Client, LocalAuth, MessageMedia, Buttons } = require('whatsapp-web.js');

const app = express();
app.use(cors());
app.use(express.json({ limit: '5mb' }));

const PORT = process.env.PORT || 3001;
const LARAVEL_WEBHOOK_URL = process.env.LARAVEL_WEBHOOK_URL || 'http://127.0.0.1:8000/webhook/whatsapp-web';
const WEBHOOK_TOKEN = process.env.WEBHOOK_TOKEN || '';

let lastQr = null;
let lastQrImage = null;
let status = 'starting';
let lastError = null;
let lastErrorAt = null;

function recordError(err, nextStatus = 'error') {
  if (!err) return;
  lastError = err.message || String(err);
  lastErrorAt = new Date().toISOString();
  status = nextStatus;
}

const client = new Client({
  authStrategy: new LocalAuth({
    dataPath: process.env.SESSION_PATH || '.wwebjs_auth',
  }),
  puppeteer: {
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  },
});

client.on('qr', async (qr) => {
  console.log('[QR EVENT] QR recebido, tamanho:', qr.length, 'bytes');
  lastQr = qr;
  status = 'qr';
  try {
    console.log('[QR EVENT] Iniciando geração de QRImage via qrcode.toDataURL()...');
    lastQrImage = await qrcode.toDataURL(qr);
    console.log('[QR EVENT] ✓ QRImage gerado com sucesso! Tamanho:', lastQrImage.length, 'bytes');
  } catch (err) {
    console.error('[QR EVENT] ✗ Erro ao gerar QRImage:', err.message);
    recordError(err);
  }
});

client.on('authenticated', () => {
  console.log('[AUTH] ✓ Cliente autenticado com sucesso');
  status = 'authenticated';
  lastError = null;
  lastErrorAt = null;
});

client.on('ready', () => {
  console.log('[READY] ✓ Cliente pronto para usar!');
  status = 'ready';
  lastQr = null;
  lastQrImage = null;
  lastError = null;
  lastErrorAt = null;
});

client.on('auth_failure', (msg) => {
  console.error('[AUTH_FAIL] ✗ Falha na autenticação:', msg);
  status = 'auth_failure';
  lastError = msg;
  lastErrorAt = new Date().toISOString();
});

client.on('disconnected', (reason) => {
  console.error('[DISCONNECT] ✗ Cliente desconectado:', reason);
  status = 'disconnected';
  lastError = reason;
  lastErrorAt = new Date().toISOString();
});

client.on('message', async (message) => {
  if (!message.from || message.fromMe) {
    return;
  }

  try {
    const contact = await message.getContact();
    const fromValue = message.from?.endsWith('@c.us')
      ? cleanNumber(message.from)
      : message.from;

    const payload = {
      from: fromValue,
      text: message.body || '',
      name: contact?.pushname || contact?.name || null,
      raw: {
        id: message.id?.id,
        from: message.from,
        body: message.body,
        timestamp: message.timestamp,
      },
    };

    console.log('[MESSAGE] Recebida mensagem de:', cleanNumber(message.from), '-', message.body?.substring(0, 50));
    const response = await axios.post(LARAVEL_WEBHOOK_URL, payload, {
      headers: WEBHOOK_TOKEN ? { 'X-Webhook-Token': WEBHOOK_TOKEN } : undefined,
    });
    console.log('[MESSAGE] ✓ Mensagem enviada para webhook:', LARAVEL_WEBHOOK_URL);
  } catch (err) {
    console.error('[MESSAGE] ✗ Erro ao processar mensagem:', err.message);
    recordError(err);
  }
});

app.get('/', (req, res) => {
  res.json({
    service: 'WhatsApp Web.js API',
    version: '1.0.0',
    status,
    endpoints: {
      status: 'GET /status - Check connection status',
      qr: 'GET /qr - Get QR code for authentication',
      send: 'POST /send - Send text message',
      sendImage: 'POST /send-image - Send image message',
      sendButtons: 'POST /send-buttons - Send buttons message',
    },
    docs: {
      send: { to: 'phone_number', text: 'message' },
      sendImage: { to: 'phone_number', image_url: 'url', caption: 'optional' },
      sendButtons: { to: 'phone_number', body: 'message', buttons: '[{id,title}]' },
    },
  });
});

app.get('/status', (req, res) => {
  res.json({
    status,
    lastError,
    lastErrorAt,
  });
});

app.get('/qr', (req, res) => {
  res.json({
    status,
    qr: lastQr,
    qrImage: lastQrImage,
  });
});

app.post('/send', async (req, res) => {
  const { to, text } = req.body || {};
  if (!to || !text) {
    return res.status(422).json({ error: 'to and text are required' });
  }

  try {
    const chatId = normalizeNumber(to);
    const msg = await client.sendMessage(chatId, text);
    return res.json({ ok: true, message_id: msg.id?.id });
  } catch (err) {
    recordError(err);
    return res.status(500).json({ error: err.message });
  }
});

app.post('/send-image', async (req, res) => {
  const { to, image_url, caption } = req.body || {};
  if (!to || !image_url) {
    return res.status(422).json({ error: 'to and image_url are required' });
  }

  try {
    const chatId = normalizeNumber(to);
    const media = await fetchMedia(image_url);
    const msg = await client.sendMessage(chatId, media, { caption: caption || undefined });
    return res.json({ ok: true, message_id: msg.id?.id });
  } catch (err) {
    recordError(err);
    return res.status(500).json({ error: err.message });
  }
});

app.post('/send-buttons', async (req, res) => {
  const { to, body, buttons, title, footer } = req.body || {};
  if (!to || !body || !Array.isArray(buttons) || buttons.length === 0) {
    return res.status(422).json({ error: 'to, body and buttons are required' });
  }

  try {
    const formatted = buttons
      .map((btn) => ({
        id: btn.id,
        body: btn.title || btn.body || btn.label || '',
      }))
      .filter((btn) => btn.body !== '')
      .slice(0, 3);

    if (!formatted.length) {
      return res.status(422).json({ error: 'buttons are empty' });
    }

    const message = new Buttons(body, formatted, title, footer);
    const msg = await client.sendMessage(normalizeNumber(to), message);
    return res.json({ ok: true, message_id: msg.id?.id });
  } catch (err) {
    recordError(err);
    return res.status(500).json({ error: err.message });
  }
});

function normalizeNumber(number) {
  if (!number) return number;
  if (number.includes('@')) {
    return number;
  }
  return `${number}@c.us`;
}

function cleanNumber(number) {
  if (!number) return number;
  return number.replace('@c.us', '').replace('@lid', '');
}

async function fetchMedia(url) {
  const response = await axios.get(url, { responseType: 'arraybuffer' });
  const mime = response.headers['content-type'] || 'image/png';
  const data = Buffer.from(response.data, 'binary').toString('base64');
  return new MessageMedia(mime, data);
}

process.on('unhandledRejection', (err) => recordError(err, 'unhandled_rejection'));
process.on('uncaughtException', (err) => recordError(err, 'uncaught_exception'));

async function initClient() {
  try {
    console.log('[INIT] Iniciando cliente WhatsApp Web.js...');
    console.log('[INIT] SESSION_PATH:', process.env.SESSION_PATH || '.wwebjs_auth');
    await client.initialize();
    console.log('[INIT] ✓ Cliente inicializado com sucesso');
  } catch (err) {
    console.error('[INIT] ✗ Erro ao inicializar cliente:', err.message);
    recordError(err, 'init_error');
  }
}

initClient();

app.listen(PORT, () => {
  console.log(`[SERVER] ✓ WhatsApp Web service rodando em http://127.0.0.1:${PORT}`);
  console.log(`[SERVER] Endpoint /qr: http://127.0.0.1:${PORT}/qr`);
  console.log(`[SERVER] Endpoint /status: http://127.0.0.1:${PORT}/status`);
  console.log(`[SERVER] CORS habilitado para todas as origins`);
});
