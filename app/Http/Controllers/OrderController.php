<?php

namespace App\Http\Controllers;
use Validator;
use App\User;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Catalogue;
use App\Order;
class OrderController extends BaseController
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function placeOrder() {
        $cart = $this->request->post('cart');
        $this->validate($this->request, [
            'cart'     => 'required'
        ]);
        if(empty($cart)) {
            return response()->json([
                'error' => "No order in cart specified"
            ], 400);
        }

        $cartDetails = array();
        $pickupLocations = array();
        $pickupLocationIDs = array();

        foreach ($cart as $cartItem) {
            $itemDetails = Catalogue::details($cartItem['item_id']);
            $item = array();
            $item['item_id'] = $cartItem['item_id'];
            $item['quantity'] = $cartItem['quantity'];
            $availableAddress = $itemDetails->address;
            $addressIndex = array_rand($availableAddress);
            $item['address'] = $availableAddress[$addressIndex]['id'];

            array_push($cartDetails,$item);
            array_push($pickupLocations,$availableAddress[$addressIndex]['address']);
            array_push($pickupLocationIDs,$availableAddress[$addressIndex]['id']);
        }
        $order = new Order();
        $order->customer_id = $this->request->auth->id;
        $order->status = 0;
        $order->details = json_encode(array(
            'cart'=>$cartDetails,
            'pickupLocations' => array_unique($pickupLocationIDs)
        ));
        if($order->save()) {
            return response()->json([
                'message' => "Order Placed Successfully",
                'pickupLocations' => array_unique($pickupLocations)
            ], 201);
        }



    }

    public function orderList() {
        $status = $this->request->get('status');
        $orders = Order::details(Order::where('status',$status)->get()->pluck('id')->toArray());
        return $orders;
    }

    public function assignDeliveryPerson() {
        $this->validate($this->request, [
            'order_id'               => 'required',
            'delivery_person_id'     => 'required'
        ]);

        $order = Order::find($this->request->get('order_id'));

        if(empty($order) || $order->status !=0) {
            return response()->json([
                'error' => "No such pending order exists"
            ], 400);
        }

        $deliveryPersonDetails = User::where('category',2)->find($this->request->get('delivery_person_id'));
        $validDeliveryPerson = true;

        if(empty($deliveryPersonDetails)) {
            $validDeliveryPerson = false;
        }
        $checkIfDeliveryPersonBusy = Order::whereBetween('status',[1,4])->where('delivery_person_id',$this->request->get('delivery_person_id'))->first();

        if(!empty($checkIfDeliveryPersonBusy)) {
            $validDeliveryPerson = false;
        }

        if(!$validDeliveryPerson) {
            return response()->json([
                'error' => "No such free delivery person exists"
            ], 400);
        }

        $order->status = 1;
        $order->delivery_person_id = $this->request->get('delivery_person_id');

        if($order->save()) {
            return response()->json([
                'message' => "Delivery Person Assigned Successfully"
            ], 200);
        }
    }

    public function changeOrderStatus() {
        $this->validate($this->request, [
            'order_id'               => 'required',
            'status'                 => 'required|integer|between:2,6'
        ]);
        $order = Order::where('id',$this->request->post('order_id'))->where('delivery_person_id',$this->request->auth->id)->whereBetween('status',[1,4])->first();
        if(empty($order)) {
            return response()->json([
                'error' => "Invalid Order"
            ], 400);
        }
        $order->status = $this->request->post('status');
        if($order->save()) {
            return response()->json([
                'message' => "Order Status updated succesfully",
                "data" => array(
                    'order_id'=>$order->id,
                    'status'=>Order::$status[$order->status],
                )
            ], 200);
        }
    }

}