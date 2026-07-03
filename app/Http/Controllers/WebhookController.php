<?php

namespace App\Http\Controllers;

use App\Models\ChannelConnection;
use App\Models\WebhookEvent;
use App\Jobs\ProcessIncomingWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Verifica el webhook con Meta (GET).
     */
    public function verifyWhatsApp(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode !== 'subscribe' || !$token) {
            return response('Forbidden', 403);
        }

        // 1. Verificar contra el token global del config/services.php
        $globalToken = config('services.whatsapp.verify_token');
        if ($globalToken && $token === $globalToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        // 2. Verificar contra los tokens guardados en las conexiones de canales (tenants)
        // Como no tenemos usuario autenticado (es una llamada anónima de Meta),
        // deshabilitamos el Tenant scope temporalmente para buscar en todas las conexiones.
        $exists = ChannelConnection::withoutGlobalScopes()
            ->where('verify_token', $token)
            ->exists();

        if ($exists) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Recibe y guarda los eventos de webhook de WhatsApp (POST).
     */
    public function handleWhatsApp(Request $request)
    {
        $payload = $request->all();

        // Extraer el external_phone_number_id del payload
        $phoneNumberId = null;
        if (!empty($payload['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'])) {
            $phoneNumberId = $payload['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
        }

        // Resolver la empresa (company_id) a partir del phone_number_id
        $companyId = null;
        if ($phoneNumberId) {
            $connection = ChannelConnection::withoutGlobalScopes()
                ->where('external_phone_number_id', $phoneNumberId)
                ->first();
            if ($connection) {
                $companyId = $connection->company_id;
            }
        }

        // Registrar el evento
        // Usamos withoutGlobalScopes para crear o podemos usar DB/Eloquent directamente
        // Seteamos el status por defecto a 'pending'
        $event = WebhookEvent::create([
            'company_id' => $companyId,
            'channel_type' => 'whatsapp_cloud',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        // Despachar el Job para procesamiento asíncrono
        ProcessIncomingWebhookJob::dispatch($event);

        return response('EVENT_RECEIVED', 200);
    }
}
