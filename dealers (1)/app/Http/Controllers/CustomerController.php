<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use Illuminate\Http\Request;
use App\Models\DealerAuthController;
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{

    public function index()
    {
        $dealerId = Auth::guard('dealer')->id();
    
        $customers = Customer::where('dealer_id', $dealerId)
                    ->with('vehicles')
                    ->get();
    
        return view('customers.index', compact('customers'));
    }
    

public function search(Request $request)
{
    $dealerId = Auth::guard('dealer')->id();
    $query = $request->q;

    $results = Customer::where('dealer_id', $dealerId)
        ->where(function ($q) use ($query) {
            $q->where('customer_name', 'like', "%$query%")
              ->orWhere('contact_number', 'like', "%$query%");
        })
        ->select('customer_name', 'contact_number')
        ->get();

    return response()->json($results);
}




}