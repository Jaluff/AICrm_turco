# Módulo 11 — Orquestador IA en conversaciones

```txt
MÓDULO 11 — ORQUESTADOR IA PARA CONVERSACIONES

Tareas:
1. Crear ConversationAiOrchestrator.
2. En mensaje entrante, decidir si atiende IA:
   - si handler_type human, no responder IA
   - si hay ai_agent activo, responder IA
   - si no, pending_human
3. Crear HandleAiConversationJob:
   - historial reciente
   - AiAgentService + RAG
   - guardar ai_interaction
   - crear message sender_type ai
   - enviar por WhatsApp
4. Reglas mínimas:
   - si pide humano, derivar
   - si fallback por falta de conocimiento, derivar o responder fallback
   - si error IA, derivar
5. DeriveToHumanAction:
   - status pending_human
   - handler_type none
   - internal note con motivo
   - event realtime
6. UI simple: Atendido por IA / Derivado a humano / motivo.

No implementes herramientas API todavía.

Al terminar, explicar prueba completa. No avances.
```
