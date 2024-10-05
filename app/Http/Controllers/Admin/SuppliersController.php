<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\suppliers;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Suppliers\UpdateSuppliers;
use App\Http\Requests\Suppliers\CreateSuppliers;

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
}
