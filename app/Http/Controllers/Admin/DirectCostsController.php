<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\directCosts;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\DirectCosts\UpdateDirectCosts;
use App\Http\Requests\DirectCosts\CreateDirectCosts;
use App\Models\Admin\transactions;

class DirectCostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            if(directCosts::count() == 0){
                return ApiResponse::success('No hay costos directos', Response::HTTP_OK, []);

            }

            $directCosts = directCosts::with(['categories_direct_costs:id,name,slug'])
                        ->orderBy('name', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de costos directos', Response::HTTP_OK, $directCosts);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDirectCosts $request)
    {
        try {
            //instanciamos el modelo de directCosts para guardar el costo directo
            $directCost = new directCosts($request->input());
            //instanciamos el modelo transactions para guardar el costo directo
            $transaction = new transactions();
            //guardamos el costo directo
            $directCost->save();
            //obtenemos el id del costo directo
            $directCost_id = $directCost->id;
            //guardamos el id del costo directo en la tabla transactions
            $transaction->direct_costs_id = $directCost_id;
            //guardamos la transacción
            $transaction->save();


            return ApiResponse::success('Costo directo creado', Response::HTTP_CREATED, $directCost);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            $directCost = directCosts::with(['categories_direct_costs:id,name,slug'])
                        ->findOrFail($id);

            return ApiResponse::success('Costo directo', Response::HTTP_OK, $directCost);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDirectCosts $request, $id)
    {
        try {

            $directCost = directCosts::findOrFail($id);
            $directCost->update($request->input());

            return ApiResponse::success('Costo directo actualizado', Response::HTTP_OK, $directCost);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            //instanciamos el modelo transactions para eliminar el costo directo
            $transaction = new transactions();
            $directCost = directCosts::findOrFail($id);
            //eliminamos el costo directo de la tabla transactions
            $transaction->where('direct_costs_id', $id)->delete();
            $directCost->delete();

            return ApiResponse::success('Costo directo eliminado', Response::HTTP_OK);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch (\Exception $e) {
            return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function searchDirectCosts($name)
    {
        try {

            $directCosts = directCosts::with(['categories_direct_costs:id,name'])
                        ->where('name', 'like', '%'.$name.'%')
                        ->orWhere('description', 'like', '%'.$name.'%')
                        ->orWhereHas('categories_direct_costs', function($query) use ($name){
                            $query->where('name', 'like', '%'.$name.'%');
                        })
                        ->orderBy('created_at', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de costos directos', Response::HTTP_OK, $directCosts);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function sumaryDirectCosts()
    {
        try {

            $totalCount = directCosts::count();
            $totalAmount = directCosts::sum('amount');
            $totalPrice = directCosts::sum('price');
            $averagePrice = directCosts::avg('price');

            $data = [
                'total_count' => $totalCount,
                'total_amount' => $totalAmount,
                'total_price' => $totalPrice,
                'average_price' => $averagePrice
            ];

            return ApiResponse::success('Resumen de costos directos', Response::HTTP_OK, $data);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
