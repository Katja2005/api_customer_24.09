<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Customer $customer)
    {
        //return $customer->orders;

        $results = DB::table('orders as o')
        ->join('order_statuses as os', 'o.status', '=', 'os.order_status_id')
        ->join('customers as c', 'c.customer_id', '=', 'o.customer_id')
        ->select(
            'o.order_id',
            'o.order_date',
            'os.name as order_status_name'
        )
        ->where('c.customer_id', $customer->customer_id)
        ->get();

        return $results;

       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Nav nepieciešams API vidē
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validācija
        $request->validate([
            'order_date' => 'required|date',
            'status' => 'required|exists:order_statuses,order_status_id',
            'customer_id' => 'required|exists:customers,customer_id',
        ]);

        // Jauna pasūtījuma izveide
        $order = Order::create([
            'order_date' => $request->order_date,
            'status' => $request->status,
            'customer_id' => $request->customer_id,
        ]);

        return response()->json($order, 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer, Order $order)
    {
        // return $order->customer;

        $result = DB::table('orders as o')
            ->join('order_statuses as os', 'o.status', '=', 'os.order_status_id')
            ->join('customers as c', 'c.customer_id', '=', 'o.customer_id')
            ->select(
                'o.order_id',
                'o.order_date',
                'os.name as order_status_name'
            )
            ->where('c.customer_id', $customer->customer_id)
            ->where('o.order_id', $order->order_id)
            ->first(); 

        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Validācija
        $request->validate([
            'order_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|exists:order_statuses,order_status_id',
        ]);

        // Atjauninām pasūtījuma datus
        $order->update($request->only(['order_date', 'status']));

        return response()->json($order); // Atgriežam atjaunoto pasūtījumu
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete(); // Dzēšam pasūtījumu

        return response()->json(null, 204); // Atgriežam 204 No Content statusu
    }
}