# IA, RAG y herramientas externas

## Enfoque

Sistema IA-first con fallback humano.

No construir chatbot visual complejo en fase 1.

## Agentes IA

Cada agente puede estar asociado a:

- empresa,
- departamento,
- modelo,
- prompt,
- idioma,
- fallback,
- transferencia a humano,
- base de conocimiento,
- herramientas permitidas.

## RAG

Usar PostgreSQL + pgvector.

Flujo:

```txt
Artículo → chunks → embeddings → búsqueda vectorial → contexto → respuesta IA
```

## Reglas mínimas

Derivar a humano si:

- cliente pide humano,
- confianza baja,
- no hay contexto suficiente,
- error de modelo,
- herramienta externa falla,
- acción crítica requiere aprobación,
- intención sensible.

## Herramientas externas

No hacer constructor no-code universal al inicio.

Usar clases PHP:

```php
CheckOrderStatusTool
CheckStockTool
GeneratePaymentLinkTool
CreateSupportTicketTool
BookAppointmentTool
CreateLeadTool
```

Cada herramienta implementa:

```php
interface AiTool
{
    public function name(): string;
    public function description(): string;
    public function parametersSchema(): array;
    public function requiresConfirmation(): bool;
    public function execute(array $arguments, Conversation $conversation): ToolResult;
}
```

Registrar cada ejecución en `ai_tool_runs`.
