<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\Sales;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\SaleResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    public function store(Request $request)
    {
        //
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
}
