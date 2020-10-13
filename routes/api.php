<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use CloudCreativity\LaravelJsonApi\Facades\JsonApi;

/*Route::get('articles/{article}', [\App\Http\Controllers\Api\ArticleController::class, 'show'])->name('api.v1.articles.read');
Route::get('articles', [\App\Http\Controllers\Api\ArticleController::class, 'index'])->name('api.v1.articles.index');*/

JsonApi::register('v1')->routes(function ($api) {
    /*$api->resource('articles')->only('create', 'update', 'delete')->middleware('auth');
    $api->resource('articles')->except('create', 'update', 'delete');*/
    $api->resource('articles')->relationships(function ($api) {
        $api->hasOne('authors')->except('replace');
    });
    $api->resource('authors')->only('index', 'read');
});
