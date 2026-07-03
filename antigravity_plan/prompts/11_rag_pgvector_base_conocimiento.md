# Módulo 10 — RAG con pgvector

```txt
MÓDULO 10 — RAG, BASE DE CONOCIMIENTO Y PGVECTOR

Tareas:
1. Verificar/habilitar pgvector.
2. Crear knowledge_base_articles:
   - company_id
   - department_id nullable
   - title
   - content
   - active
3. Crear knowledge_base_chunks:
   - company_id
   - article_id
   - department_id nullable
   - content
   - embedding vector
   - metadata jsonb
4. KnowledgeBaseService:
   - splitArticleIntoChunks
   - generateEmbeddings
   - searchRelevantChunks
5. GenerateKnowledgeBaseEmbeddingsJob.
6. Filament Resource para artículos.
7. Integrar AiAgentService con RAG.
8. Si no hay contexto suficiente, usar fallback/derivación según config.

No implementes herramientas todavía.

Al terminar, explicar cómo cargar artículo y probar pregunta. No avances.
```
