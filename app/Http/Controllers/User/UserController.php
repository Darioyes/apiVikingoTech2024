<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Users\User as UserFront;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Responses\ApiResponse;
//esto se importa para escribir sql crudo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login(LoginRequest $request){
        try{
             $guard = $request->has('users') ? 'admin' : 'user';
            //buscamos en la base de deatos con attempt que el email y el password sean correctos
            //Auth es una clase que se importa para manejar la autenticacion de los usuarios y el guard es para saber si el usuario es admin o usuario dependiendo del guard que se le pase
            if(!Auth::guard($guard)->attempt($request->only('email','password'))){
                //si no lo son devolvemos un error
                return ApiResponse::error('Usuario y/o contraseña incorrectas', Response::HTTP_UNAUTHORIZED);
            }

            $user = UserFront::where('email', $request->email)->first();

            $tokenAccess = DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->first();

            if($tokenAccess){
                //si hay un token de ese usuario lo eliminamos
                DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
            }
            
            //si lo son creamos un token de acceso para ese usuario
            $token = $user->createToken('vikingo_token')->plainTextToken;
            //retornamos el token de acceso y el usuario
            return ApiResponse::successAuth('Login exitoso', Response::HTTP_OK, $token, $user);

        }catch(ModelNotFoundException $e){}
    }

        public function logout(){
        //eliminamos el token de la base de datos desde la autenticacion de sanctum
        auth()->user()->tokens()->delete();

        return ApiResponse::success('Sesión cerrada correctamente', Response::HTTP_OK);
    }
}
