# Módulo 12 — Herramientas/API externas

```txt
MÓDULO 12 — AI TOOLS Y APIS EXTERNAS

Implementar herramientas como clases PHP controladas. No crear constructor no-code universal.

Tareas:
1. Crear AiTool interface:
   - name
   - description
   - parametersSchema
   - requiresConfirmation
   - execute
2. Crear ToolResult DTO.
3. Crear external_api_connections.
4. Crear ai_tools.
5. Crear ai_agent_tools pivot.
6. Crear ai_tool_runs.
7. ToolRegistry.
8. Dos herramientas iniciales:
   - CheckOrderStatusTool
   - CreateSupportTicketTool
   Si no hay API real, usar mock controlado con estructura lista.
9. Integrar tool/function calling en AiAgentService.
10. Registrar ejecuciones.
11. Si falla una herramienta, derivar a humano con nota.
12. Filament Resources para conexiones externas, tools y logs.
13. En UI chat mostrar herramientas ejecutadas.

Al terminar, explicar cómo crear nueva herramienta. No avances.
```
