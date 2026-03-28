<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\Sales;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\SaleResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Sales\CreateSales;
use App\Models\Users\Transaction;
use App\Models\Users\Products;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(CreateSales $request)
    {
        try {
            //validamos si hay stock suficiente para la venta
            if(!$this->validateStock($request->input('product_id'), $request->input('amount'),false,1)){
                return ApiResponse::error('No hay stock suficiente para la venta', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            //instanciamos el modelo de transactions para guardar la venta
            $transaction = new Transaction();

            //obtenemos el amount de la venta
            $amount = $request->input('amount');
            //instanciamos el modelo para crear la venta
            $sale = new sales($request->input());
            //traemos el id del producto
            $product_id = $request->input('product_id');
            //instanciamos el producto
            $product = Products::findOrFail($product_id);
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
    public function show($idUser)  
    {
        try {
            $sales = Sales::with(['product:id,name,slug,sale_price,image1'])
                            ->where('user_id', $idUser)
                            ->orderBy('id', 'asc')
                            ->get();
            return ApiResponse::success('Ventas obtenidas exitosamente', Response::HTTP_OK, SaleResource::collection($sales)); 
  
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve sales: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);   
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

        //funcion para validar si hay stock suficiente para la venta
    private function validateStock($product_id, $amount, $modify,$idSale){
        //traemos el producto
        $product = Products::findOrFail($product_id);
        //traemos el stock del producto
        $stock = $product->stock;

        if($modify == true) { 
            $sale = sales::findOrFail($idSale);
            $amountSale = $sale->amount;
            $modifyAmount = $amountSale + $stock;
            //dd($modifyAmount);
            if($modifyAmount < $amount){
                return false;
            }
            return true;
        }else{
            //validamos si el stock es menor al amount
            if($stock < $amount){
                return false;
            }
            return true;
        }
    }
}
