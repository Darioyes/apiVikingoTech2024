<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriesDirectCosts\UpdateCategoryDirect;
use App\Http\Requests\CategoriesDirectCosts\CreateCategoryDirect;
use App\Models\Admin\CategoriesDirectCosts as categoriesDirectCostsAdmin;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoriesDirectCostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay categorias de costos directos devolvemos el json vacio
            if(categoriesDirectCostsAdmin::count() === 0){
                return ApiResponse::success('No hay categorias de costos directos creadas', Response::HTTP_OK, []);
            }
            $categories = categoriesDirectCostsAdmin::orderBy('name', 'asc')
            ->paginate(10);
            return ApiResponse::success('Categorias de costos directos', Response::HTTP_OK, $categories);

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryDirect $request)
    {
        try{
            $category = new categoriesDirectCostsAdmin($request->input());
            //obtenemos el nombre de la categoria
            $name = $request->input('name');
            //creamos el slug
            $slug = str_replace(' ', '-', $name);
            //guardamos el slug
            $category->slug = $slug;
            //guardamos la categoria
            $category->save();
            //retornamos la respuesta
            return ApiResponse::success('Categoria creada', Response::HTTP_CREATED, $category);
        }catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $categoriesDirectCosts = categoriesDirectCostsAdmin::findOrFail($id);

            return ApiResponse::success('Categoria de costos directos', Response::HTTP_OK, $categoriesDirectCosts);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria de costos directos no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryDirect $request, $id)
    {
        try{
            $name = $request->input('name');
            $slug = str_replace(' ', '-', $name);
            $categoriesDirectCosts = categoriesDirectCostsAdmin::findOrFail($id);
            $categoriesDirectCosts->slug = $slug;
            if($categoriesDirectCosts->isDirty()){

                $categoriesDirectCosts->update($request->input());
                return ApiResponse::success('Categoria actualizada', Response::HTTP_OK, $categoriesDirectCosts);
            }
            return ApiResponse::error('No realizo ningún cambio', Response::HTTP_UNPROCESSABLE_ENTITY);

        }catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
        //buscamos la categoria por id
        $categoriesDirectCosts = categoriesDirectCostsAdmin::findOrFail($id);
        //obtenemos el nombre de la categoria
        $name = $categoriesDirectCosts->name;
        //eliminamos la categoria
        $categoriesDirectCosts->delete();
        //retornamos la respuesta
        return ApiResponse::success('Categoria '.$name.' eliminada', Response::HTTP_OK);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Error al eliminar la categoria', Response::HTTP_BAD_REQUEST);
        }

    }

    public function searchCategoriesDirectCosts($category)
    {
        try{
            $category = categoriesDirectCostsAdmin::where('name', 'like', "%{$category}%")
                                                    ->orderBy('name', 'asc')
                                                    ->paginate(10);
            return ApiResponse::success('Categorias de costos directos encontradas', Response::HTTP_OK, $category);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Mantenimiento no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    public function getAllCategoriesDirectCosts()
    {
        try{
            $categories = categoriesDirectCostsAdmin::orderBy('name', 'asc')
            ->get();
            return ApiResponse::success('Categorias de costos directos', Response::HTTP_OK, $categories);
        }catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }
}
