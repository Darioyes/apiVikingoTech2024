<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\CreateRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\updateRequest;
use App\Models\Admin\User as UserAdmin;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{

            //si no hay usuarios devolvemos el json vacio
            if(UserAdmin::count() === 0){
                return ApiResponse::success('No hay usuarios', Response::HTTP_OK, []);
            }

            //buscamos todos los usuarios y los paginamos de 10 en 10
            $users = UserAdmin::with(['vikingo_roles', 'cities'])
            ->orderBy('id', 'desc')
            ->paginate(10);
            //devolvemos la respuesta
            return ApiResponse::success('Usiarios registrados', Response::HTTP_OK, $users);

        }
        catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
        catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        try{
            //creamos un nuevo usuario con los datos que nos llegan
            $user = new UserAdmin($request->input());
            //subimos la imagen y guardamos la ruta en la variable $path
            $path = $request->image->store('public/images/user'); //sube los archivos en store/app/public/images/news
            //guardamos la ruta en la base de datos
            $user->image = $path;
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
    public function show(UserAdmin $user)
    {
        try{

            //buscamos el usuario por id
            $users = UserAdmin::with(['vikingo_roles', 'cities'])
            ->findOrFail($user->id);
            //si el usuario no existe devolvemos un error
            if(!$users){
                return ApiResponse::error('Usuario no encontrado', Response::HTTP_NOT_FOUND);
            }
            //si el usuario existe devolvemos la respuesta
            return ApiResponse::success('Detalle del usuario', Response::HTTP_OK, $users);


        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(updateRequest $request, UserAdmin $user)
    {
        try {
            //dd($request);
            // Encuentra al usuario
            $userToUpdate = UserAdmin::findOrFail($user->id);

            // Llena el modelo con los datos del request
            $userToUpdate->fill($request->input());

            //si el usuario sube una imagen
            if($request->hasFile('image') && $request->file('image')->isValid()){
                //guardamos la ruta de la imagen imagen
                $path = $request->file('image')->store('public/images/user');
                //eliminamos la imagen anterior si existe
                if($userToUpdate->image){
                    Storage::delete($userToUpdate->image);
                };
                $userToUpdate->image = $path;
            }

            // Guarda solo si hubo cambios
            if ($userToUpdate->isDirty()) {
                //guardamos todo menos la imagen ya que se guardo anteriormente
                $userToUpdate->fill($request->except('image'));
                //salvamos la imagen
                $userToUpdate->save();
                return ApiResponse::success('Usuario actualizado correctamente', Response::HTTP_OK, $userToUpdate);
            }

            return ApiResponse::success('No hubo cambios para actualizar', Response::HTTP_OK, $userToUpdate);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('El usuario que desea modificar no existe', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAdmin $users)
    {
        //
    }

    public function login(LoginRequest $request){

        try{


            //verificamos el guard que sea admin
            $guard = $request->has('users') ? 'admin' : 'user';
            //buscamos en la base de deatos con attempt que el email y el password sean correctos
            if(!Auth::guard($guard)->attempt($request->only('email','password'))){
                //si no lo son devolvemos un error
                return ApiResponse::error('Usuario y/o contraseña incorrectas', Response::HTTP_UNAUTHORIZED);
            }

            $user = UserAdmin::where('email', $request->email)->first();
            //$user = Auth::guard($guard)->user();


            if ( $user->vikingo_roles_id === 2) {
                // Si '2 o administrador' no es true, el usuario no tiene permisos de administrador
                Auth::guard($guard)->logout();
                return ApiResponse::error('No tiene acceso a esta aplicación', Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('vikingo_token')->plainTextToken;
            return ApiResponse::successAuth('Login exitoso', Response::HTTP_OK, $token, $user);

        }catch(ModelNotFoundException $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        }

    }

    public function logout(){
        //eliminamos el token de la base de datos desde la autenticacion de sanctum
        auth()->user()->tokens()->delete();

        return ApiResponse::success('Sesión cerrada correctamente', Response::HTTP_OK);
    }
}
