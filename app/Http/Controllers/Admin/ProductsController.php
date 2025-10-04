<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProducts;
use App\Http\Requests\Products\UpdateProducts;
use App\Models\Admin\Products as ProductsAdmin;
//use Illuminate\Http\Request;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay productos devolvemos el json vacio
            if(ProductsAdmin::count() === 0){
                return ApiResponse::success('No hay productos creados', Response::HTTP_OK, []);
            }
            $products = ProductsAdmin::with(['categoriesProducts:id,name'])
                                        ->orderBy('stock', 'asc')
                                        ->paginate(10);
            return ApiResponse::success('Productos', Response::HTTP_OK, $products);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        public function indexNoPaginate()
    {
        try {
            //si no hay productos devolvemos el json vacio
            if(ProductsAdmin::count() === 0){
                return ApiResponse::success('No hay productos creados', Response::HTTP_OK, []);
            }
            $products = ProductsAdmin::with(['categoriesProducts:id,name'])
                                        ->orderBy('name', 'asc')
                                        ->get();
            return ApiResponse::success('Productos', Response::HTTP_OK, $products);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProducts $request)
    {
        try {
            //obtenemos el nombre del producto
            $name = $request->input('name');
            //creamos el slug
            $slug = str_replace(' ', '-', $name);
            //dd($slug);
            //instanciamos el modelo para crear el producto
            $product = new ProductsAdmin($request->all());
            //dd($product);
            //guardamos el slug
            $product->slug = $slug;
            //subimos la image1 del producto y guardamo la ruta en la variable path
            $path = $request->file('image1')->store('public/images/products');
            //guardamos la ruta en la base de datos
            $product->image1 = $path;
            //si viene la image2 del producto y guardamo la ruta en la variable path2
            if($request->hasFile('image2')){
                $path2 = $request->file('image2')->store('public/images/products');
                //guardamos la ruta en la base de datos
                $product->image2 = $path2;
            }
            //si viene la image3 del producto y guardamo la ruta en la variable path3
            if($request->hasFile('image3')){
                $path3 = $request->file('image3')->store('public/images/products');
                //guardamos la ruta en la base de datos
                $product->image3 = $path3;
            }
            //si viene la image4 del producto y guardamo la ruta en la variable path4
            if($request->hasFile('image4')){
                $path4 = $request->file('image4')->store('public/images/products');
                //guardamos la ruta en la base de datos
                $product->image4 = $path4;
            }
            //si viene la image5 del producto y guardamo la ruta en la variable path5
            if($request->hasFile('image5')){
                $path5 = $request->file('image5')->store('public/images/products');
                //guardamos la ruta en la base de datos
                $product->image5 = $path5;
            }
            $product->save();

            return ApiResponse::success('Producto creado', Response::HTTP_CREATED, $product);

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
            //buscamos el producto
            $product = ProductsAdmin::with(['categoriesProducts:id,name'])->findOrFail($id);
            //retornamos el producto
            return ApiResponse::success('Producto', Response::HTTP_OK, $product);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProducts $request, $id)
    {
        try{

            $productToUpdate = ProductsAdmin::with(['categoriesProducts'])->findOrFail($id);
            //dd($productToUpdate);
            $image1 = $productToUpdate->image1;
            $image2 = $productToUpdate->image2;
            $image3 = $productToUpdate->image3;
            $image4 = $productToUpdate->image4;
            $image5 = $productToUpdate->image5;

            //llena el modelo con los datos de la peticion
            $productToUpdate->fill($request->input());
            //si el usuario sube image1
            if ($request->hasFile('image1') && $request->file('image1')->isValid()) {
                //guardamos la image1 y la ruta de la image1
                $path = $request->file('image1')->store('public/images/products');
                //eliminamos la image1 anterior si existe
                if ($image1) {
                    Storage::delete($image1);
                }
                //guardamos la nueva ruta de la image1
                $productToUpdate->image1 = $path;
            }
            //si el usuario sube image2
            if ($request->hasFile('image2') && $request->file('image2')->isValid()) {
                //guardamos la image2 y la ruta de la image2
                $path2 = $request->file('image2')->store('public/images/products');
                //eliminamos la image2 anterior si existe
                if ($image2) {
                    Storage::delete($image2);
                }
                //guardamos la nueva
                $productToUpdate->image2 = $path2;
            }
            //si el usuario sube image3
            if ($request->hasFile('image3') && $request->file('image3')->isValid()) {
                //guardamos la image3 y la ruta de la image3
                $path3 = $request->file('image3')->store('public/images/products');
                //eliminamos la image3 anterior si existe
                if ($image3) {
                    Storage::delete($image3);
                }
                //guardamos la nueva ruta de la image3
                $productToUpdate->image3 = $path3;
            }
            //si el usuario sube image4
            if ($request->hasFile('image4') && $request->file('image4')->isValid()) {
                //guardamos la image4 y la ruta de la image4
                $path4 = $request->file('image4')->store('public/images/products');
                //eliminamos la image4 anterior si existe
                if ($image4) {
                    Storage::delete($image4);
                }
                //guardamos la nueva ruta de la image4
                $productToUpdate->image4 = $path4;
            }
            //si el usuario sube image5
            if ($request->hasFile('image5') && $request->file('image5')->isValid()) {
                //guardamos la image5 y la ruta de la image5
                $path5 = $request->file('image5')->store('public/images/products');
                //eliminamos la image5 anterior si existe
                if ($image5) {
                    Storage::delete($image5);
                }
                //guardamos la nueva ruta de la image5
                $productToUpdate->image5 = $path5;
            }
            //si el producto no ha cambiado
            if ($productToUpdate->isDirty()) {
                //guardamos todo menos las imagenes que ya se guardaron anteriormente
                $productToUpdate->fill($request->except('image1', 'image2', 'image3', 'image4', 'image5'));
                //guardamos los cambios del producto
                $productToUpdate->save();
                //retornamos la respuesta
                return ApiResponse::success('Producto actualizado', Response::HTTP_OK, $productToUpdate);
            }
            //retornamos la respuesta
            return ApiResponse::success('Producto no ha cambiado', Response::HTTP_OK, $productToUpdate);

        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //buscamos el producto
            $product = ProductsAdmin::findOrFail($id);
            //obtenemos el nombre del producto
            $name = $product->name;
            //si existe la image1 la eliminamos
            if ($product->image1) {
                Storage::delete($product->image1);
            }
            //si existe la image2 la eliminamos
            if ($product->image2) {
                Storage::delete($product->image2);
            }
            //si existe la image3 la eliminamos
            if ($product->image3) {
                Storage::delete($product->image3);
            }
            //si existe la image4 la eliminamos
            if ($product->image4) {
                Storage::delete($product->image4);
            }
            //si existe la image5 la eliminamos
            if ($product->image5) {
                Storage::delete($product->image5);
            }
            //eliminamos el producto
            $product->delete();
            //retornamos la respuesta
            return ApiResponse::success('Producto '.$name.' eliminado', Response::HTTP_OK, $name);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getInfoBasicProducts(){
        try {
            //traemos la cantidad de productos
            $totalProducts = ProductsAdmin::count();
            //traemos el total del precio de los productos
            $totalPrice = ProductsAdmin::sum('sale_price');
            //traemos el costo total de los productos
            $totalCost = ProductsAdmin::sum('cost_price');
            //traemos el total del stock de los productos
            $totalStock = ProductsAdmin::sum('stock');

            return ApiResponse::success('Productos', Response::HTTP_OK,[
                'totalProducts' => $totalProducts,
                'totalPrice' => $totalPrice,
                'totalCost' => $totalCost,
                'totalStock' => $totalStock
            ]);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchProduct($name){
        try {
            //buscamos el producto
            $product = ProductsAdmin::with(['categoriesProducts:id,name'])
                                    ->where('name', 'like', '%'.$name.'%')
                                    ->orWhere('reference', 'like', '%'.$name.'%')
                                    ->orWhere('description', 'like', '%'.$name.'%')
                                    ->orWhereHas('categoriesProducts', function($query) use ($name){
                                        $query->where('name', 'like', '%'.$name.'%');
                                    })
                                    ->orderBy('stock', 'asc')
                                    ->paginate(10);
            //retornamos el producto
            return ApiResponse::success('Producto', Response::HTTP_OK, $product);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado', Response::HTTP_NOT_FOUND);
        }
    }


}
