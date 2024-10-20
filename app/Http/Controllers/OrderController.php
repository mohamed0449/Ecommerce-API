<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
        ->paginate(20);
        if ($orders){
            foreach ($orders as $order){
                foreach ($order->orderItems as $orderItem){
                    $product =  Product::where('id', $orderItem->product_id)->pluck('name');
                    $orderItem->product_name = $product['0'];
                }
            }

            return response()->json($orders, 200);
        }
        else{
            return response()->json('No orders found', 404);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        if ($order){
            return response()->json($order, 200);
        }
        else{
            return response()->json('Order not found', 404);
        }
    }

    public function store(Request $request)
    {
        
        try{
            $location = Location::where('user_id', Auth::id())->first();
            if (!$location) {
                return response()->json(['error' => 'Location not found'], 404);
            }
    
          
            $request->validate([
           'order_items' => 'required|array', // Ensure order_items is an array
            'quantity' => 'required|integer', // Add integer validation for quantity
            'total_price' => 'required|numeric', // Add numeric validation for total_price
            'date_of_delivery' => 'required|date', // Add date validation for date_of_delivery

        ]);
        $order = new Order();
        $order->user_id = Auth::id();
        $order->location_id = $location->id;
        $order->total_price = $request->total_price;
        $order->date_of_delivery = $request->date_of_delivery;
        $order->save();

        foreach ($request->order_items as $orderItem){
            $items = new OrderItems();
            $items->order_id = $order->id;
            $items->product_id = $orderItem['product_id'];
            $items->quantity = $orderItem['quantity'];
            $items->price = $orderItem['price'];
            $items->save();

            $product = Product::where('id', $orderItem['product_id'])->first();
            $product->amount = $product->quantity - $orderItem['quantity'];
            $product->save();

        }
        return response()->json('Order created successfully', 201);
    }
    catch (\Exception $e){
        return response()->json($e->getMessage(), 500);
    }
    }

    public function get_order_items($id)
    {
        $orderItems = OrderItems::where('order_id', $id)->get();
        if ($orderItems){
            foreach ($orderItems as $orderItem){
                $product =  Product::where('id', $orderItem->product_id)->pluck('name');
                $orderItem->product_name = $product['0'];
            }
            return response()->json($orderItems, 200);
        }
        else{
            return response()->json('No order items found', 404);
        }
    }

    public function get_user_orders($id)
    {
        $orders = Order::where('user_id', $id)
            ->with('orderItems', function($query) {
                $query->orderby('created_at', 'desc');
            })->get();
    
        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->orderItems as $order_items) {
                    $product = Product::where('id', $order_items->product_id)->pluck('name');
                    $order_items->product_name = $product['0'];
                }
            }
            return response()->json($orders, 200);
        } else {
            return response()->json('No orders found', 404);
        }
    }

    public function change_order_status($id, Request $request)
    {
        $order = Order::find($id);
        if ($order){
            $order->update([
                'status' => $request->status
            ]);
            return response()->json('Status updated successfully', 200);
        }
        else{
            return response()->json('Status not found', 404);
        }
    }
}

