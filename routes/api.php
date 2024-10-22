<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CategoriesProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriesDirectCostsController;
use App\Http\Controllers\Admin\CategoriesIndirectCostsController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\VikingoRolesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\MaintenancesController;
use App\Http\Controllers\Admin\CarouselController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\DirectCostsController;
use App\Http\Controllers\Admin\IndirectCostsController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\PurchaseOrdersController;
use App\Http\Controllers\Admin\TransactionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//!rutas bakcend
//ruta de login
Route::post('vikingouser/login', [UsersController::class, 'login']);

Route::middleware('auth:sanctum','verified')->group(function(){
    //?rutas de usuarios
    //ruta tipo recurso de users
    Route::resource('users', UsersController::class)->only(['index', 'store', 'show','update','destroy']);
    //Route::resource('users', UsersController::class)->except(['create','edit']);
    //ruta de busqueda por nombre o apellido de usuario
    Route::get('searchusers/{user}',[UsersController::class,'searchUser']);
    //ruta del total de usuarios
    Route::get('userstotal',[UsersController::class,'totalUsers']);
    //ruta total por genero de usuario
    Route::get('gender',[UsersController::class,'countGender']);
    //ruta de promedio de usuarios por genero
    Route::get('genderavg',[UsersController::class,'genderAVG']);
    //ruta de logout de users
    Route::post('vikingouser/logout', [UsersController::class, 'logout']);

    //?rutas de categorias de productos
    //ruta tipo recurso de categoriesProducts
    Route::resource('categoriesproducts', CategoriesProductsController::class)->only(['index', 'store', 'show','update','destroy']);
    //?rutas de categorias de costos directos
    //ruta tipo recurso de categoriesDirectCosts
    Route::resource('categoriesdirectcosts', CategoriesDirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de categorias de costos indirectos
    //ruta tipo recurso de categoriesIndirectCosts
    Route::resource('categoriesindirectcosts', CategoriesIndirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de ciudades
    //ruta tipo recurso de cities
    Route::resource('cities', CitiesController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de roles
    //ruta tipo recurso de roles
    Route::resource('roles', VikingoRolesController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de productos
    //ruta tipo recurso de products
    Route::resource('products', ProductsController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de información básica de productos
    Route::get('basicproducts',[ProductsController::class,'getInfoBasicProducts']);

    //?rutas de mantenimientos
    //ruta tipo recurso de maintenances
    Route::resource('maintenances', MaintenancesController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de busqueda por nombre o descripión de mantenimiento
    Route::get('searchmaintenance/{maintenance}',[MaintenancesController::class,'searchMaintenance']);
    //ruta para obtener infirmación básica de mantenimientos
    Route::get('basicmaintenance',[MaintenancesController::class,'getInfoBasicMaintenance']);

    //?rutas de carrusel
    //ruta tipo recurso de carousel
    Route::resource('carousel', CarouselController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de proveedores
    //ruta tipo recurso de suppliers
    Route::resource('suppliers', SuppliersController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de costos directos
    //ruta tipo recurso de directCosts
    Route::resource('directcosts', DirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de costos indirectos
    //ruta tipo recurso de indirectCosts
    Route::resource('indirectcosts', IndirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de ventas
    //rutas tipo recurso de sales
    Route::resource('sales', SalesController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de información básica de ventas
    Route::get('basicsales',[SalesController::class,'getInfoBasicSales']);

    //?rutas de ordenes de compra
    //ruta tipo recurso de purchaseOrders
    Route::resource('purchaseorders', PurchaseOrdersController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta para obtener la informaciòn básica de las ordenes de compra
    Route::get('basicpurchaseorders',[PurchaseOrdersController::class,'getInfoBasicPurcharseOrders']);

    //?rutas de transacciones
    //ruta tipo recurso de transactions
    Route::resource('transactions', TransactionsController::class)->only(['index', 'show']);

});
