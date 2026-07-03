# WhatsApp Cloud API — diseño fase 1

## Objetivo

Usar WhatsApp Cloud API oficial como único canal operativo real en fase 1.

## Necesidades

- Verificación de webhook.
- Recepción de mensajes.
- Envío de texto.
- Envío de plantillas.
- Estados de mensaje en fase posterior.
- Multimedia en fase posterior si complica.

## Variables de entorno

```txt
WHATSAPP_VERIFY_TOKEN=
WHATSAPP_APP_SECRET=
```

Los tokens por número/conexión se guardan cifrados en `channel_connections`.

## Flujo entrante

```txt
POST webhook
→ guardar payload en webhook_events
→ ProcessIncomingWebhookJob
→ normalizar payload
→ buscar/crear contacto
→ crear/reabrir conversación
→ crear mensaje inbound
→ emitir realtime
→ orquestador IA o pending_human
```

## Flujo saliente

```txt
SendMessageAction
→ crear message outbound pending
→ SendOutgoingMessageJob
→ WhatsAppCloudAdapter
→ actualizar status/external_message_id
```

## Adapter

Crear interfaz genérica:

```php
interface ChannelAdapter
{
    public function sendTextMessage(...): SendResult;
    public function sendTemplateMessage(...): SendResult;
    public function normalizeIncomingWebhook(array $payload): IncomingMessageData;
}
```
