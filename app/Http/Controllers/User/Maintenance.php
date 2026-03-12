<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Users\Maintenance as Maintenances;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class Maintenance extends Controller
{

    public function show(string $id)
    {
        try {
            //buscamos el mantenimiento por el user_id
            $maintenance = Maintenances::where('users_id', $id)
                                        //->with(['users'])
                                        ->get();
            if($maintenance->isEmpty()){
                return ApiResponse::error('No se encontraron mantenimientos para este usuario', Response::HTTP_NOT_FOUND);
            }
            return ApiResponse::success('Mantenimiento', Response::HTTP_OK, $maintenance);
          

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
