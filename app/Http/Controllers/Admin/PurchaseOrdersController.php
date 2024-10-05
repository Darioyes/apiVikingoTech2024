<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\purchaseOrders;
use App\Models\Admin\products;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseOrders\CreatePurchaseOrders;
use App\Http\Requests\PurchaseOrders\UpdatePurchaseOrders;
use App\Models\Admin\transactions;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchaseOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if(purchaseOrders::count() == 0){
                return ApiResponse::success('No hay ordenes de compra', Response::HTTP_OK, []);
            }

            $purchaseOrders = purchaseOrders::with(['suppliers','products'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            return ApiResponse::success('Lista de ordenes de compra', Response::HTTP_OK, $purchaseOrders);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePurchaseOrders $request)
    {
        try {
            //instanciamos el modelo de purchaseOrders para guardar la orden de compra que esta en el request
            $purchaseOrders = new purchaseOrders($request->input());
            //inicializamos el modelo de transactions para guardar la orden de compra
            $transactions = new transactions();
            //instanciamos el modelo de products para guardar el stock y el costo del producto
            $products = new products();
            //guardamos la orden de compra
            $purchaseOrders->save();
            //obtenemos el id del producto
            $products_id = $request->input('products_id');
            //obtenemos el amoutn de la orden de compra
            $amount = $request->input('amount');
            //obtenemos purchase de la orden de compra
            $purcharse = $request->input('purcharse');
            //dividimos el purchase entre el amount para obtener el costo del producto
            $costNew = $purcharse / $amount;
            //traemos el costo del producto de la base de datos
            $costActual = $products->where('id', $products_id)->first()->cost_price;
            if($costActual > 0){
                //sumamos el costo del producto con el costo de la orden de compra y la dividimos por 2
                $costSave = ($costNew + $costActual) / 2;
            }else{
                //si el costo del producto es 0 guardamos el costo de la orden de compra
                $costSave = $costNew;
            }
            //actualizamos el costo del producto
            $products->where('id', $products_id)->update(['cost_price' => $costSave]);
            //obtenemos el stock del producto
            $stockActual = $products->where('id', $products_id)->first()->stock;
            //sumamos el stock con el amount
            $stockNew = $stockActual + $amount;
            //actualizamos el stock del producto
            $products->where('id', $products_id)->update(['stock' => $stockNew]);
            //obtenemos el id de la orden de compra
            $purchaseOrders_id = $purchaseOrders->id;
            //guardamos el id de la orden de compra en la tabla transactions
            $transactions->purchase_orders_id = $purchaseOrders_id;
            //guardamos la transacción
            $transactions->save();


            return ApiResponse::success('Orden de compra creada', Response::HTTP_CREATED, $purchaseOrders);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $purchaseOrders = purchaseOrders::with(['suppliers','products'])
                        ->where('id', $id)
                        ->first();

            return ApiResponse::success('Orden de compra', Response::HTTP_OK, $purchaseOrders);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseOrders $request, $id)
    {
        try {
            $purchaseOrders = purchaseOrders::findOrFail($id);
            //obtenemos el amoutn actual de la orden de compra
            $amountActual = $purchaseOrders->amount;
            //actualizamos la orden de compra
            $purchaseOrders->update($request->input());
            //instanciamos el modelo de products para guardar el stock y el costo del producto
            $products = new products();
            //obtenemos el id del producto
            $products_id = $request->input('products_id');
            //obtenemos el amoutn de la orden de compra
            $amount = $request->input('amount');
            //obtenemos purchase de la orden de compra
            $purcharse = $request->input('purcharse');
            //dividimos el purchase entre el amount para obtener el costo del producto
            $costNew = $purcharse / $amount;
            //traemos el costo del producto de la base de datos
            $costActual = $products->where('id', $products_id)->first()->cost_price;
            if($costActual > 0){
                //sumamos el costo del producto con el costo de la orden de compra y la dividimos por 2
                $costSave = ($costNew + $costActual) / 2;
            }else{
                //si el costo del producto es 0 guardamos el costo de la orden de compra
                $costSave = $costNew;
            }
            //actualizamos el costo del producto
            $products->where('id', $products_id)->update(['cost_price' => $costSave]);
            //obtenemos el stock del producto
            $stockActual = $products->where('id', $products_id)->first()->stock;
            //validamos si el amount es mayor al amountActual
            if($amount > $amountActual){
                //restamos el amount con el amountActual
                $amountNew = $amount - $amountActual;
                //sumamos el stock con el amountNew
                $stockNew = $stockActual + $amountNew;
            }else{
                //restamos el amountActual con el amount
                $amountNew = $amountActual - $amount;
                //restamos el stock con el amountNew
                $stockNew = $stockActual - $amountNew;
            }

            //actualizamos el stock del producto
            $products->where('id', $products_id)->update(['stock' => $stockNew]);



            return ApiResponse::success('Orden de compra actualizada', Response::HTTP_OK, $purchaseOrders);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseOrders = purchaseOrders::findOrFail($id);
            //instanciamos el modelo de products para guardar el stock y el costo del producto
            $products = new products();
            //instanciamos el modelo transactions
            $transaction = new transactions();
            //obtenemos el id del producto
            $products_id = $purchaseOrders->products_id;
            //obtenemos el amoutn de la orden de compra
            $amount = $purchaseOrders->amount;
            //obtenemos el stock del producto
            $stockActual = $products->where('id', $products_id)->first()->stock;
            //restamos el stock con el amount
            $stockNew = $stockActual - $amount;
            //actualizamos el stock del producto
            $products->where('id', $products_id)->update(['stock' => $stockNew]);
            //para sacar en nuevo costo del producto debo recorrer todas las ordenes de compra y la cantidad de amount que tiene el id producto
            $purchaseOrdersAll = purchaseOrders::where('products_id', $products_id)->get();
            if($purchaseOrdersAll->count() == 1){
                //si solo hay una orden de compra eliminamos el costo del producto y el stock
                $products->where('id', $products_id)->update(['cost_price' => 0, 'stock' => 0]);
                //eliminamos la orden de compra
                $purchaseOrders->delete();
                //eliminamos la transacción
                $transaction->where('purchase_orders_id', $id)->delete();
                return ApiResponse::success('Orden de compra eliminada', Response::HTTP_OK, []);
            }
            $cost = 0;
            $amountTotal = 0;
            foreach($purchaseOrdersAll as $purchaseOrder){
                //sumamos el purchase de la orden de compra
                $cost = $cost + $purchaseOrder->purcharse;
                //sumamos el amount de la orden de compra
                $amountTotal = $amountTotal + $purchaseOrder->amount;
            }
            //dividimos el costo entre el amountTotal
            $costNew = $cost / $amountTotal;
            //actualizamos el costo del producto
            $products->where('id', $products_id)->update(['cost_price' => $costNew]);

            //eliminamos la orden de compra
            $purchaseOrders->delete();
            //eliminamos la transacción
            $transaction->where('purchase_orders_id', $id)->delete();

            return ApiResponse::success('Orden de compra eliminada', Response::HTTP_OK, []);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
