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
use App\Http\Controllers\Admin\General;
use App\Http\Controllers\User\Carousel;

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

//!rutas del bacoffice de vikingo tech
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
    Route::get('databasicusers',[UsersController::class,'dataBasicUsers']);
    //ruta total por genero de usuario
    Route::get('gender',[UsersController::class,'countGender']);
    //ruta de promedio de usuarios por genero
    Route::get('genderavg',[UsersController::class,'genderAVG']);
    //ruta de logout de users
    Route::post('vikingouser/logout', [UsersController::class, 'logout']);
    //ruta para verificar si hay un token activo
    Route::get('verifytoken/{id}', [UsersController::class, 'checkToken']);
    //ruta usuarios index no páginados
    Route::get('users-nopaginate',[UsersController::class,'indexNoPagination']);

    //?rutas de categorias de productos
    //ruta tipo recurso de categoriesProducts
    Route::resource('categoriesproducts', CategoriesProductsController::class)->only(['index', 'store', 'show','update','destroy']);
    Route::get('searchcategoriesproducts/{category}', [CategoriesProductsController::class, 'searchCategoriesProducts']);
    Route::get('categories/total', [CategoriesProductsController::class, 'getCategoriesTotalCategories']);

    //?rutas de categorias de costos directos
    //ruta tipo recurso de categoriesDirectCosts
    Route::resource('categoriesdirectcosts', CategoriesDirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);
    Route::get('searchcategoriesdirectcosts/{category}', [CategoriesDirectCostsController::class, 'searchCategoriesDirectCosts']);
    Route::get('categoriesdirect/total', [CategoriesDirectCostsController::class, 'getAllCategoriesDirectCosts']);

    //?rutas de categorias de costos indirectos
    //ruta tipo recurso de categoriesIndirectCosts
    Route::resource('categoriesindirectcosts', CategoriesIndirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);
    Route::get('searchcategoriesindirectcosts/{category}', [CategoriesIndirectCostsController::class, 'searchCategoriesIndirectCosts']);
    Route::get('categoriesindirect/total', [CategoriesIndirectCostsController::class, 'getAllCategoriesIndirectCosts']);

    //?rutas de ciudades
    //ruta tipo recurso de cities
    Route::resource('cities', CitiesController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de ciudades para el input
    Route::get('citiesinput',[CitiesController::class,'citiesInput']);

    //?rutas de roles
    //ruta tipo recurso de roles
    Route::resource('roles', VikingoRolesController::class)->only(['index', 'store', 'show','update','destroy']);

    //?rutas de productos
    //ruta tipo recurso de products
    Route::resource('products', ProductsController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de información básica de productos
    Route::get('basicproducts',[ProductsController::class,'getInfoBasicProducts']);
    //ruta para obtener la busqueda de productos por nombre o descripción
    Route::get('searchproducts/{product}',[ProductsController::class,'searchProduct']);
    //ruta para todos los productos no páginados
    Route::get('products-nopaginate',[ProductsController::class,'indexNoPaginate']);
    //ruta para obtener el total de productos sin paginación
    Route::get('totalproducts',[ProductsController::class,'searchProductNoPaginate']);

    //?rutas de mantenimientos
    //ruta tipo recurso de maintenances
    Route::resource('maintenances', MaintenancesController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de busqueda por nombre o descripión de mantenimiento
    Route::get('searchmaintenance/{maintenance}',[MaintenancesController::class,'searchMaintenance']);
    //ruta para obtener infirmación básica de mantenimientos
    Route::get('basicmaintenance',[MaintenancesController::class,'getInfoBasicMaintenance']);
    //ruta para obtener el progreso del mantenimiento
    Route::get('progressmaintenance/{day}',[MaintenancesController::class,'getMaintenanceProgress']);

    //?rutas de carrusel
    //ruta tipo recurso de carousel
    Route::resource('carousel', CarouselController::class)->only(['index', 'store', 'show','update','destroy']);
    Route::post('carousel/update-order', [CarouselController::class, 'updateOrder']);
    Route::get('searchcarousel/{carousel}', [CarouselController::class, 'searchCarousel']);

    //?rutas de proveedores
    //ruta tipo recurso de suppliers
    Route::resource('suppliers', SuppliersController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de busqueda por nombre de proveedor
    Route::get('searchsuppliers/{supplier}',[SuppliersController::class,'searchSuppliers']);
    //ruta de información básica de proveedores
    Route::get('basicsuppliers',[SuppliersController::class,'infoBasicSuppliers']);
    //ruta para obtener los proveedores sin paginar
    Route::get('totalsuppliers',[SuppliersController::class,'getAllSuppliersNoPaginate']);

    //?rutas de costos directos
    //ruta tipo recurso de directCosts
    Route::resource('directcosts', DirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de search por nombre de costos directos
    Route::get('searchdirectcosts/{directcost}',[DirectCostsController::class,'searchDirectCosts']);
    //ruta para obtener el resumen de costos directos
    Route::get('sumarydirectcosts',[DirectCostsController::class,'sumaryDirectCosts']);

    //?rutas de costos indirectos
    //ruta tipo recurso de indirectCosts
    Route::resource('indirectcosts', IndirectCostsController::class)->only(['index', 'store', 'show','update','destroy']);
    //rutra de search de indirectCosts
    Route::get('searchindirectcosts/{indirectcost}',[IndirectCostsController::class,'searchindirectCosts']);
    //ruta para obtener el resumen de costos indirectos
    Route::get('sumaryindirectcosts',[IndirectCostsController::class,'sumaryIndirectCosts']);

    //?rutas de ventas
    //rutas tipo recurso de sales
    Route::resource('sales', SalesController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta de información básica de ventas
    Route::get('basicsales',[SalesController::class,'getInfoBasicSales']);
    //ruta para obtener la busqueda de ventas por busqueda especifica
    Route::get('searchsales/{sale}',[SalesController::class,'searchSales']);
    //ruta para obtener el resumen de ventas
    Route::get('sumarysales/{day}',[SalesController::class,'getSumarySales']);
 

    //?rutas de ordenes de compra
    //ruta tipo recurso de purchaseOrders
    Route::resource('purchaseorders', PurchaseOrdersController::class)->only(['index', 'store', 'show','update','destroy']);
    //ruta para obtener la informaciòn básica de las ordenes de compra
    Route::get('basicpurchaseorders',[PurchaseOrdersController::class,'getInfoBasicPurcharseOrders']);
    //ruta para obtener la busqueda de ordenes de compra por descripción o número de orden
    Route::get('searchpurchaseorders/{purchaseorder}',[PurchaseOrdersController::class,'searchPurchaseOrders']);

    //?rutas de transacciones
    //ruta tipo recurso de transactions
    Route::resource('transactions', TransactionsController::class)->only(['index', 'show']);
    //ruta para obtener el resumen de transacciones
    Route::get('sumarytransactions',[TransactionsController::class,'sumaryTransactions']);
    //ruta para obtener los movimientos de las transacciones de usuarios
    Route::get('transactionsusers',[TransactionsController::class,'transactionsUsers']);
    //ruta para obtener los movimientos de las transacciones de mantenimientos
    Route::get('transactionsmaintenance',[TransactionsController::class,'transactionsMaintenance']);
    //ruta para obtener los movimientos de las transacciones de ordenes de compra
    Route::get('transactionspurchaseorders',[TransactionsController::class,'transactionsPurchaseOrders']);
    //ruta para obtener los movimientos de las transacciones de costos directos
    Route::get('transactionsdirectcosts',[TransactionsController::class,'transactionsDirectCosts']);
    //ruta para obtener los movimientos de las transacciones de costos indirectos
    Route::get('transactionsindirectcosts',[TransactionsController::class,'transactionsIndirectCosts']);

    //?rutas de summary frontend
    //ruta para obtener la información de la página de inicio
    Route::get('summary/{day}',[SalesController::class,'getSummary']);

});

//!rutas del frontend de usuarios de vikingo tech
Route::get('vikingousers/carousel',[Carousel::class,'index']);
