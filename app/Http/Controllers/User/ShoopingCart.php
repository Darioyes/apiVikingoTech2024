<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\ShoopingCart as ShoopingCartModel;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ShoopingCart\CreateShoopingCart;
use App\Http\Requests\ShoopingCart\UpdateShoopingCart;
use App\Models\Users\Products;

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
            $shoopingCart = ShoopingCartModel::with(['product:id,name,slug,reference,description,stock,sale_price,image1,image2,image3,image4,image5,color', 'user:id,name,email'])
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
     * funcion para obtener el carrito de compras por usuario
     */
    public function getShoopingCartByUser($userId){
        try {
            $shoopingCart = ShoopingCartModel::with(['product:id,name,slug,reference,description,stock,sale_price,image1,image2,image3,image4,image5,color', 'user:id,name,email'])
                                                ->where('user_id', $userId)
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
            // 1. Buscamos el producto para verificar stock real
            $product = Products::findOrFail($request->product_id);
            $userId = auth()->id();

            // 2. Buscamos si ya existe este producto en el carrito del usuario
            $shoopingCart = ShoopingCartModel::where('user_id', $userId)
                ->where('product_id', $request->product_id)
                ->first();

            // 3. Calculamos la cantidad total que resultaría
            $totalAmount = $shoopingCart 
                ? $shoopingCart->amount + $request->amount 
                : $request->amount;

            // 4. Validamos contra el stock disponible
            if ($totalAmount > $product->stock) {
                return ApiResponse::error(
                    "Stock insuficiente. Solo quedan {$product->stock} unidades disponibles, porfavor revisa tu selección nuevamente.", 
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($shoopingCart) {
                // Actualizamos registro existente
                $shoopingCart->amount = max(1, $totalAmount);
                $shoopingCart->save();
                return ApiResponse::success('Cantidad actualizada con éxito', Response::HTTP_OK, $shoopingCart);
            }

            // Creamos nuevo registro si no existía
            $newCartItem = new ShoopingCartModel($request->validated());
            $newCartItem->user_id = $userId;
            $newCartItem->save();

            return ApiResponse::success('Producto agregado al carrito', Response::HTTP_CREATED, $newCartItem);

        } catch (\Exception $e) {
            return ApiResponse::error('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $shoopingCart = ShoopingCartModel::with(['product:name,slug,reference,description,stock,sale_price,image1,image2,image3,image4,image5,color', 'user'])->findOrFail($id);
            return ApiResponse::success('Carrito de compras obtenido correctamente', Response::HTTP_OK, $shoopingCart);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Carrito de compras no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoopingCart $request, $id)
    {
        try {
            $shoopingCart = ShoopingCartModel::findOrFail($id);
            $shoopingCart->update($request->input());
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
