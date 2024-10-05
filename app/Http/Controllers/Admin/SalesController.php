<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\sales;
use App\Models\Admin\transactions;

use App\Http\Requests\Sales\CreateSales;
use App\Http\Requests\Sales\UpdateSales;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Admin\products as ProductsAdmin;


class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            if(sales::count() == 0){
                return ApiResponse::success('No hay ventas', Response::HTTP_OK, []);

            }

            $sales = sales::with(['users','products'])
                        ->orderBy('created_at', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de ventas', Response::HTTP_OK, $sales);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSales $request)
    {
        try {
            //validamos si hay stock suficiente para la venta
            if(!$this->validateStock($request->input('product_id'), $request->input('amount'))){
                return ApiResponse::error('No hay stock suficiente para la venta', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            //instanciamos el modelo de transactions para guardar la venta
            $transaction = new transactions();

            //obtenemos el amount de la venta
            $amount = $request->input('amount');
            //instanciamos el modelo para crear la venta
            $sale = new sales($request->input());
            //traemos el id del producto
            $product_id = $request->input('product_id');
            //instanciamos el producto
            $product = ProductsAdmin::findOrFail($product_id);
            //resta el amount(cantidad) de la venta al stock del producto
            $product->stock = $product->stock - $amount;
            //obtenemos el precio del producto
            $price = $product->sale_price;
            //obtenemos el costo del producto
            $cost = $product->cost_price;
            //multiplicamos el precio del producto por el amount de la venta
            $sale_total = $price * $amount;
            //multiplicamos el costo del producto por el amount de la venta
            $cost_total = $cost * $amount;
            //guardamos el sale_total en la venta
            $sale->sale_total = $sale_total;
            //guardamos el cost_total en la venta
            $sale->cost_total = $cost_total;
            //guardamos el producto
            $product->save();
            $sale->save();
            //obtenemos el id de la venta
            $sales_id = $sale->id;
            //guardamos el id de la venta en la tabla transactions
            $transaction->sales_id = $sales_id;
            //guardamos los cambios en la tabla transactions
            $transaction->save();

            return ApiResponse::success('Venta exitosa', Response::HTTP_CREATED, $sale);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $sale = sales::with(['users','products'])
                        ->findOrFail($id);

            return ApiResponse::success('Venta', Response::HTTP_OK, $sale);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSales $request, $id)
    {
        try {
             //validamos si hay stock suficiente para la venta
             if(!$this->validateStock($request->input('product_id'), $request->input('amount'))){
                return ApiResponse::error('No hay stock suficiente para la venta', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            //treemos la venta que se quiere actualizar
            $sale = sales::findOrFail($id);
            //traemos el amount de la venta
            $amountActual = $sale->amount;
            //traemos el amount del request
            $amountModify = $request->input('amount');
            //traemos el id del producto
            $product_id = $sale->product_id;
            //traemos el producto
            $product = ProductsAdmin::findOrFail($product_id);
            //traemos el stock del producto
            $stock = $product->stock;
            //validamos si el amountActual es mayor al amountModify
            if($amountActual > $amountModify){
                //sumamos al stock del producto la diferencia de amountActual y amountModify
                $product->stock = $stock + ($amountActual - $amountModify);
                //sumamos al stock del producto la diferencia de amountActual y amountModify
            }else if($amountActual < $amountModify){
                //restamos al stock del producto la diferencia de amountModify y amountActual
                $product->stock = $stock - ($amountModify - $amountActual);
            }
            //guardamos el producto
            $product->save();
            //multiplicamos el precio del producto por el amount de la venta
            $sale_total = $product->sale_price * $amountModify;
            //multiplicamos el costo del producto por el amount de la venta
            $cost_total = $product->cost_price * $amountModify;
            //guardamos el sale_total en la venta
            $request->merge(['sale_total' => $sale_total]);
            //guardamos el cost_total en la venta
            $request->merge(['cost_total' => $cost_total]);
            //actualizamos la venta
            $sale->update($request->input());

            return ApiResponse::success('Venta actualizada', Response::HTTP_OK, $sale);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //instanciamos el modelo transactions
            $transaction = new transactions();

            $sale = sales::findOrFail($id);
            //traemos el amount de de la venta
            $amount = $sale->amount;
            //traemos el id del producto
            $product_id = $sale->product_id;
            //traemos el producto
            $product = ProductsAdmin::findOrFail($product_id);
            //traemos el stock del producto
            $stock = $product->stock;
            //sumamos al producto en el stock el amoutn de la venta
            $product->stock = $stock + $amount;
            //guardamos el producto
            $product->save();
            //eliminamos la venta de la tabla transactions
            $transaction->where('sales_id', $id)->delete();
            //eliminamos la venta
            $sale->delete();

            return ApiResponse::success('Venta eliminada', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    //funcion para validar si hay stock suficiente para la venta
    private function validateStock($product_id, $amount){
        //traemos el producto
        $product = ProductsAdmin::findOrFail($product_id);
        //traemos el stock del producto
        $stock = $product->stock;
        //validamos si el stock es menor al amount
        if($stock < $amount){
            return false;
        }
        return true;
    }
}
