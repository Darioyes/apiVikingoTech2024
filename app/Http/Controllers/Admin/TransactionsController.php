<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\transactions;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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


}
