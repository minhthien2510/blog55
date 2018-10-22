<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;

    protected $table = 'categories';

    public function post()
    {
        return $this->belongsToMany('App\Post');
    }

    public function categoryPost()
    {
        return $this->hasOne('App\CategoryPost');
    }
}
