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

Route::get('/categories/search', function () {
    $categories = \App\Category::search($_GET['q'])->get();
    return $categories;
})->name('categories.search');

Route::get('/posts/search', function () {
    $posts = \App\Post::search($_GET['q'])->get();
    return $posts;
})->name('posts.search');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/search', function () {
    return view('search');
})->name('search');

Route::post('/search', function () {
    $client = Elasticsearch\ClientBuilder::create()->build();

    $params = [
        'index' => 'es',
        'body' => [
            'query' => [
                'bool' => [
                    'must' => [
                        [ 'match' => [ 'name' => $_POST['search'] ] ]
                    ]
                ]
            ]
        ]
    ];

    return $client->search($params);

})->name('search');
