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

    public static function details ($itemId) {
        $item = Self::find($itemId);
        $item->category = Category::find($item['category_id']);
        $item->address = Address::details(explode(',',$item->address));
        return $item;
    }


}
