# Módulo 7 — Plantillas WhatsApp y mensajes programados

```txt
MÓDULO 7 — PLANTILLAS WHATSAPP Y MENSAJES PROGRAMADOS

Tareas:
1. Crear message_templates:
   - company_id
   - channel_connection_id
   - external_template_id
   - name
   - language
   - category
   - status
   - components jsonb
   - variables jsonb
2. Filament Resource.
3. WhatsAppCloudAdapter::sendTemplateMessage.
4. Crear scheduled_messages:
   - company_id
   - contact_id
   - conversation_id nullable
   - channel_connection_id
   - message_template_id nullable
   - body nullable
   - variables jsonb
   - send_at
   - status
   - sent_message_id
   - error
   - created_by
5. Resource para ScheduledMessages.
6. Scheduler cada minuto para enviar vencidos.
7. Crear desde contacto o chat un mensaje programado simple.

No implementes campañas masivas.

Al terminar, explicar cómo probar. No avances.
```
