<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Support\Tenant;
use Carbon\Carbon;
use Exception;

class SendScheduledMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-scheduled-messages';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Procesa y envía los mensajes programados cuya fecha de envío ya expiró.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Iniciando procesamiento de mensajes programados...');

        // Consultar mensajes pendientes expirados (sin Scope de Tenant inicialmente)
        $scheduledMessages = ScheduledMessage::where('status', 'pending')
            ->where('send_at', '<=', now())
            ->get();

        if ($scheduledMessages->isEmpty()) {
            $this->info('No hay mensajes programados pendientes para enviar.');
            return;
        }

        $this->info("Procesando {$scheduledMessages->count()} mensaje(s) programado(s)...");

        foreach ($scheduledMessages as $scheduledMessage) {
            try {
                // Establecer el Tenant para el procesamiento del mensaje
                Tenant::set($scheduledMessage->company);

                $this->sendMessage($scheduledMessage);

                $this->info("Mensaje programado ID {$scheduledMessage->id} procesado con éxito.");
            } catch (Exception $e) {
                $this->error("Error procesando mensaje programado ID {$scheduledMessage->id}: " . $e->getMessage());
                
                $scheduledMessage->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            } finally {
                Tenant::clear();
            }
        }

        $this->info('Procesamiento completado.');
    }

    /**
     * Procesa el envío de un mensaje programado individual.
     */
    protected function sendMessage(ScheduledMessage $scheduledMessage): void
    {
        // 1. Buscar o crear conversación activa
        $conversation = $scheduledMessage->conversation;
        if (!$conversation) {
            $conversation = Conversation::where('contact_id', $scheduledMessage->contact_id)
                ->where('channel_connection_id', $scheduledMessage->channel_connection_id)
                ->where('status', '!=', 'closed')
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'contact_id' => $scheduledMessage->contact_id,
                    'channel_connection_id' => $scheduledMessage->channel_connection_id,
                    'status' => 'open',
                    'handler_type' => 'none',
                    'last_message_at' => now(),
                ]);
            }

            $scheduledMessage->update([
                'conversation_id' => $conversation->id,
            ]);
        }

        // 2. Preparar el cuerpo y los metadatos según el tipo de mensaje
        $body = '';
        $type = 'text';
        $metadata = [];

        if ($scheduledMessage->message_template_id) {
            $template = $scheduledMessage->messageTemplate;
            if (!$template) {
                throw new Exception("La plantilla asociada ID {$scheduledMessage->message_template_id} no existe.");
            }

            $type = 'template';
            $body = $this->renderTemplateBody($template, $scheduledMessage->variables ?? []);
            $whatsappComponents = $this->buildWhatsAppComponents($scheduledMessage->variables ?? []);

            $metadata = [
                'template_name' => $template->name,
                'language_code' => $template->language,
                'components' => $whatsappComponents,
            ];
        } else {
            $body = $scheduledMessage->body;
        }

        // 3. Crear el mensaje saliente en estado 'pending'
        $message = Message::create([
            'company_id' => $scheduledMessage->company_id,
            'conversation_id' => $conversation->id,
            'contact_id' => $scheduledMessage->contact_id,
            'channel_connection_id' => $scheduledMessage->channel_connection_id,
            'sender_type' => 'system',
            'direction' => 'outbound',
            'type' => $type,
            'body' => $body,
            'status' => 'pending',
            'metadata' => $metadata,
        ]);

        // 4. Actualizar la programación con la referencia al mensaje y marcar como procesando (dejando que el Job de salida ponga el 'sent' final)
        $scheduledMessage->update([
            'sent_message_id' => $message->id,
            // Lo dejamos en pending para que cambie a sent o failed tras la llamada a la API en el Job
        ]);

        // 5. Despachar el Job de envío inmediatamente
        \App\Jobs\SendOutgoingMessageJob::dispatch($message);
    }

    /**
     * Renderiza el cuerpo de la plantilla localmente reemplazando las variables.
     */
    protected function renderTemplateBody($template, array $variables): string
    {
        $bodyText = '';
        if (is_array($template->components)) {
            foreach ($template->components as $component) {
                if (($component['type'] ?? '') === 'BODY') {
                    $bodyText = $component['text'] ?? '';
                    break;
                }
            }
        }

        if (empty($bodyText)) {
            $bodyText = $template->name;
        }

        foreach ($variables as $index => $value) {
            $placeholder = '{{' . ($index + 1) . '}}';
            $bodyText = str_replace($placeholder, $value, $bodyText);
        }

        return $bodyText;
    }

    /**
     * Construye la estructura de componentes esperada por la API de WhatsApp Cloud.
     */
    protected function buildWhatsAppComponents(array $variables): array
    {
        if (empty($variables)) {
            return [];
        }

        $parameters = [];
        foreach ($variables as $value) {
            $parameters[] = [
                'type' => 'text',
                'text' => $value,
            ];
        }

        return [
            [
                'type' => 'body',
                'parameters' => $parameters,
            ]
        ];
    }
}
