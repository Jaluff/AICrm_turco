# Arquitectura del Sistema - CRM Conversacional WhatsApp-first

Este documento resume las decisiones técnicas y de diseño para la base del CRM.

## Stack Tecnológico

- **Framework**: Laravel 13.x
- **Base de Datos**: PostgreSQL 18 + extensión `pgvector` para RAG.
- **Cache & Colas**: Redis (usando el cliente PHP `predis/predis`).
- **Realtime**: Laravel Reverb (Broadcasting en tiempo real para la bandeja de chats).
- **Procesamiento en Segundo Plano**: Laravel Horizon (Monitoreo de colas Redis).
- **UI/Bandeja**: Filament (Paneles de administración) y Livewire (Bandeja de chat interactiva customizada).
- **IA**: API de OpenAI inicialmente.

## Estructura de Directorios

Se han creado carpetas especializadas en `app/` para estructurar limpiamente las capas de la aplicación:

```txt
app/
├── Actions/            # Casos de uso de negocio autónomos y reutilizables.
├── Services/           # Lógica compartida de integración o utilidades más complejas.
├── DTOs/               # Objetos de transferencia de datos estructurados.
├── Support/            # Clases auxiliares y extensiones del framework.
├── ChannelAdapters/    # Adaptadores para los distintos canales de chat (WhatsApp, etc.).
├── Ai/                 # Lógica de agentes, prompts y RAG.
│   └── Tools/          # Funciones externas (tools) que puede invocar la IA.
├── Filament/           # Recursos y configuración de la interfaz administrativa de Filament.
└── Livewire/           # Componentes interactivos de Livewire para la bandeja de chats.
```

## Estrategia de Multiempresa (Tenancy)

- Se opta por **Single Database Multi-tenant**.
- Todas las tablas principales de negocio deben incluir un campo `company_id`.
- Se aplicará aislamiento mediante scopes globales (`Scope`) de Eloquent o filtrado explícito en queries críticas.

## Abstracción de Canales

Para evitar acoplamiento con la API de WhatsApp, la bandeja de entrada y el timeline de mensajes utilizan conceptos genéricos:
- `conversations` (Conversaciones genéricas).
- `messages` (Mensajes entrantes y salientes).
- `contact_identities` (Identidades del contacto en cada canal).
- El envío y recepción pasan a través de adaptadores que implementan una interfaz común (e.g., `ChannelAdapter`).

## Colas y Broadcasting en Tiempo Real

- Las colas se configuran con la conexión `redis` y se administran a través de **Laravel Horizon** (`php artisan horizon`).
- El broadcasting utiliza **Laravel Reverb**, lo que permite notificar en tiempo real en la bandeja de chats de Livewire la llegada de nuevos mensajes sin dependencias de servicios externos comerciales.
