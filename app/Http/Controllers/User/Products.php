<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\Products as ProductsUser;
use App\Models\Users\CategoriesProducts as CategoriesProductsUser;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            //si no hay productos devolvemos el json vacio
            if (ProductsUser::count() === 0) {
                return ApiResponse::success('No hay productos creados', Response::HTTP_OK, []);
            }
            //traer solo las columnas id, name, slug, sale_price, image1 ordenados por name ascendente
            $products = ProductsUser::orderBy('name', 'asc')->select([
                'id', 
                'name', 
                'slug', 
                'reference', 
                'description',
                'stock', 
                'sale_price',
                'visible', 
                'image1',
                'image2',
                'image3',
                'image4',
                'image5',
                'color',
                'categories_products_id',
            ])->where('visible', true)
            ->paginate($request->get('per_page', 20));
                return ApiResponse::success('Productos', Response::HTTP_OK, $products);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

    //buscar producto por slug
    public function show($slug)
    {
        try {
            $product = ProductsUser::with(['categoriesProducts:id,name'])
            ->where('slug', $slug)
            ->where('visible', true)
            ->first( [
                'id', 
                'name', 
                'slug', 
                'reference', 
                'description',
                'stock', 
                'sale_price',
                'visible', 
                'image1',
                'image2',
                'image3',
                'image4',
                'image5',
                'color',
                'categories_products_id',
            ]);
            if (!$product) {
                return ApiResponse::error('Producto no encontrado', Response::HTTP_NOT_FOUND);
            }
            return ApiResponse::success('Producto encontrado', Response::HTTP_OK, $product);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoria no encontrada', Response::HTTP_NOT_FOUND);
        }
    }

     public function productsByCategory($categorySlug, Request $request)
    {
        try {
            // Buscar la categoría por slug
            $category = CategoriesProductsUser::where('slug', $categorySlug)->first();
            
            if (!$category) {
                return ApiResponse::error('Categoría no encontrada', Response::HTTP_NOT_FOUND);
            }

            // Productos de esa categoría con paginación
            $products = ProductsUser::whereHas('categoriesProducts', function($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                })
                ->with(['categoriesProducts:id,name,slug']) // Cargar datos de la categoría
                ->where('visible', true)
                ->select([
                    'id', 
                    'name', 
                    'slug', 
                    'reference', 
                    'description',
                    'stock', 
                    'sale_price',
                    'visible', 
                    'image1',
                    'image2',
                    'image3',
                    'image4',
                    'image5',
                    'color',
                    'categories_products_id',
                ])
                ->orderBy('name', 'asc')
                ->paginate($request->get('per_page', 20));

            // Si no hay productos en esta categoría
            if ($products->count() === 0) {
                return ApiResponse::success(
                    "No hay productos en la categoría {$category->name}", 
                    Response::HTTP_OK, 
                    [
                        'category' => $category,
                        'products' => []
                    ]
                );
            }

            return ApiResponse::success(
                "Productos de la categoría {$category->name}", 
                Response::HTTP_OK, 
                [
                    'category' => $category,
                    'products' => $products
                ]
            );
            
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}