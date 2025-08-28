<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CategoriesProducts as CategoriesProductsAdmin;
use App\Models\Admin\categoriesIndirectCosts as CategoriesIndirectCostsAdmin;
use App\Models\Admin\categoriesDirectCosts as categoriesDirectCostsAdmin;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests\CategoriesProducts\CreateCategory;
use App\Http\Requests\CategoriesProducts\UpdateCategory;

class CategoriesProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay usuarios devolvemos el json vacio
            if(CategoriesProductsAdmin::count() === 0){
                return ApiResponse::success('No hay categorias creadas', Response::HTTP_OK, []);
            }
            $categories = CategoriesProductsAdmin::orderBy('name', 'asc')
            ->paginate(10);
            return ApiResponse::success('Categorias de productos', Response::HTTP_OK, $categories);

        } catch (\Exception $e) {
            return back()->with('error', 'Error, intente nuevamente');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategory $request)
    {
        try {
            //creamos la categoria
            $category = new CategoriesProductsAdmin($request->input());
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
            //buscamos la categoria
            $categoriesProduct = CategoriesProductsAdmin::findOrFail($id);

            //retornamos la categoria
            return ApiResponse::success('Categoria', Response::HTTP_OK, $categoriesProduct);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategory $request, $id)
    {
        try{
        $name = $request->input('name');
        $slug = str_replace(' ', '-', $name);
        $categoriesProducts = CategoriesProductsAdmin::findOrFail($id);
        $categoriesProducts->slug = $slug;
        //$categoriesProducts->description= $request->input('description');
        //$categoriesProducts->update($request->input());
        if($categoriesProducts->isDirty()){
            $categoriesProducts->update($request->input());

            return ApiResponse::success('Categoria actualizada', Response::HTTP_OK, $categoriesProducts);
        }

            return ApiResponse::error('No realizo ningún cambio', Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
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
            //buscamos la categoria
            $categoriesProduct = CategoriesProductsAdmin::findOrFail($id);
            //obtenemos el nombre de la categoria
            $name = $categoriesProduct->name;
            //eliminamos la categoria
            $categoriesProduct->delete();

            return ApiResponse::success('Categoria'.$name. ' eliminada correctamente', Response::HTTP_OK, []);

        } catch (\Exception $e) {
            return ApiResponse::error('Categoria no encontrada',Response::HTTP_NOT_FOUND );
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
//     public function destroy(CategoriesProductsAdmin $categoriesProducts)
// {
//     try {
//         // Obtenemos el nombre de la categoría antes de eliminarla
//         $name = $categoriesProducts->name;

//         // Eliminamos la categoría
//         $categoriesProducts->delete();

//         // Retornamos la respuesta de éxito
//         return ApiResponse::success('Categoria ' . $name . ' eliminada correctamente', Response::HTTP_OK, []);

//     } catch (\Exception $e) {
//         // Si ocurre algún error, lo capturamos y devolvemos un mensaje de error
//         return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
//     }
// }

    public function searchCategoriesProducts($category)
    {
        try {
            $categoriesProducts = CategoriesProductsAdmin::where('name', 'LIKE', "%{$category}%")
                                                        ->orderBy('name', 'asc')
                                                        ->paginate(10);
            return ApiResponse::success('Categorías encontradas', Response::HTTP_OK, $categoriesProducts);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCategoriesTotalCategories()
    {
        try {
            $totalCategorias = CategoriesProductsAdmin::count();
            $totalindirectCosts = CategoriesIndirectCostsAdmin::count();
            $totalDirectCosts = categoriesDirectCostsAdmin::count();
            return ApiResponse::success('Total de categorías encontradas', Response::HTTP_OK,
            [
            'totalProducts' => $totalCategorias,
            'totalIndirectCosts' => $totalindirectCosts,
            'totalDirectCosts' => $totalDirectCosts
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
