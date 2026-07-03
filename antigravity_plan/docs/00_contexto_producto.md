# Contexto del producto

## Visión

Construir un CRM conversacional **WhatsApp-first** donde agentes IA atienden la mayoría de conversaciones, consultan conocimiento interno, ejecutan acciones reales mediante APIs externas y derivan a humanos solo cuando corresponde.

## Fase 1

Canal operativo real: **WhatsApp Cloud API oficial**.

El sistema debe quedar diseñado para sumar luego:

- WebChat.
- Instagram.
- Facebook Messenger.
- Otros canales.

Pero fase 1 no debe implementar esos canales.

## Filosofía IA-first

La IA no será solo un panel de ayuda para humanos. La IA debe:

- recibir el mensaje,
- entender intención,
- consultar RAG,
- ejecutar herramientas/API externas,
- responder,
- derivar si no puede resolver.

El humano es fallback y supervisor operativo.

## Flujo principal deseado

```txt
Cliente escribe por WhatsApp
→ webhook recibe mensaje
→ sistema crea contacto/conversación
→ agente IA interpreta intención
→ consulta base de conocimiento
→ ejecuta API externa si hace falta
→ responde por WhatsApp
→ si no puede resolver, deriva a humano con resumen
```

## Diferencial del producto

No competir solo como bandeja multiagente. Diferenciarse por:

- agentes IA especializados,
- acciones reales con APIs externas,
- logs de herramientas,
- transferencia a humano con contexto,
- RAG mantenible,
- arquitectura simple para un solo desarrollador.
