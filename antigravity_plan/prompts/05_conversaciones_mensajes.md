# Módulo 4 — Conversaciones y mensajes

```txt
MÓDULO 4 — CONVERSATIONS, MESSAGES Y WEBHOOK PROCESSING

Implementá núcleo de conversaciones/mensajes.

Crear:
1. conversations con company_id, contact_id, channel_connection_id, department_id, assigned_user_id, status, handler_type, handler_id, flags IA, keep_assigned, snoozed_until, first_response_at, assigned_at, resolved_at, last_message_at, metadata.
2. messages con company_id, conversation_id, contact_id, channel_connection_id, sender_type, sender_user_id, ai_agent_id, external_message_id, direction, type, body, status, sent_at, delivered_at, read_at, metadata.
3. message_attachments.
4. conversation_events.
5. conversation_reason_assignments.
6. ProcessIncomingWebhookJob:
   - leer webhook_events
   - normalizar WhatsApp
   - buscar/crear contacto
   - crear identity
   - buscar/crear conversación
   - guardar message inbound
   - actualizar last_message_at
7. SendMessageAction.
8. SendOutgoingMessageJob.
9. Tests mínimos inbound y outbound.

No implementes UI ni IA todavía.

Al terminar, resumí cambios. No avances.
```
