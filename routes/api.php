<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('/search_by_keyword/{keyword}', [ApiController::class, 'searchByKeyword']);
Route::post('/import_article/{name}', [ApiController::class, 'getNewArticle']);
Route::post('/get_links_table', [ApiController::class, 'getLinksTable']);
Route::post('/article_text/{name}', [ApiController::class, 'getArticleText']);
