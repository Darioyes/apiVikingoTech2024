<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Users\User as UserFront;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\CreateRequest;
//esto se importa para escribir sql crudo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
//el Illuminate\Support\Facades\Password; es para personalizar la url de restablecimiento de contraseña
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;

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
    public function store(CreateRequest $request)
    {
        try{
            //creamos un nuevo usuario con los datos que nos llegan
            $user = new UserFront($request->input());
            //subimos la imagen y guardamos la ruta en la variable $path
            if($request->hasFile('image')){
                $path = $request->image->store('public/images/user'); //sube los archivos en store/app/public/images/news
                //guardamos la ruta en la base de datos
                $user->image = $path;

            }
            //lo que llega en la variable name los convertimos a minusculas
            $user->name = strtolower($request->name);
            //lo que llega en la variable last_name los convertimos a minusculas
            $user->lastname = strtolower($request->lastname);
            //dejamos la primera letra en mayuscula
            $user->name = ucfirst($user->name);
            //dejamos la primera letra en mayuscula
            $user->lastname = ucfirst($user->lastname);
            //hasheamos la contraseña
            $user->password = Hash::make($request->password);
            //guardamos el usuario
            $user->save();
            //si el save es correcto devolvemos la respuesta
            if($user){
                $user->sendEmailVerificationNotification();
                return ApiResponse::success('Usuario creado correctamente', Response::HTTP_CREATED);
            }else{
                return ApiResponse::error('Error al crear el usuario', Response::HTTP_INTERNAL_SERVER_ERROR);
            }


        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
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

    //funcion para reenviar el correo de verificacion
    public function resendVerificationEmail($email){
        try{
            $user = UserFront::where('email', $email)->first();
            if($user->hasVerifiedEmail()){
                return ApiResponse::error('El correo electrónico ya está verificado', Response::HTTP_BAD_REQUEST);
            }
            $user->sendEmailVerificationNotification();
            return ApiResponse::success('Correo de verificación reenviado correctamente', Response::HTTP_OK);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no encontrado', Response::HTTP_NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //funcion para enviar el correo de restablecimiento de contraseña
    public function sendRecoveryLink(Request $request) 
    {
        $request->validate(['email' => 'required|email']);
        
        try{
        //buscar al usuario, genera el token y envía el mail automáticamente
        $status = Password::sendResetLink($request->only('email'));

        //Password::sendResetLink devuelve un status que puede ser Password::RESET_LINK_SENT o Password::INVALID_USER, dependiendo de si el correo existe o no en la base de datos, por eso se compara el status con Password::RESET_LINK_SENT para saber si se envió correctamente el correo o no
        return $status === Password::RESET_LINK_SENT
            ? ApiResponse::success('Correo de restablecimiento de contraseña enviado correctamente, recuerde que tiene 10 minutos para utilizarlo, no olvide revisar su bandeja de entrada o spam', Response::HTTP_OK)
            : ApiResponse::error('El correo electrónico no esta registrado, por favor verifique el correo electrónico enviado', Response::HTTP_BAD_REQUEST);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    public function resetPassword(Request $request) 
    {   
        $passwordRule = Rules\Password::min(8)
        ->mixedCase()
        ->numbers()
        ->symbols();
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|min:5|max:100',
            'password' => ['required', 'confirmed',$passwordRule],
        ]);
        try{

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ApiResponse::success('Contraseña restablecida correctamente', Response::HTTP_OK)
            : ApiResponse::error('Error, el token es inválido.', Response::HTTP_BAD_REQUEST);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no encontrado', Response::HTTP_NOT_FOUND);
        }
    }

}
