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

        Log::info('🔥 WEBHOOK BOLD', $request->all());

        $payload = $request->all();

        // 🔐 1. VALIDAR FIRMA (PRIMERO)
        $receivedSignature = $request->header('x-bold-signature');
        $secret = env('BOLD_SECRET_KEY');

        $calculated = hash('sha256', json_encode($payload) . $secret);

        if ($receivedSignature !== $calculated) {
            Log::error('❌ Firma inválida');
            return ApiResponse::error('Firma inválida', Response::HTTP_FORBIDDEN);
        }

        // 🧠 2. EXTRAER DATOS CORRECTOS DE BOLD
        $type = $payload['type'] ?? null;

        $orderId = data_get($payload, 'data.metadata.reference'); // 🔥 ESTE ES TU ID
        $paymentId = data_get($payload, 'data.payment_id');

        if (!$orderId) {
            return ApiResponse::error('No viene reference en metadata', Response::HTTP_BAD_REQUEST);
        }

        // 🔎 3. BUSCAR ORDEN
        $order = OrderBold::where('order_id', $orderId)->first();

        if (!$order) {
            Log::error('❌ Orden no encontrada', ['order_id' => $orderId]);
            return ApiResponse::error('Orden no encontrada', Response::HTTP_NOT_FOUND);
        }

        // 🔄 4. MAPEAR ESTADO REAL DE BOLD
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

        // 💾 5. GUARDAR DATOS
        $order->reference = $paymentId;
        $order->bold_response = $payload;
        $order->save();

        Log::info('✅ Orden actualizada', [
            'order_id' => $orderId,
            'status' => $order->status
        ]);

        return ApiResponse::success('Webhook procesado', Response::HTTP_OK);

    } catch (\Exception $e) {
        Log::error('❌ ERROR WEBHOOK', ['error' => $e->getMessage()]);
        return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}
