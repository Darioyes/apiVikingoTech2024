<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transactions;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            if(transactions::count() == 0){
                return ApiResponse::success('No hay transacciones', Response::HTTP_OK, []);
            }
            $transactions = transactions::with(['maintenances','sales','purchase_orders','direct_costs','indirect_costs'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

            return ApiResponse::success('Lista de transacciones', Response::HTTP_OK, $transactions);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $transactions = transactions::with(['maintenances','sales','purchase_orders','direct_costs','indirect_costs'])
                        ->where('id', $id)
                        ->first();

            return ApiResponse::success('Transaccion encontrada', Response::HTTP_OK, $transactions);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function sumaryTransactions(){
        try{

            $totalTrasantionsFamele = DB::select("SELECT COUNT(us.gender) AS genero FROM transactions AS t INNER JOIN sales AS s ON t.sales_id = s.id INNER JOIN users as us ON s.user_id = us.id WHERE us.gender = 'famele';");

            $totalTransactionsMale = DB::select("SELECT COUNT(us.gender) AS genero FROM transactions AS t INNER JOIN sales AS s ON t.sales_id = s.id INNER JOIN users as us ON s.user_id = us.id WHERE us.gender = 'male';");

            $totalTransactions = DB::select("SELECT COUNT(us.gender) AS genero FROM transactions AS t INNER JOIN sales AS s ON t.sales_id = s.id INNER JOIN users as us ON s.user_id = us.id WHERE us.gender = 'other';");

            return ApiResponse::success('Resumen de transacciones', Response::HTTP_OK, [
                'totalTrasantionsFamele' => $totalTrasantionsFamele[0]->genero,
                'totalTransactionsMale' => $totalTransactionsMale[0]->genero,
                'totalTransactions' => $totalTransactions[0]->genero
            ]);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function transactionsUsers(){
        try{
            $transactionsUsers = DB::table('transactions as t')
                ->join('sales as s', 't.sales_id', '=', 's.id')
                ->join('users as us', 's.user_id', '=', 'us.id')
                ->join('products as p', 'p.id', '=', 's.product_id')
                ->select('t.created_at', 'p.name as product', 'p.sale_price as unit_value', 'us.name AS client', 's.amount', 's.sale_total')
                ->orderBy('t.created_at', 'desc')
                ->paginate(10);

            return ApiResponse::success('Movimientos de Compras usuario', Response::HTTP_OK,$transactionsUsers);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function transactionsMaintenance(){

        $transactionsMaintenances = DB::table('transactions as t')
            ->join('maintenances as m', 'm.id', '=', 't.maintenances_id')
            ->join('users as us', 'm.users_id', '=', 'us.id')
            ->select('t.created_at', 'm.product', 'm.price', 'us.name', 'm.warranty')
            ->orderBy('t.created_at', 'desc')
            ->paginate(10);

        return ApiResponse::success('Movimientos de Mantenimientos', Response::HTTP_OK, $transactionsMaintenances);
    }

    public function transactionsPurchaseOrders(){

        $transactionsPurchaseOrders = DB::table('transactions as t')
            ->join('purchase_orders as po', 't.purchase_orders_id', '=', 'po.id')
            ->join('suppliers as s', 'po.suppliers_id', '=', 's.id')
            ->select('t.created_at', 'po.purcharse', 'po.amount', 'po.description', 's.name')
            ->orderBy('t.created_at', 'desc')
            ->paginate(10);

        return ApiResponse::success('Movimientos de Ordenes de compra', Response::HTTP_OK,$transactionsPurchaseOrders);

    }

    public function transactionsDirectCosts(){

        $transactionsDirectCosts = DB::table('transactions as t')
        ->join('direct_costs as dc', 't.direct_costs_id', '=', 'dc.id')
        ->select('t.created_at', 'dc.name', 'dc.amount', 'dc.price')
        ->orderBy('t.created_at', 'desc')
        ->paginate(10);

        return ApiResponse::success('Movimientos de Costos directos', Response::HTTP_OK, $transactionsDirectCosts);

    }

    public function transactionsIndirectCosts(){

        $transactionsIndirectCosts = DB::table('transactions as t')
        ->join('indirect_costs as ic', 't.indirect_costs_id', '=', 'ic.id')
        ->select('t.created_at', 'ic.name', 'ic.amount', 'ic.price')
        ->orderBy('t.created_at', 'desc')
        ->paginate(10);

        return ApiResponse::success('Movimientos de Costos indirectos', Response::HTTP_OK, $transactionsIndirectCosts);

    }

}
