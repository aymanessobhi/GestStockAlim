<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/productsInStock',[StockController::class,'productsInStock']);
Route::get('/products',[ProductController::class,'getProducts']);
Route::post('/addProductInStock',[StockController::class, 'addProductInStock']);
Route::get('/possible-recipes', [RecipeController::class, 'getPossibleRecipes']);
Route::post('/validate-recipe',[RecipeController::class,'validateRecipe']);