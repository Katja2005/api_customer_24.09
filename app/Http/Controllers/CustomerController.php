<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = DB::select('SELECT 
        c.customer_id,
        c.first_name,
        c.last_name,
        c.address,
        c.city,
        c.state,
        c.points,
        o.order_date,
        os.name
        FROM 
        sql_store.customers c
        JOIN 
        sql_store.orders o
        JOIN
        sql_store.order_statuses os
        ON 
        c.customer_id = o.customer_id');     
    return $customers;
    // \Log::debug($customers);
}
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([ 
            
        'customer_id' => 'required|integer|unique:customers,customer_id',  
        'first_name' => 'required|string|max:255',  
        'last_name' => 'required|string|max:255',   
        'address' => 'required|string|max:255',    
        'city' => 'required|string|max:255',        
        'state' => 'required|string|max:255',     
        'points' => 'nullable|integer',            
        'gold_member' => 'nullable|boolean',
        ]); 
        
        $customer = Customer::create($fields); 
        return [ 'customer' => $customer];   
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer){

    
        $results = DB::table('customers as c')
        ->join('orders as o', 'c.customer_id', '=', 'o.customer_id')
        ->join('order_statuses as os', 'o.status', '=', 'os.order_status_id')
        ->select(
            'c.customer_id',
            'c.first_name',
            'c.last_name',
            'c.address',
            'c.city',
            'c.state',
            'c.points',
            'o.order_date',
            'os.name as order_status_name'
        )
        ->where('c.customer_id', $customer->id) // Filtering by customer ID
        ->get();
    
        
    
    // Check if any results were found
    if ($results->isEmpty()) {
        return response()->json(['message' => 'Customer not found or no orders available'], 404);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
