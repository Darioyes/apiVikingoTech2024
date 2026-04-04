<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Users\OrderBold;

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

        try{
            $orderId = $request->input('metadata.orderId'); 
            $status = $request->input('status'); 
            $reference = $request->input('reference');
            

            $order = OrderBold::where('order_id', $orderId)->first();
            $order->bold_response = $request->all();

            if (!$order) {
                return ApiResponse::error('Orden no encontrada', Response::HTTP_NOT_FOUND);
            }

            // 🧠 Mapear estado
            switch ($status) {
                case 'approved':
                    $order->status = 'paid';
                    break;
                case 'rejected':
                    $order->status = 'failed';
                    break;
                default:
                    $order->status = 'pending';
            }

            $order->reference = $reference;
            $order->save();

            $receivedSignature = $request->header('x-bold-signature');
            $secret = env('BOLD_SECRET_KEY');

            $calculated = hash('sha256', json_encode($request->all()) . $secret);

            

            if ($receivedSignature !== $calculated) {
                return ApiResponse::error('Firma inválida', Response::HTTP_FORBIDDEN);
            }

            return ApiResponse::success('Webhook procesado', Response::HTTP_OK);

        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

       
    }
}
