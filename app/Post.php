<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    protected $table = 'posts';

    public function category()
    {
        return $this->belongsToMany('App\Category');
    }

    public function categoryPost()
    {
        return $this->hasOne('App\CategoryPost');
    }
}
