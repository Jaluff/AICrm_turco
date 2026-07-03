# Stack y arquitectura

## Stack elegido

- Laravel.
- PostgreSQL.
- pgvector.
- Redis.
- Laravel Reverb.
- Livewire.
- Alpine.js.
- Tailwind CSS.
- Filament.
- Laravel Horizon.
- Laravel Scheduler.
- WhatsApp Cloud API.
- OpenAI API inicialmente.

## Tipo de arquitectura

**Monolito modular Laravel**.

No microservicios. No Kubernetes. No frontend separado pesado en fase 1.

## División de responsabilidades

### Filament

Usar para:

- empresas,
- usuarios,
- roles,
- contactos,
- etiquetas,
- motivos,
- departamentos,
- conexiones,
- respuestas rápidas,
- plantillas,
- mensajes programados,
- horarios,
- configuración IA,
- herramientas/API externas,
- logs.

### Livewire custom

Usar para:

- bandeja de chats,
- timeline de mensajes,
- composer,
- aceptar/resolver/transferir,
- posponer,
- ver estado IA,
- ver herramientas ejecutadas.

## Estructura recomendada

```txt
app/
  Actions/
  Services/
  DTOs/
  Support/
  ChannelAdapters/
  Ai/
    Tools/
  Jobs/
  Events/
  Filament/
  Livewire/
```

## Patrón de canales

No usar nombres atados a WhatsApp en el dominio principal.

Correcto:

```txt
channel_connections
conversations
messages
contact_identities
```

Incorrecto:

```txt
whatsapp_chats
whatsapp_messages
```

WhatsApp debe ser un adaptador:

```php
WhatsAppCloudAdapter implements ChannelAdapter
```

## Tenancy

Una sola base de datos. Todas las tablas principales tienen `company_id`.

No usar paquetes complejos de tenancy en fase 1.
