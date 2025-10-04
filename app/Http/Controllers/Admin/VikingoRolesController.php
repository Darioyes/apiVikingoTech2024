<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\VikingoRoles as VikingoRolesAdmin;
use Illuminate\Http\Request;

use App\Http\Requests\Roles\CreateRoles;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VikingoRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay roles devolvemos el json vacio
            if(VikingoRolesAdmin::count() === 0){
                return ApiResponse::success('No hay roles creados', Response::HTTP_OK, []);
            }
            $roles = VikingoRolesAdmin::orderBy('name_admin', 'asc')
            ->paginate(10);
            return ApiResponse::success('Roles', Response::HTTP_OK, $roles);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoles $request)
    {
        try {
            //creamos el rol
            $role = new VikingoRolesAdmin($request->input());
            //dd($role);
            //obtenemos el nombre del rol
            $roleName = $role->name_admin;
            //guardamos el rol
            $role->save();
            //retornamos la respuesta
            return ApiResponse::success('Rol de '.$roleName.' creado', Response::HTTP_CREATED, $role);

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
            //buscamos el rol
            $role = VikingoRolesAdmin::findOrFail($id);
            return ApiResponse::success('Rol', Response::HTTP_OK, $role);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Rol no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            //buscamos el rol
            $role = VikingoRolesAdmin::findOrFail($id);
            //actualizamos el rol
            $role->update($request->input());
            //obtenemos el nombre del rol
            $roleName = $role->name_admin;
            //retornamos la respuesta
            return ApiResponse::success('Rol de '.$roleName.' actualizado', Response::HTTP_OK, $role);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Rol no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //buscamos el rol
            $role = VikingoRolesAdmin::findOrFail($id);
            //obtenemos el nombre del rol
            $roleName = $role->name_admin;
            //eliminamos el rol
            $role->delete();
            //retornamos la respuesta
            return ApiResponse::success('Rol de '.$roleName.' eliminado', Response::HTTP_OK, $role);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Rol no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
