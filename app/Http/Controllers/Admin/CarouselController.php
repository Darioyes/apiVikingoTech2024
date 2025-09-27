<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\carousel;
//use Illuminate\Http\Request;

use App\Http\Requests\Carousel\CreateCarousel;
use App\Http\Requests\Carousel\UpdateCarousel;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //si no hay productos devolvemos el json vacio
            if(carousel::count() === 0){
                return ApiResponse::success('No hay carrusel creado', Response::HTTP_OK, []);
            }
            $carousel = carousel::with(['product:id,name,description'])
                                        ->orderBy('id', 'asc')
                                        ->paginate(10);
            return ApiResponse::success('Productos', Response::HTTP_OK, $carousel);

        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCarousel $request)
    {
        try {

            //instanciamos el modelo para crear el producto
            $carousel = new carousel($request->all());
            //dd($carousel);

            //subimos la image1 del producto y guardamo la ruta en la variable path
            $path = $request->file('image')->store('public/images/carousel');
            //guardamos la ruta en la base de datos
            $carousel->image = $path;
            //subimos la image2 del producto y guardamo la ruta en la variable path
            $path2 = $request->file('image2')->store('public/images/carousel');
            //guardamos la ruta en la base de datos
            $carousel->image2 = $path2;
            //subimos la image3 del producto y guardamo la ruta en la variable path
            $path3 = $request->file('image3')->store('public/images/carousel');
            //guardamos la ruta en la base de datos
            $carousel->image3 = $path3;
            //guardamos el producto
            $carousel->save();
            //devolvemos la respuesta
            return ApiResponse::success('Foto carrusel creado', Response::HTTP_CREATED, $carousel);

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
            $carousel = carousel::with(['product:id,name,description'])
                                    ->findOrFail($id);
            return ApiResponse::success('Lista de carruseles', Response::HTTP_OK, $carousel);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Carrusel no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarousel $request, $id)
    {
        try {
            $carousel = carousel::findOrFail($id);
            $image = $carousel->image;
            $image2 = $carousel->image2;
            $image3 = $carousel->image3;
            $carousel->fill($request->all());

            //si viene la image del carrusel
            if($request->hasFile('image')&&$request->file('image')->isValid()){
                if($image){
                    //borramos la imagen anterior
                    Storage::delete($image);
                }
                //subimos la nueva imagen
                $path = $request->file('image')->store('public/images/carousel');
                //guardamos la ruta en la base de datos
                $carousel->image = $path;
            }
            //si viene la image2 del carrusel
            if($request->hasFile('image2')&&$request->file('image2')->isValid()){
                if($image2){
                    //borramos la imagen anterior
                    Storage::delete($image2);
                }
                //subimos la nueva imagen
                $path2 = $request->file('image2')->store('public/images/carousel');
                //guardamos la ruta en la base de datos
                $carousel->image2 = $path2;
            }
            //si viene la image3 del carrusel
            if($request->hasFile('image3')&&$request->file('image3')->isValid()){
                if($image3){
                    //borramos la imagen anterior
                    Storage::delete($image3);
                }
                //subimos la nueva imagen
                $path3 = $request->file('image3')->store('public/images/carousel');
                //guardamos la ruta en la base de datos
                $carousel->image3 = $path3;
            }
            //guardamos todo menos la imagen ya que se guardo anteriormente
            $carousel->fill($request->except('image'));
            $carousel->fill($request->except('image2'));
            $carousel->fill($request->except('image3'));
            //guardamos el carrusel
            $carousel->save();
            return ApiResponse::success('Carrusel actualizado', Response::HTTP_OK, $carousel);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Carrusel no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $carousel = carousel::findOrFail($id);
            //borramos la imagen
            Storage::delete($carousel->image);
            //borramos el producto
            $carousel->delete();
            return ApiResponse::success('Carrusel eliminado', Response::HTTP_OK);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error('Carrusel no encontrado', Response::HTTP_NOT_FOUND);
        } catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
