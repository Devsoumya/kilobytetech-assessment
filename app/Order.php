<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class Order extends Model
{

    /**
     * 0: placed but delivery person not assigned
     * 1: delivery person assigned by admin
     * 2: reached store
     * 3: item picked
     * 4: enroute
     * 5: delievered
     * 6: cancelled
     *
     *
     */
    public static $status = array(
        '0' =>'Order Placed but delivery person not assigned',
        '1' =>'Delivery person assigned by admin',
        '2' =>'Reached store',
        '3' =>'Item picked',
        '4' =>'Enroute',
        '5' =>'Delivered',
        '6' =>'Cancelled',

    );

    public static function details($orderIds) {
        $orders = Self::whereIn('id',$orderIds)->get();
        foreach ($orders as $key=>$order) {
            if($order->status > 0) {
                $order->deliveryPerson = User::find($order->delivery_person_id);
            }
            if($order->status == 0) {
                $busyDeliveryPersons = Self::whereIn('status',array(1,2,3,4))->get()->pluck('id')->toArray();
                $freeDelieveryPersons = User::whereNotIn('id',$busyDeliveryPersons)->where('category',2)->select('id','name','mobile')->get();
                $order->freeDeliverPerson = $freeDelieveryPersons;
            }
            $order->customer = User::find($order->customer_id);
            $order->details = json_decode($order->details);
            $order->orderStatus = Self::$status[$order->status];
            unset($order->customer_id);
            unset($order->delivery_person_id);
            $orders[$key] = $order;
        }
        return $orders;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','delivery_person_id','status','details'
    ];


}
