<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\indirectCosts;
use App\Models\Admin\transactions;

use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\IndirectCosts\UpdateIndirectCosts;
use App\Http\Requests\IndirectCosts\CreateIndirectCosts;


class IndirectCostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            if(indirectCosts::count() == 0){
                return ApiResponse::success('No hay costos indirectos', Response::HTTP_OK, []);

            }

            $indirectCosts = indirectCosts::with(['categories_indirect_costs:id,name,slug'])
                        ->orderBy('name', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de costos indirectos', Response::HTTP_OK, $indirectCosts);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateIndirectCosts $request)
    {
        try {
            //instanciamos el modelo transactions para guardar el costo directo
            $transaction = new transactions();
            $indirectCost = new indirectCosts($request->input());

            //guardamos el costo directo
            $indirectCost->save();
            //obtenemos el id del costo indirecto
            $indirectCost_id = $indirectCost->id;
            //guardamos el id del costo indirecto en la tabla transactions
            $transaction->indirect_costs_id = $indirectCost_id;
            //guardamos la transacción
            $transaction->save();

            return ApiResponse::success('Costo indirecto creado', Response::HTTP_CREATED, $indirectCost);
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

            $indirectCost = indirectCosts::with(['categories_indirect_costs:id,name,slug'])
                        ->findOrFail($id);

            return ApiResponse::success('Costo directo', Response::HTTP_OK, $indirectCost);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndirectCosts $request, $id)
    {
        try {

            $indirectCost = indirectCosts::findOrFail($id);

            $indirectCost->update($request->input());

            return ApiResponse::success('Costo indirecto actualizado', Response::HTTP_OK, $indirectCost);

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
            //instanciamos el modelo transactions para eliminar el costo indirecto
            $transaction = new transactions();
            $indirectCost = indirectCosts::findOrFail($id);

            //eliminamos el costo indirecto de la tabla transactions
            $transaction->where('indirect_costs_id', $id)->delete();


            //eliminamos el costo indirecto
            $indirectCost->delete();

            return ApiResponse::success('Costo indirecto eliminado', Response::HTTP_OK);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

    }

    public function searchindirectCosts($name)
    {
        try {

            $directCosts = indirectCosts::with(['categories_indirect_costs:id,name'])
                        ->where('name', 'like', '%'.$name.'%')
                        ->orWhere('description', 'like', '%'.$name.'%')
                        ->orWhereHas('categories_indirect_costs', function($query) use ($name){
                            $query->where('name', 'like', '%'.$name.'%');
                        })
                        ->orderBy('created_at', 'asc')
                        ->paginate(10);

            return ApiResponse::success('Lista de costos directos', Response::HTTP_OK, $directCosts);

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function sumaryIndirectCosts()
    {
        try {
        try {

            $totalCount = indirectCosts::count();
            $totalAmount = indirectCosts::sum('amount');
            $totalPrice = indirectCosts::sum('price');
            $averagePrice = indirectCosts::avg('price');

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

        } catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
