<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Users\Cities as CitiesFront;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class Cities extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         try {
            //si no hay ciudades devolvemos el json vacio
            if(CitiesFront::count() === 0){
                return ApiResponse::success('No hay ciudades creadas', Response::HTTP_OK, []);
            }
            $cities = CitiesFront::orderBy('city', 'asc')
            ->get();
            return ApiResponse::success('Ciudades', Response::HTTP_OK, $cities);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function show(string $id)
    {
        //
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
