# Módulo 0 — Setup base

```txt
MÓDULO 0 — SETUP BASE

Prepará la base del proyecto Laravel para el CRM WhatsApp-first con IA.

Tareas:
1. Inspeccioná la estructura actual del proyecto.
2. Verificá dependencias: PostgreSQL, Redis, Filament, Livewire, Reverb, Horizon, Sanctum.
3. Si falta algo, indicame comandos antes de asumir instalación.
4. Crear estructura:
   - app/Actions
   - app/Services
   - app/DTOs
   - app/Support
   - app/ChannelAdapters
   - app/Ai
   - app/Ai/Tools
5. Ajustar .env.example con variables:
   - DB_CONNECTION=pgsql
   - OPENAI_API_KEY=
   - WHATSAPP_VERIFY_TOKEN=
   - WHATSAPP_APP_SECRET=
   - REVERB_*
   - REDIS_*
6. Configurar queue con Redis si corresponde.
7. Configurar broadcasting con Reverb si corresponde.
8. Crear docs/arquitectura.md con resumen técnico.

No implementes modelos de negocio todavía.

Al terminar, informá archivos, comandos y próximos pasos. No avances.
```
