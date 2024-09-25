<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\categoriesIndirectCosts as CategoriesIndirectCostsAdmin;
use Illuminate\Http\Request;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\CategoriesIndirectCosts\CreateCategoryIndirect;
use App\Http\Requests\CategoriesIndirectCosts\UpdateCategoryIndirect;

class CategoriesIndirectCostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay categorias de costos indirectos devolvemos el json vacio
            if(CategoriesIndirectCostsAdmin::count() === 0){
                return ApiResponse::success('No hay categorias de costos indirectos creadas', Response::HTTP_OK, []);
            }
            $categories = CategoriesIndirectCostsAdmin::orderBy('name', 'asc')
            ->paginate(10);
            return ApiResponse::success('Categorias de costos indirectos', Response::HTTP_OK, $categories);

        } catch (\Exception $e) {
            return back()->with('error', 'Error, intente nuevamente');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryIndirect $request)
    {
        try{
            $category = new CategoriesIndirectCostsAdmin($request->input());
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
            $category = CategoriesIndirectCostsAdmin::findOrFail($id);
            return ApiResponse::success('Categoria de costo indirecto', Response::HTTP_OK, $category);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria de costo indirecto no encontrada', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryIndirect $request, $id)
    {
        try{
            $name = $request->input('name');
            $slug = str_replace(' ', '-', $name);

            $categoryIndirect = CategoriesIndirectCostsAdmin::findOrFail($id);
            $categoryIndirect->slug = $slug;

            if($categoryIndirect->isDirty()){
                $categoryIndirect->update($request->input());
                return ApiResponse::success('Categoria actualizada', Response::HTTP_OK, $categoryIndirect);
            }
            return ApiResponse::error('No hay cambios en la categoria', Response::HTTP_UNPROCESSABLE_ENTITY);
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
        try {
            $category = CategoriesIndirectCostsAdmin::findOrFail($id);
            $name = $category->name;
            $category->delete();
            return ApiResponse::success('Categoria '.$name.' eliminada', Response::HTTP_OK, $category);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
