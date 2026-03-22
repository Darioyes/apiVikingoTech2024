<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\ShoopingCart as ShoopingCartModel;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ShoopingCart\CreateShoopingCart;
use App\Http\Requests\ShoopingCart\UpdateShoopingCart;

class ShoopingCart extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay nada en el carrito de compras se muestra un mensaje de que el carrito de compras esta vacio
            if(ShoopingCartModel::count() === 0) {
                return ApiResponse::success('No tiene productos en el carrito de compras', Response::HTTP_OK, []);
            }
            $shoopingCart = ShoopingCartModel::with(['product', 'user'])
                                                ->orderBy('id', 'asc')
                                                ->get();
            return ApiResponse::success('Carrito de compras obtenido correctamente', Response::HTTP_OK, $shoopingCart);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener el carrito de compras', Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Carrito de compras no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateShoopingCart $request)
    {
        try {
            $shoopingCart = ShoopingCartModel::create($request->validated());
            return ApiResponse::success('Producto agregado al carrito de compras correctamente', Response::HTTP_CREATED, $shoopingCart);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al agregar el producto al carrito de compras', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $shoopingCart = ShoopingCartModel::with(['product', 'user'])->findOrFail($id);
            return ApiResponse::success('Carrito de compras obtenido correctamente', Response::HTTP_OK, $shoopingCart);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Carrito de compras no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoopingCart $request, string $id)
    {
        try {
            $shoopingCart = ShoopingCartModel::findOrFail($id);
            $shoopingCart->update($request->validated());
            return ApiResponse::success('Carrito de compras actualizado correctamente', Response::HTTP_OK, $shoopingCart);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Carrito de compras no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el carrito de compras', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $shoopingCart = ShoopingCartModel::findOrFail($id);
            $shoopingCart->delete();
            return ApiResponse::success('Carrito de compras eliminado correctamente', Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Carrito de compras no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al eliminar el carrito de compras', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
