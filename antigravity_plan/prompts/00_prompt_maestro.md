# Prompt maestro inicial

```txt
Vas a ayudarme a desarrollar un MVP en Laravel para un CRM conversacional WhatsApp-first con agentes IA.

Leé y respetá estos documentos si existen en el proyecto:
- antigravity_plan/docs/00_contexto_producto.md
- antigravity_plan/docs/01_stack_arquitectura.md
- antigravity_plan/docs/02_alcance_mvp_fase1.md
- antigravity_plan/docs/03_modelo_datos_inicial.md
- antigravity_plan/docs/04_reglas_codigo.md
- antigravity_plan/docs/05_whatsapp_cloud_api.md
- antigravity_plan/docs/06_ia_rag_herramientas.md

Contexto:
- Fase 1 usa solo WhatsApp Cloud API como canal operativo real.
- El diseño debe permitir sumar WebChat, Instagram y Facebook después.
- La IA atiende por defecto usando RAG y herramientas/API externas.
- Humanos intervienen como fallback.
- Stack: Laravel, PostgreSQL, Redis, Reverb, Livewire, Filament, pgvector, OpenAI.

Reglas:
- Trabajá solo sobre el módulo que te indique.
- No avances al siguiente módulo sin mi confirmación.
- No implementes microservicios.
- No implementes campañas masivas ni billing en fase 1.
- No implementes chatbot visual complejo.
- Mantené company_id en entidades multiempresa.
- Usá Actions, Services, Jobs, Events y Filament Resources.
- Livewire custom solo para la pantalla de chats.
- Al finalizar cada módulo, resumí archivos modificados, comandos y tests.
```
