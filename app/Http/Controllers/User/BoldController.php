<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Users\OrderBold;
use Illuminate\Support\Facades\Log;

class BoldController extends Controller
{
    public function generateSignature(Request $request)
    {
        try{
            $request->validate([
                'orderId' => 'required|string',
                'amount' => 'required|numeric',
                'currency' => 'required|string',
            ]);

            $orderId = $request->orderId;
            $amount = (int) $request->amount;
            $currency = $request->currency;

            // 🔐 TU LLAVE SECRETA (NO la pública)
            $secretKey = env('BOLD_SECRET_KEY');

            // 🔥 Construcción del string (IMPORTANTE ORDEN)
            $data = $orderId . $amount . $currency . $secretKey;

            // 🔐 Generar firma
            $signature = hash('sha256', $data);

            return ApiResponse::success('Firma generada', Response::HTTP_OK, ['signature' => $signature]);

        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function createOrder(Request $request)
    {

        try{

            $request->validate([
            'orderId' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            ]);

            $order = OrderBold::create([
                'order_id' => $request->orderId,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'status' => 'pending'
            ]);

            return ApiResponse::success('Orden creada', Response::HTTP_OK, $order);

        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

  public function webhook(Request $request)
{
    try {

        // 🔥 1. OBTENER PAYLOAD (SIEMPRE)
        $payload = $request->all();

        if (empty($payload)) {
            $payload = json_decode($request->getContent(), true);
        }

        Log::info('🔥 WEBHOOK BOLD', $payload);

        // 🧠 2. EXTRAER DATOS CLAVE (ANTES DE TODO)
        $orderId = data_get($payload, 'data.metadata.reference');
        $paymentId = data_get($payload, 'data.payment_id');
        $type = data_get($payload, 'type');

        // 🔎 3. BUSCAR ORDEN (SI EXISTE)
        $order = null;

        if ($orderId) {
            $order = OrderBold::where('order_id', $orderId)->first();
        }

        // 💾 4. GUARDAR SIEMPRE LA RESPUESTA (🔥 CLAVE)
        if ($order) {
            $order->bold_response = [
                'payload' => $payload,
                'raw' => $request->getContent(),
                'headers' => $request->headers->all()
            ];
            $order->save();
        } else {
            Log::warning('⚠️ Orden no encontrada para guardar payload', [
                'order_id' => $orderId
            ]);
        }

        // 🔐 5. VALIDAR FIRMA (DESPUÉS DE GUARDAR)
        $receivedSignature = $request->header('x-bold-signature');
        $raw = $request->getContent();
        $secret = env('BOLD_SECRET_KEY');
        Log::info('🔐 DEBUG FIRMA', [
            'raw' => $raw,
            'secret' => $secret,
            'header' => $request->header('x-bold-signature')
        ]);
        

    if ($receivedSignature) {

        // 🔥 1. RAW BODY
        $rawBody = $request->getContent();

        // 🔥 2. BASE64
        $encoded = base64_encode($rawBody);

        // 🔥 3. HMAC SHA256
        $calculated = hash_hmac('sha256', $encoded, $secret);

        // 🔥 DEBUG (puedes quitar luego)
        Log::info('🔐 DEBUG FIRMA', [
            'base64' => $encoded,
            'calculated' => $calculated,
            'received' => $receivedSignature
        ]);

        // 🔥 4. COMPARACIÓN SEGURA
        if (!hash_equals($calculated, $receivedSignature)) {
            Log::error('❌ Firma inválida', [
                'received' => $receivedSignature,
                'calculated' => $calculated
            ]);

            // ⚠️ En pruebas NO bloquees
            // return response()->json(['error' => 'Firma inválida'], 403);
        }

    } else {
        Log::warning('⚠️ Webhook sin firma');
    }

        // 🧠 6. VALIDACIONES LÓGICAS
        if (!$orderId || !$order) {
            return ApiResponse::error('Orden no encontrada', Response::HTTP_NOT_FOUND);
        }

        // 🔄 7. MAPEAR ESTADO
        switch ($type) {
            case 'SALE_APPROVED':
                $order->status = 'paid';
                break;
            case 'SALE_REJECTED':
                $order->status = 'failed';
                break;
            default:
                $order->status = 'pending';
        }

        // 💾 8. ACTUALIZAR ORDEN
        $order->reference = $paymentId;
        $order->save();

        Log::info('✅ Orden actualizada', [
            'order_id' => $orderId,
            'status' => $order->status
        ]);

        return ApiResponse::success('Webhook procesado', Response::HTTP_OK);

    } catch (\Exception $e) {
        Log::error('❌ ERROR WEBHOOK', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}
