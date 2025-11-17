<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\Products as ProductsUser;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay productos devolvemos el json vacio
            if (ProductsUser::count() === 0) {
                return ApiResponse::success('No hay productos creados', Response::HTTP_OK, []);
            }
            //traer solo las columnas id, name, slug, sale_price, image1 ordenados por name ascendente
            $products = ProductsUser::orderBy('name', 'asc')->get([
                'id', 
                'name', 
                'slug', 
                'reference', 
                'description',
                'stock', 
                'sale_price',
                'visible', 
                'image1',
                'image2',
                'image3',
                'image4',
                'image5',
                'color',
                'categories_products_id',
            ]);
                return ApiResponse::success('Productos', Response::HTTP_OK, $products);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    //buscar producto por slug
    public function show($slug)
    {
        try {
            $product = ProductsUser::with(['categoriesProducts:id,name'])->where('slug', $slug)->first( [
                'id', 
                'name', 
                'slug', 
                'reference', 
                'description',
                'stock', 
                'sale_price',
                'visible', 
                'image1',
                'image2',
                'image3',
                'image4',
                'image5',
                'color',
                'categories_products_id',
            ]);
            if (!$product) {
                return ApiResponse::error('Producto no encontrado', Response::HTTP_NOT_FOUND);
            }
            return ApiResponse::success('Producto encontrado', Response::HTTP_OK, $product);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }
}