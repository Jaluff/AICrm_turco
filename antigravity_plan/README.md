# Pack de planificación para Antigravity IDE

Proyecto: **CRM WhatsApp-first con agentes IA, RAG y acciones por APIs externas**.

Este pack está pensado para usar con un agente de código en Antigravity IDE.

## Estrategia recomendada

Usar **dos tipos de archivos**:

1. **Docs permanentes** en `docs/`  
   Son el contexto estable del proyecto: producto, arquitectura, reglas, stack, modelo de datos y decisiones.

2. **Prompts por módulo** en `prompts/`  
   Son instrucciones ejecutables. Se cargan de a uno. No avanzar al siguiente hasta terminar el actual.

## Flujo de trabajo

Para cada módulo:

1. Abrí Antigravity IDE.
2. Dale contexto con estos docs:
   - `docs/00_contexto_producto.md`
   - `docs/01_stack_arquitectura.md`
   - `docs/02_alcance_mvp_fase1.md`
   - `docs/04_reglas_codigo.md`
3. Pegá el prompt correspondiente de `prompts/`.
4. Esperá implementación.
5. Corré migraciones/tests.
6. Pedile revisión.
7. Recién cuando esté correcto, cargá el próximo módulo.
8. Usá pnpm en lugar de npm para instalar dependencias frontend. No agregues paquetes NPM innecesarios. Si necesitás instalar un paquete nuevo, primero explicá para qué sirve y pedime confirmación. Mantené el frontend simple con Blade, Livewire, Alpine y Tailwind.


## Orden de prompts

1. `00_prompt_maestro.md`
2. `01_setup_base.md`
3. `02_empresas_usuarios_roles.md`
4. `03_contactos_departamentos.md`
5. `04_canales_whatsapp_cloud.md`
6. `05_conversaciones_mensajes.md`
7. `06_bandeja_chats.md`
8. `07_respuestas_motivos_operacion.md`
9. `08_plantillas_mensajes_programados.md`
10. `09_horarios_atencion.md`
11. `10_ia_base_agentes_logs.md`
12. `11_rag_pgvector_base_conocimiento.md`
13. `12_orquestador_ia_conversaciones.md`
14. `13_herramientas_api_externas.md`
15. `14_hardening_mvp.md`

## Regla de oro

> Si una tarea no ayuda a que WhatsApp + IA + humano fallback funcione, no va en fase 1.
