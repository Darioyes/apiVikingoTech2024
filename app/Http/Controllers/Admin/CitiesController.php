<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\cities as CitiesAdmin;

use App\Http\Requests\Cities\CreateCities;
use App\Http\Requests\Cities\UpdateCities;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay ciudades devolvemos el json vacio
            if(CitiesAdmin::count() === 0){
                return ApiResponse::success('No hay ciudades creadas', Response::HTTP_OK, []);
            }
            $cities = CitiesAdmin::orderBy('city', 'asc')
            ->paginate(10);
            return ApiResponse::success('Ciudades', Response::HTTP_OK, $cities);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCities $request)
    {
        try {
            //creamos la ciudad
            $city = new CitiesAdmin($request->input());
            //dd($city);
            //obtenemos el nombre de la ciudad
            $cityName = $city->city;
            //guardamos la ciudad
            $city->save();
            //retornamos la respuesta
            return ApiResponse::success('Ciudad de '.$cityName.' creada', Response::HTTP_CREATED, $city);

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cities = CitiesAdmin::findOrFail($id);

            return ApiResponse::success('Ciudad', Response::HTTP_OK, $cities);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Ciudad no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCities $request, $id)
    {
        try {
            //buscamos la ciudad
            $city = CitiesAdmin::findOrFail($id);
            //obtenemos el nombre de la ciudad
            $cityName = $city->city;
            //actualizamos la ciudad
            if($city->isDirty()){
                $city->update($request->input());
                //retornamos la respuesta
                return ApiResponse::success('Ciudad de '.$cityName.' actualizada', Response::HTTP_OK, $city);
            }
            return ApiResponse::error('No realizo ningún cambio', Response::HTTP_BAD_REQUEST);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Ciudad no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //buscamos la ciudad
            $city = CitiesAdmin::findOrFail($id);
            //obtenemos el nombre de la ciudad
            $cityName = $city->city;
            //eliminamos la ciudad
            $city->delete();
            //retornamos la respuesta
            return ApiResponse::success('Ciudad de '.$cityName.' eliminada', Response::HTTP_OK, $city);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Ciudad no encontrada', Response::HTTP_NOT_FOUND);
        }
    }
}
