# Módulo 2 — Contactos, departamentos, etiquetas y motivos

```txt
MÓDULO 2 — CONTACTOS, DEPARTAMENTOS, ETIQUETAS Y MOTIVOS

Implementá catálogos base con company_id.

Crear:
1. departments:
   - company_id, name, color
   - greeting_message, farewell_message, away_message
   - auto_assignment_enabled
   - assign_offline_enabled
   - redistribute_unavailable_enabled
   - ai_enabled
2. tags:
   - company_id, name, color
3. contact_reasons:
   - company_id, name, color, active
4. contacts:
   - company_id, name, nickname, phone, email, language, avatar_url
   - opt_in, opt_out
   - custom_fields jsonb
5. contact_identities:
   - company_id, contact_id, channel_type, external_id, phone, username, metadata jsonb
6. Pivots:
   - contact_tag
   - department_user con receives_auto_assignment
7. Relaciones completas.
8. Filament Resources para Contacts, Tags, ContactReasons, Departments.
9. Tests mínimos.

No implementes conversaciones ni WhatsApp todavía.

Al terminar, resumí archivos y comandos. No avances.
```
