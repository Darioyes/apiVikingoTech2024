<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\Carousel as CarouselUsers;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class Carousel extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     try {
            //si no hay productos devolvemos el json vacio
            if(CarouselUsers::count() === 0){
                return ApiResponse::success('No hay carrusel creado', Response::HTTP_OK, []);
            }
            $carousel = CarouselUsers::with(['product:id,name,description,slug,stock,sale_price,image1'])
                                        ->orderBy('order', 'asc')
                                        ->get();
            return ApiResponse::success('Productos', Response::HTTP_OK, $carousel);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
