<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'storeName','area','city','latitude','longitude'
    ];

    public static function details($addressIds) {
        $addresses = Self::whereIn('id',$addressIds)->get();
        $addressData = array();
        foreach ($addresses as $address) {
            $data = array();
            $data['id'] = $address->id;
            $data['address'] = $address->storeName.', '.$address->area.', '.$address->city;
            array_push($addressData,$data);
        }
        return $addressData;
    }


}
