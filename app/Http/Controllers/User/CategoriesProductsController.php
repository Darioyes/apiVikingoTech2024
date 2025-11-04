<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\CategoriesProducts as CategoriesProductsUser;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoriesProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
        //si no hay usuarios devolvemos el json vacio
        if(CategoriesProductsUser::count() === 0){
            return ApiResponse::success('No hay categorias creadas', Response::HTTP_OK, []);
        }
        $categories = CategoriesProductsUser::orderBy('name', 'asc')->get();
        return ApiResponse::success('Categorias de productos', Response::HTTP_OK, $categories);

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

   
}
