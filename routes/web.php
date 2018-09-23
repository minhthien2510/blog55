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

Route::get('blog/updateDB', function () {
    $posts = json_decode(file_get_contents('public/itviec-blog-content.json'));

    foreach ($posts as $post) {
        if (\Illuminate\Support\Facades\DB::table('posts')->where('name', $post->url_path)->doesntExist()) {
            $query1 = new \App\Post();
            $query1->title = $post->title;
            $query1->name = $post->name;
            $query1->content = $post->content;
            $query1->excerpt = $post->excerpt;
            $query1->image = $post->thumbnail;
            $query1->save();

            foreach ($post->categories as $key => $value) {
                $query2 = new \App\Category();
                $query2->name = $value;
                $query2->slug = $key;
                $query2->save();

                $query3 = new \App\CategoryPost();
                $query3->post_id = $query1->id;
                $query3->category_id = $query2->id;
                $query3->save();
            }

        }
    }

    return \Illuminate\Support\Facades\DB::table('posts')->get();
});
