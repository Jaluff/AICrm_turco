# Módulo 1 — Empresas, usuarios y roles

```txt
MÓDULO 1 — EMPRESAS, USUARIOS Y ROLES

Implementá multiempresa simple sin paquetes complejos de tenancy.

Tareas:
1. Crear Company:
   - name
   - slug unique
   - timezone nullable
   - default_language nullable
   - status default active
2. Agregar company_id a users.
3. Agregar role simple a users: admin, agent, ai_manager opcional.
4. Agregar availability_status, unavailable_until, last_seen_at.
5. Relaciones Company hasMany Users, User belongsTo Company.
6. Helper/middleware simple para currentCompany desde usuario autenticado.
7. Scopes o policies básicas por company_id.
8. Factory de Company y ajustar UserFactory.
9. Seeder demo opcional.
10. Tests mínimos de relaciones.

No implementes contactos ni conversaciones.

Al terminar, resumí archivos, migraciones y comandos. No avances.
```
