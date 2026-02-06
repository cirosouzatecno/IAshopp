# IAshopp - Robô de Vendas WhatsApp

Aplicação Laravel com painel admin, catálogo de produtos e fluxo de pedidos via WhatsApp (Meta Cloud API). Inclui pagamento via Pix estático (chave + QR), confirmação manual e atualização de status.

## Requisitos
- PHP 8.2+
- MySQL (XAMPP)
- Node.js (para build dos assets)

## Setup rápido (XAMPP)
1. Crie o banco:
   - `CREATE DATABASE iashopp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
2. Ajuste `.env` se necessário (credenciais MySQL e `APP_URL`).
3. Instale dependências:
   - `composer install`
   - `npm install`
4. Gere build de assets:
   - `npm run build`
5. Rode as migrações:
   - `php artisan migrate`
6. Crie um usuário admin:
   - Use a tela de registro `/register` ou `php artisan tinker` para criar um usuário.

## Webhook do WhatsApp
Endpoint:
- `GET /webhook/whatsapp` (verificação)
- `POST /webhook/whatsapp` (mensagens)

Configure as credenciais no painel:
`Admin > Configurações`.

## Integração via QR (WhatsApp Web)
Esta opção **não é oficial** e pode sofrer instabilidade/bloqueio.  
Se quiser usar QR:
1. No painel, selecione **Provedor: WhatsApp Web (QR)**.
2. Configure o `WhatsApp Web - Base URL` (padrão: `http://127.0.0.1:3001`).
3. (Opcional) Defina um `Token Webhook (QR)`.
4. Inicie o serviço Node:
   - `cd whatsapp-webjs`
   - `npm install`
   - Copie `.env.example` para `.env` e ajuste `LARAVEL_WEBHOOK_URL` se necessário.
   - `npm start`
5. Acesse **Admin > WhatsApp Web** para escanear o QR.

## Fluxo do Bot (Resumo)
1. Menu inicial
2. Catálogo
3. Pedido + quantidade
4. Entrega/retirada
5. Instruções Pix
6. Status atualizado manualmente no painel

## Configurações Pix
No painel:
- Chave Pix
- Tipo de chave (CPF, CNPJ, email, etc.)
- Recebedor e cidade
 - QR Code (imagem)

## Status do Pedido
- Aguardando Pagamento
- Confirmado
- Entregue
