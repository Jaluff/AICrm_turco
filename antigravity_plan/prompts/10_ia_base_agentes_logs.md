# Módulo 9 — Base IA, agentes y logs

```txt
MÓDULO 9 — BASE DE IA: PROVIDERS, AGENTES Y LOGS

Tareas:
1. Configuración IA de empresa con provider openai, api_key encrypted, default_model.
2. Crear ai_agents:
   - company_id
   - department_id nullable
   - name
   - model
   - response_language
   - prompt
   - fallback_message
   - transfer_enabled
   - transfer_message
   - confidence_threshold
   - active
3. Crear ai_interactions:
   - company_id
   - conversation_id
   - ai_agent_id
   - type
   - input jsonb
   - output jsonb
   - model
   - tokens/cost/error
4. AiProvider interface.
5. OpenAiProvider.
6. AiAgentService para respuesta simple con prompt + historial.
7. Filament Resources para configuración IA, agentes e interactions solo lectura.

No implementes RAG ni herramientas todavía.

Al terminar, explicar cómo probar respuesta simple. No avances.
```
