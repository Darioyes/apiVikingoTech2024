<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\suppliers;
use App\Models\Admin\purchaseOrders;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Suppliers\UpdateSuppliers;
use App\Http\Requests\Suppliers\CreateSuppliers;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            if(suppliers::count() == 0){
                return ApiResponse::success('No hay proveedores', Response::HTTP_OK, []);

            }

            $suppliers = suppliers::with(['cities:id,city'])
                        ->orderBy('name', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de proveedores', Response::HTTP_OK, $suppliers);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSuppliers $request)
    {
        try {

            $supplier = new suppliers($request->input());
            $supplier->save();

            return ApiResponse::success('Proveedor creado', Response::HTTP_CREATED, $supplier);

        } catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $supplier = suppliers::with(['cities:id,city'])
                        ->findOrFail($id);


            return ApiResponse::success('Proveedor', Response::HTTP_OK, $supplier);

        }  catch (ModelNotFoundException $e) {
            return ApiResponse::error('Ciudad no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuppliers $request, $id)
    {
        try {

            $suppliers= suppliers::with(['cities:id,city'])->findOrFail($id);


            $suppliers->update($request->input());


            return ApiResponse::success('Proveedor actualizado', Response::HTTP_OK, $suppliers);

        } catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $suppliers = suppliers::findOrFail($id);
            $suppliers->delete();

            return ApiResponse::success('Proveedor eliminado', Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Proveedor no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function searchSuppliers($name){
        try {

            $suppliers = suppliers::with(['cities:id,city'])
                        ->where('name', 'like', '%'.$name.'%')
                        ->orWhere('email', 'like', '%'.$name.'%')
                        ->orWhere('nit', 'like', '%'.$name.'%')
                        ->orderBy('name', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de proveedores', Response::HTTP_OK, $suppliers);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * info basica de proveedores
     */

     public function infoBasicSuppliers(){
        try{
            //total de proveedores
            $totalSuppliers = suppliers::count();
            //sumar el total compras a proveedores
            $totalPurchases =  DB::table('purchase_orders')
                                ->select(DB::raw('SUM(amount) as total'))
                                ->first();
            $totalbuys =  DB::table('purchase_orders')
                                ->select(DB::raw('SUM(purcharse) as total'))
                                ->first();


            return ApiResponse::success('Información básica de proveedores', Response::HTTP_OK, [
                'totalSuppliers' => $totalSuppliers,
                'totalPurchases' => $totalPurchases,
                'totalbuys' => $totalbuys
            ]);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
     }
}
