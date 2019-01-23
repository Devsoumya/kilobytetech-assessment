<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','delivery_person_id','status','details'
    ];

    public static function details($orderIds) {
        $orders = Self::whereIn('id',$orderIds)->get();
        foreach ($orders as $key=>$order) {
            if($order->status>0) {
                $order->deliveryPerson = User::find($order->delivery_person_id);
            }
            $order->customer = User::find($order->customer_id);
            $order->details = json_decode($order->details);
            unset($order->customer_id);
            unset($order->delivery_person_id);
            $orders[$key] = $order;
        }
        return $orders;
    }


}
