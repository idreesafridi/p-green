<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ConstructionSiteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

///......Partial Public Routes......../////

Route::post('/login', [UserController::class, 'login']);

///......Protected Routes......../////

Route::middleware(['auth:sanctum', ])->group(function(){  
    
    Route::get('/constructionUser', [UserController::class, 'index']);
    Route::post('/construction/images', [UserController::class, 'uploadImages']);
    Route::post('/logout', [UserController::class, 'logout']);    
    Route::delete('/images/{id}', [UserController::class, 'deleteImage']);
    Route::post('/delete/images', [UserController::class, 'deleteMultiImage']);
    Route::post('/search/cantieri', [UserController::class, 'searching'])->name('searching');
    

    Route::post('/construction-site/images/{id}', [UserController::class, 'show'])->name('construction_detail');
    Route::post('/search/cantieri', [UserController::class, 'searching'])->name('searching');

    
});


