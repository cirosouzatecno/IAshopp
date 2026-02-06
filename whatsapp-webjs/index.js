require('dotenv').config();

const express = require('express');
const cors = require('cors');
const axios = require('axios');
const qrcode = require('qrcode');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');

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
  lastQr = qr;
  status = 'qr';
  try {
    lastQrImage = await qrcode.toDataURL(qr);
  } catch (err) {
    lastError = err.message;
  }
});

client.on('authenticated', () => {
  status = 'authenticated';
});

client.on('ready', () => {
  status = 'ready';
  lastQr = null;
  lastQrImage = null;
});

client.on('auth_failure', (msg) => {
  status = 'auth_failure';
  lastError = msg;
});

client.on('disconnected', (reason) => {
  status = 'disconnected';
  lastError = reason;
});

client.on('message', async (message) => {
  if (!message.from || message.fromMe) {
    return;
  }

  try {
    const contact = await message.getContact();
    const payload = {
      from: cleanNumber(message.from),
      text: message.body || '',
      name: contact?.pushname || contact?.name || null,
      raw: {
        id: message.id?.id,
        from: message.from,
        body: message.body,
        timestamp: message.timestamp,
      },
    };

    await axios.post(LARAVEL_WEBHOOK_URL, payload, {
      headers: WEBHOOK_TOKEN ? { 'X-Webhook-Token': WEBHOOK_TOKEN } : undefined,
    });
  } catch (err) {
    lastError = err.message;
  }
});

app.get('/status', (req, res) => {
  res.json({
    status,
    lastError,
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
    lastError = err.message;
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
    lastError = err.message;
    return res.status(500).json({ error: err.message });
  }
});

function normalizeNumber(number) {
  if (!number) return number;
  if (number.includes('@c.us')) {
    return number;
  }
  return `${number}@c.us`;
}

function cleanNumber(number) {
  if (!number) return number;
  return number.replace('@c.us', '');
}

async function fetchMedia(url) {
  const response = await axios.get(url, { responseType: 'arraybuffer' });
  const mime = response.headers['content-type'] || 'image/png';
  const data = Buffer.from(response.data, 'binary').toString('base64');
  return new MessageMedia(mime, data);
}

client.initialize();

app.listen(PORT, () => {
  // eslint-disable-next-line no-console
  console.log(`WhatsApp Web service running on port ${PORT}`);
});
