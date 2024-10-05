<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\maintenances;
use App\Models\Admin\transactions;

use App\Http\Requests\Maintenances\CreateMaintenances;
use App\Http\Requests\Maintenances\UpdateMaintenances;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class MaintenancesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay mantenimientos devolvemos el json vacio
            if(maintenances::count() === 0){
                return ApiResponse::success('No hay mantenimientos creados', Response::HTTP_OK, []);
            }
            $maintenances = maintenances::with(['users'])
                                        ->orderBy('product', 'asc')
                                        ->paginate(10);
            return ApiResponse::success('Mantenimientos', Response::HTTP_OK, $maintenances);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMaintenances $request)
    {
        try {
            //instanciamos el modelo de transactions para guardar el mantenimiento
            $transaction = new transactions();

            //instanciamos el modelo para crear el mantenimiento
            $maintenance = new maintenances($request->all());

            //subimos la image1 del mantenimiento y guardamos la ruta en la variable path
            $path = $request->file('image1')->store('public/images/maintenances');
            //guardamos la ruta en la base de datos
            $maintenance->image1 = $path;
            //si viene la image2 del mantenimiento y guardamos la ruta en la variable path2
            if($request->hasFile('image2')){
                $path2 = $request->file('image2')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image2 = $path2;
            }
            //si viene la image3 del mantenimiento y guardamos la ruta en la variable path3
            if($request->hasFile('image3')){
                $path3 = $request->file('image3')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image3 = $path3;
            }
            //si viene la image4 del mantenimiento y guardamo la ruta en la variable path4
            if($request->hasFile('image4')){
                $path4 = $request->file('image4')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image4 = $path4;
            }
            //guardamos el mantenimiento
            $maintenance->save();
            //obtenemos el id del mantenimiento
            $maintenances_id = $maintenance->id;
            //guardamos el id del mantenimiento en la tabla transactions
            $transaction->maintenances_id = $maintenances_id;
            //guardamos los cambios en la tabla transactions
            $transaction->save();

            return ApiResponse::success('Mantenimiento creado', Response::HTTP_CREATED, $maintenance);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            //buscamos el mantenimiento por id
            $maintenance = maintenances::with(['users'])
                                        ->findOrFail($id);
            return ApiResponse::success('Mantenimiento', Response::HTTP_OK, $maintenance);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaintenances $request, $id)
    {
        try {
            //buscamos el mantenimiento por id
            $maintenance = maintenances::findOrFail($id);
            $image1 = $maintenance->image1;
            $image2 = $maintenance->image2;
            $image3 = $maintenance->image3;
            $image4 = $maintenance->image4;

            //actualizamos el mantenimiento
            $maintenance->update($request->all());
            //si viene la image1 del mantenimiento y guardamos la ruta en la variable path
            if($request->hasFile('image1')){
                //eliminamos la imagen anterior
                if($image1){
                    Storage::delete($image1);
                }
                $path = $request->file('image1')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image1 = $path;
            }
            //si viene la image2 del mantenimiento y guardamos la ruta en la variable path2
            if($request->hasFile('image2')){
                //eliminamos la imagen anterior
                if($image2){
                    Storage::delete($image2);
                }
                $path2 = $request->file('image2')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image2 = $path2;
            }
            //si viene la image3 del mantenimiento y guardamos la ruta en la variable path3
            if($request->hasFile('image3')){
                //eliminamos la imagen anterior
                if($image3){
                    Storage::delete($image3);
                }
                $path3 = $request->file('image3')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image3 = $path3;
            }
            //si viene la image4 del mantenimiento y guardamos la ruta en la variable path4
            if($request->hasFile('image4')){
                //eliminamos la imagen anterior
                if($image4){
                    Storage::delete($image4);
                }
                $path4 = $request->file('image4')->store('public/images/maintenances');
                //guardamos la ruta en la base de datos
                $maintenance->image4 = $path4;
            }
            //guardamos todo menos las imagenes que ya se guardaron anteriormente
            $maintenance->fill($request->except('image1', 'image2', 'image3', 'image4'));
            //guardamos el mantenimiento
            $maintenance->save();

            return ApiResponse::success('Mantenimiento actualizado', Response::HTTP_OK, $maintenance);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e
        ){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //instanciamos el modelo de transactions
            $transaction = new transactions();
            //buscamos el mantenimiento por id
            $maintenance = maintenances::findOrFail($id);
            //obtenemos el nombre del mantenimiento
            $name = $maintenance->product;
            //obtenemos el id del mantenimiento
            $maintenances_id = $maintenance->id;
            //si existe la imagen1 la eliminamos
            if($maintenance->image1){
                Storage::delete($maintenance->image1);
            }
            //si existe la imagen2 la eliminamos
            if($maintenance->image2){
                Storage::delete($maintenance->image2);
            }
            //si existe la imagen3 la eliminamos
            if($maintenance->image3){
                Storage::delete($maintenance->image3);
            }
            //si existe la imagen4 la eliminamos
            if($maintenance->image4){
                Storage::delete($maintenance->image4);
            }
            //eliminamos el mantenimiento de la tabla transactions
            $transaction->where('maintenances_id', $maintenances_id)->delete();
            //eliminamos el mantenimiento
            $maintenance->delete();

            return ApiResponse::success('Mantenimiento '.$name.' eliminado', Response::HTTP_OK);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        }
    }
}
