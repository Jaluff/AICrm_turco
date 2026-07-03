<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Company;
use App\Models\ChannelConnection;
use App\ChannelAdapters\WhatsAppCloudAdapter;
use App\Support\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Exception;

class SendOutgoingMessageJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Message $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Establecer el tenant activo para el procesamiento asíncrono
            $company = Company::find($this->message->company_id);
            if (!$company) {
                throw new Exception("Empresa no encontrada para el mensaje ID {$this->message->id}");
            }
            Tenant::set($company);

            // Cargar relación de contacto si no está cargada
            $contact = $this->message->contact;
            if (!$contact) {
                throw new Exception("El mensaje no tiene un contacto asociado.");
            }

            // Resolver conexión
            $connection = $this->message->channelConnection;
            if (!$connection) {
                throw new Exception("El mensaje no tiene una conexión de canal asociada.");
            }

            if ($connection->type === ChannelConnection::TYPE_WHATSAPP_CLOUD) {
                $adapter = new WhatsAppCloudAdapter();
                
                // Envío real (o simulado por HTTP fakes en tests)
                if ($this->message->type === 'template') {
                    $templateName = $this->message->metadata['template_name'] ?? '';
                    $languageCode = $this->message->metadata['language_code'] ?? 'es';
                    $components = $this->message->metadata['components'] ?? [];

                    $result = $adapter->sendTemplateMessage(
                        $connection,
                        $contact->phone,
                        $templateName,
                        $languageCode,
                        $components
                    );
                } else {
                    $result = $adapter->sendTextMessage($connection, $contact->phone, $this->message->body);
                }

                if ($result->success) {
                    $this->message->update([
                        'status' => 'sent',
                        'external_message_id' => $result->externalMessageId,
                        'sent_at' => now(),
                    ]);

                    \App\Models\ScheduledMessage::where('sent_message_id', $this->message->id)
                        ->update(['status' => 'sent']);
                } else {
                    $meta = $this->message->metadata ?? [];
                    $meta['error'] = $result->errorMessage;
                    if ($result->rawResponse) {
                        $meta['raw_response'] = $result->rawResponse;
                    }

                    $this->message->update([
                        'status' => 'failed',
                        'metadata' => $meta,
                    ]);

                    \App\Models\ScheduledMessage::where('sent_message_id', $this->message->id)
                        ->update([
                            'status' => 'failed',
                            'error' => $result->errorMessage,
                        ]);
                }
            }
        } catch (Exception $e) {
            $meta = $this->message->metadata ?? [];
            $meta['job_error'] = $e->getMessage();
            $this->message->update([
                'status' => 'failed',
                'metadata' => $meta,
            ]);

            \App\Models\ScheduledMessage::where('sent_message_id', $this->message->id)
                ->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);

            throw $e;
        } finally {
            Tenant::clear();
        }
    }
}
