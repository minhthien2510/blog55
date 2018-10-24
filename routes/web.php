<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/search', function () {
    $a = array();

    $categories = \App\Category::search($_GET['q'])->get();
    foreach ($categories as $category) {
        array_push($a, ['id' => $category->id, 'name' => $category->name]);
    }

//    $posts = \App\Post::search('')->get();
//    foreach ($posts as $post) {
//        array_push($a, $post->title);
//    }

    return $a;
})->name('api-search');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/search', function () {
    return view('search');
})->name('search');
