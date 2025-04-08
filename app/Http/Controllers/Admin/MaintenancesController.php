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
//esto se importa para escribir sql crudo
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $maintenances = maintenances::with(['users:id,name,lastname'])
                                        ->orderBy('updated_at', 'desc')
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

    /**
     * Search maintenance by name or description.
     * @param string $maintenance Maintenance name or description (required).
     * @return JsonResponse
     */

    public function searchMaintenance($maintenance)
    {
        try {
            //buscamos el mantenimiento por nombre o descripción
            $maintenance = maintenances::with(['users'])
                                        ->where('product', 'LIKE', "%$maintenance%")
                                        ->orWhere('description', 'LIKE', "%$maintenance%")
                                        ->orWhere('advance', 'LIKE', "%$maintenance%")
                                        ->orWhereHas('users', function($query) use ($maintenance) {
                                            $query->where('name', 'LIKE', "%$maintenance%")
                                                  ->orWhere('lastname', 'LIKE', "%$maintenance%");
                                        })
                                        ->paginate(10);
            return ApiResponse::success('Mantenimiento', Response::HTTP_OK, $maintenance);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * query's to get the maintenance
     */

     public function getInfoBasicMaintenance(){
        try {
            //pasamos a español la base de datos
            DB::statement("SET lc_time_names = 'es_ES';");
            //realizamos consulta para validar el promedio de price
            $average_price = maintenances::avg('price');
            //realizamos una consulta para obtener el total de manteminientos
            $total_maintenances = maintenances::count();
            //Realizar consulta para obtener el total de mantenimientos por mes
            $total_maintenances_mes = DB::select("SELECT COUNT(*) as total, MONTHNAME(created_at) AS month FROM maintenances GROUP BY MONTHNAME( created_at);");
            //realizamos una consulta para obtener el total de mantenimientos activos
            $total_maintenances_progress = DB::select("SELECT advance, COUNT(*) as total FROM maintenances GROUP BY advance;");
            //retornamos la suma de ventas por mes
            $total_price_mes = DB::select("SELECT SUM(price) as total_price, MONTHNAME(created_at) AS month FROM maintenances GROUP BY MONTHNAME( created_at);");
            //retornamos el promedio de ventas por mes
            $average_price_mes = DB::select("SELECT AVG(price) as total_average, MONTHNAME(created_at) AS month FROM maintenances GROUP BY MONTHNAME( created_at);");
            //mantenimientos que estan por arriba del promedio
            $above_average = DB::select("SELECT product, description, price FROM maintenances WHERE price > (SELECT AVG(price) as average FROM maintenances);");
            //realizar consulta para obtener el total de reparados y no reparados
            $total_maintenances_repaired = DB::select("SELECT repaired, COUNT(*) as total FROM maintenances GROUP BY repaired;");
            //realizar consulta de las garantias
            $total_maintenances_warranty = DB::select("SELECT warranty, COUNT(*) as total FROM maintenances GROUP BY warranty;");

            return ApiResponse::success('Información básica de mantenimientos', Response::HTTP_OK,
            [
            'average_price' => $average_price,
            'total_maintenances' => $total_maintenances,
            'total_maintenances_mes' => $total_maintenances_mes,
            'total_maintenances_progress' => $total_maintenances_progress,
            'total_price_mes' => $total_price_mes,
            'average_price_mes' => $average_price_mes,
            'above_average' => $above_average,
            'total_maintenances_repaired' => $total_maintenances_repaired,
            'total_maintenances_warranty' => $total_maintenances_warranty
            ]);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        }
    }
    public function getMaintenanceProgress($day){
        try{
            $startDay = Carbon::now()->subDays($day);

            //pasamos a español la base de datos
            DB::statement("SET lc_time_names = 'es_ES';");
            //traemos un group by de los mantenimientos desde el dia actual menos 7 dias atras
            $total_maintenances_progress = DB::select("SELECT advance, COUNT(*) as total FROM maintenances WHERE created_at >= '$startDay' GROUP BY advance;");

            // //realizamos una consulta para obtener el total de mantenimientos activos
            // $total_maintenances_progress = DB::select("SELECT advance, COUNT(*) as total FROM maintenances GROUP BY advance;");
            return ApiResponse::success('Mantenimientos activos', Response::HTTP_OK, $total_maintenances_progress);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        }
    }
}
