<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Catalogue extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','category_id','address'
    ];


}
