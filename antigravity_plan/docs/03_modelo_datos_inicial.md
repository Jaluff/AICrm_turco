# Modelo de datos inicial

## Tablas principales

```txt
companies
users
departments
department_user
contacts
contact_identities
tags
contact_tag
contact_reasons
channel_connections
webhook_events
conversations
messages
message_attachments
conversation_events
conversation_reason_assignments
quick_replies
message_templates
scheduled_messages
business_hours
ai_agents
ai_interactions
knowledge_base_articles
knowledge_base_chunks
external_api_connections
ai_tools
ai_agent_tools
ai_tool_runs
audit_logs
```

## Estados de conversación

```txt
pending_human
open
closed
snoozed
```

## Handler de conversación

```txt
handler_type = ai | human | none
handler_id = ai_agent_id o user_id
```

## Tipos de mensaje

```txt
contact
human
ai
system
internal_note
```

## Dirección de mensaje

```txt
inbound
outbound
internal
```

## Tipos de canal

```txt
whatsapp_cloud
webchat      // futuro
instagram    // futuro
facebook     // futuro
```
