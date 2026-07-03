# Reglas de código para el agente

## Principios

- Mantener simple.
- No sobrediseñar.
- No agregar paquetes innecesarios.
- No construir features fuera del módulo actual.
- Preferir código explícito y fácil de leer.
- Crear Actions para casos de uso importantes.
- Crear Services para lógica compartida.
- Crear Jobs para procesos asíncronos.
- Crear Events para realtime y auditoría.

## Multiempresa

Toda query crítica debe filtrar por `company_id`.

No mezclar datos entre empresas.

## Seguridad

Cifrar:

- tokens WhatsApp,
- OpenAI API key,
- credenciales de APIs externas.

No loguear secretos.

## Tests

Agregar tests mínimos por módulo cuando aplique.

No buscar cobertura perfecta en fase 1, pero sí cubrir:

- relaciones críticas,
- webhooks,
- creación de conversaciones,
- envío de mensajes,
- RAG básico,
- herramientas IA.

## Definition of Done

Un módulo termina cuando:

- compila,
- migraciones corren,
- relaciones básicas funcionan,
- tests mínimos pasan o quedan indicados,
- no se avanzó fuera del alcance,
- se documentan archivos cambiados y comandos.
