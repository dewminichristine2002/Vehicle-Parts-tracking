<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use Illuminate\Http\Request;


class CustomerController extends Controller
{

public function index()
{
    $customers = Customer::with('vehicles')->get();
    return view('customers.index', compact('customers'));
}

public function edit($contact_number)
{
    $customer = Customer::where('contact_number', $contact_number)->firstOrFail();
    return response()->json($customer);
}

public function search(Request $request)
{
    $query = $request->input('q');

    $results = Customer::where('contact_number', 'like', "%$query%")
                ->orWhere('customer_name', 'like', "%$query%")
                ->limit(10)
                ->get(['contact_number', 'customer_name']);

    return response()->json($results);
}




/*public function update(Request $request, $contact_number)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
    ]);

    $customer = Customer::where('contact_number', $contact_number)->firstOrFail();
    $customer->customer_name = $request->customer_name;
    $customer->save();

    return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
}*/
}