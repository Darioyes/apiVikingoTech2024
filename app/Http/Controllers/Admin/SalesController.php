<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\sales;
use App\Models\Admin\transactions;
use App\Models\Admin\maintenances;

use App\Http\Requests\Sales\CreateSales;
use App\Http\Requests\Sales\UpdateSales;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Admin\products as ProductsAdmin;
use Illuminate\Support\Facades\DB;
//clase para manejar fechas
use Carbon\Carbon;


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

    public function getInfoBasicSales(){
        try {
            //pasamos a español la base de datos
            DB::statement("SET lc_time_names = 'es_ES';");
            //contar el total de productos vendidos (amount)
            $totalProducts = sales::sum('amount');
            $sales = sales::count();
            $total = sales::sum('sale_total');
            $cost = sales::sum('cost_total');
            //promedio de ventas
            $avgSales = sales::avg('sale_total');
            //ganancias brutas
            $grossProfits = $total - $cost;
            //ventas por mes
            $salesMonth = DB::select("SELECT SUM(sale_total) as sales_month, SUM(cost_total) as cost_total, MONTHNAME(created_at) AS month FROM sales GROUP BY MONTHNAME( created_at);");
            //top 10 de productos más vendidos
            $topProducts = DB::select("SELECT products.name, SUM(sales.amount) as total_amount, SUM(sales.sale_total) AS total_sales FROM sales INNER JOIN products ON sales.product_id = products.id GROUP BY products.name ORDER BY total_sales DESC LIMIT 10;");
            //productos que estan por arriba del promedio
            $above_average = DB::select("SELECT p.name as product, SUM(s.sale_total) as sales_top FROM sales AS s INNER JOIN products AS p ON s.product_id = p.id GROUP BY s.product_id HAVING SUM(s.sale_total) > (SELECT AVG(sale_total) FROM sales);");
            //productos más vendidos
            $topSales = DB::select("SELECT p.name, SUM(s.sale_total) as salestotal FROM sales AS s INNER JOIN products AS p ON s.product_id = p.id GROUP BY s.product_id ORDER BY salestotal DESC LIMIT 10;");
            //productos menos vendidos
            $lessSold = DB::select("SELECT p.name, SUM(s.sale_total) as salestotal FROM sales AS s INNER JOIN products AS p ON s.product_id = p.id GROUP BY s.product_id ORDER BY salestotal ASC LIMIT 10;");
            //cantidad de ventas por aprobar
            $salesToApprove = sales::where('confirm_sale', 'false')->count();

            return ApiResponse::success('Información básica de ventas', Response::HTTP_OK,
            [
                'totalProducts' => $totalProducts,
                'total' => $total,
                'cost' => $cost,
                'sales' => $sales,
                'avgSales' => $avgSales,
                'grossProfits' => $grossProfits,
                'salesMonth' => $salesMonth,
                'topProducts' => $topProducts,
                'above_average' => $above_average,
                'topSales' => $topSales,
                'lessSold' => $lessSold,
                'salesToApprove' => $salesToApprove

            ]);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function getSummary( $day){
        try{
            //usamos la clase Carbon para calcular la fecha de inicio restando los dias proporcionados
            $startDay = Carbon::now()->subDays($day);

            //traemos las ventas desde el dia actual menos 7 dias atras
            $sales = sales::where('created_at', '>=', $startDay)->sum('sale_total');
            //traemos los mantenimientos desde el dia actual menos 7 dias atras
            $maintenances = maintenances::where('created_at', '>=', $startDay)->sum('price');
            //treamos los costos directos desde el dia actual menos 7 dias atras
            $directCosts = DB::table('direct_costs')->where('created_at', '>=', $startDay)->sum('price');
            //traemos los costos indirectos desde el dia actual menos 7 dias atras
            $indirectCosts = DB::table('indirect_costs')->where('created_at', '>=', $startDay)->sum('price');
            return ApiResponse::success('Listado básico',Response::HTTP_OK,[
                'sales' => $sales,
                'maintenances' => $maintenances,
                'directCosts' => $directCosts,
                'indirectCosts' => $indirectCosts
            ]);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
