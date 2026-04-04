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

        // 🔥 1. RAW REAL (ÚNICA FUENTE DE VERDAD)
        $rawBody = file_get_contents('php://input');

        Log::info('🔥 RAW REAL', [
            'raw' => $rawBody,
            'length' => strlen($rawBody)
        ]);

        // 🔥 2. CONVERTIR A JSON
        $payload = json_decode($rawBody, true);

        Log::info('🔥 WEBHOOK BOLD', $payload);
        Log::info('🔎 HEADERS RAW', $request->headers->all());

        // 🧠 3. VALIDAR FIRMA (ANTES DE TODO)
        $receivedSignature = $request->header('x-bold-signature');
        $secret = env('BOLD_SECRET_KEY');

        if ($receivedSignature) {

            $calculated = hash_hmac('sha256', $rawBody, $secret);

            Log::info('🔐 DEBUG FIRMA', [
                'calculated' => $calculated,
                'received' => $receivedSignature
            ]);

            if (!hash_equals($calculated, $receivedSignature)) {
                Log::error('❌ Firma inválida', [
                    'calculated' => $calculated,
                    'received' => $receivedSignature
                ]);

                return response()->json(['error' => 'Firma inválida'], 403);
            }

        } else {
            Log::warning('⚠️ Webhook sin firma');
            return response()->json(['error' => 'Sin firma'], 400);
        }

        // 🧠 4. EXTRAER DATOS
        $orderId = data_get($payload, 'data.metadata.reference');
        $paymentId = data_get($payload, 'data.payment_id');
        $type = data_get($payload, 'type');

        // 🔎 5. BUSCAR ORDEN
        $order = $orderId ? OrderBold::where('order_id', $orderId)->first() : null;

        if (!$order) {
            Log::warning('⚠️ Orden no encontrada', [
                'order_id' => $orderId
            ]);
            return ApiResponse::error('Orden no encontrada', Response::HTTP_NOT_FOUND);
        }

        // 💾 6. GUARDAR RESPUESTA COMPLETA
        $order->bold_response = [
            'payload' => $payload,
            'raw' => $rawBody,
            'headers' => $request->headers->all()
        ];

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

        // 💾 8. ACTUALIZAR
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
