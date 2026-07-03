# Módulo 3 — Canales y WhatsApp Cloud API

```txt
MÓDULO 3 — CHANNEL CONNECTIONS Y WHATSAPP CLOUD API

Implementá abstracción de canales y conexión WhatsApp Cloud API.

Tareas:
1. Crear channel_connections:
   - company_id
   - type default whatsapp_cloud
   - name
   - status
   - external_business_id
   - external_phone_number_id
   - external_waba_id
   - phone_number
   - access_token encrypted
   - verify_token
   - app_secret encrypted
   - greeting_message
   - farewell_message
   - metadata jsonb
2. Constantes de tipo: whatsapp_cloud, webchat futuro, instagram futuro, facebook futuro.
3. Crear ChannelAdapter interface.
4. Crear WhatsAppCloudAdapter con sendTextMessage, sendTemplateMessage preparado y normalizeIncomingWebhook.
5. Filament Resource para ChannelConnection.
6. Crear webhook_events.
7. Ruta GET de verificación webhook WhatsApp.
8. Ruta POST para guardar webhook y encolar ProcessIncomingWebhookJob.
9. Tests: verify token correcto/incorrecto y guardado POST.

No proceses conversaciones todavía.

Al terminar, resumí rutas, adapter, migraciones, tests. No avances.
```
