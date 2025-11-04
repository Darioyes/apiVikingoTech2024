<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\Products as ProductsUser;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

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
            $products = ProductsUser::orderBy('name', 'asc')->get();
                return ApiResponse::success('Productos', Response::HTTP_OK, $products);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
}
