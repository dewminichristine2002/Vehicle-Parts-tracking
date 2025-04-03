<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\SoldPart;
use App\Models\OtherCost;
use App\Models\BatchPart;
use App\Models\VehiclePart;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Customer;
use App\Models\CustomerVehicle;

class InvoiceController extends Controller
{
    public function create()
    {
        $parts = BatchPart::select('part_number', 'part_name')->distinct()->get();
        return view('invoices.create', compact('parts'));
    }
    public function store(Request $request)
    {
        // ✅ Sanitize input first
        $request->merge([
            'parts' => array_filter($request->parts ?? [], function ($item) {
                return isset($item['part_number'], $item['quantity']) && $item['quantity'] > 0;
            }),
            'other_costs' => array_filter($request->other_costs ?? [], function ($item) {
                return isset($item['description']) && $item['description'] !== '';
            }),
        ]);
    
        // ✅ Validation
        $request->validate([
            'invoice_no' => 'required|unique:invoices',
            'contact_number' => 'required|digits:10',
            'customer_name' => 'required|string|max:255',
            'vehicle_number' => 'required|string|max:20',
            'grand_total' => 'required|numeric|min:0',
            'date' => 'required|date',
            'parts' => 'required|array|min:1',
            'parts.*.part_number' => 'required|string',
            'parts.*.part_name' => 'required|string',
            'parts.*.quantity' => 'required|numeric|min:1',
            'parts.*.unit_price' => 'required|numeric|min:0',
            'parts.*.discount' => 'nullable|numeric|min:0|max:100',
            'parts.*.total' => 'required|numeric|min:0',
            'other_costs' => 'nullable|array',
            'other_costs.*.description' => 'required|string',
            'other_costs.*.price' => 'required|numeric|min:0',
        ]);
    
        DB::beginTransaction();
    
        try {
            $existingCustomer = Customer::where('contact_number', $request->contact_number)->first();

            if (!$existingCustomer) {
                // Insert into customers table
                Customer::create([
                    'contact_number' => $request->contact_number,
                    'customer_name' => $request->customer_name,
                ]);
            }
    
        // Find the vehicle by vehicle number, regardless of current owner
            $existingVehicle = CustomerVehicle::where('vehicle_number', $request->vehicle_number)->first();

            if ($existingVehicle) {
            //  If this vehicle belongs to someone else, remove that old ownership
                if ($existingVehicle->contact_number !== $request->contact_number) {
                    $existingVehicle->delete();
                 }
             }

        // Register or re-register vehicle to the current customer
        CustomerVehicle::updateOrCreate(
        [
        'vehicle_number' => $request->vehicle_number,
        'contact_number' => $request->contact_number,
        ],
    
        );

    
            // 🔹 Create Invoice
            $invoice = Invoice::create([
                'invoice_no'     => $request->invoice_no,
                'customer_name'  => $request->customer_name,
                'contact_number' => $request->contact_number,
                'vehicle_number' => $request->vehicle_number,
                'grand_total'    => $request->grand_total,
                'date'           => $request->date,
            ]);
    
            // 🔹 Store sold parts
            foreach ($request->parts as $part) {
                SoldPart::create([
                    'invoice_no' => $invoice->invoice_no,
                    'part_number' => $part['part_number'],
                    'part_name' => $part['part_name'],
                    'quantity' => $part['quantity'],
                    'unit_price' => $part['unit_price'],
                    'discount' => $part['discount'] ?? 0,
                    'total' => $part['total'],
                ]);
            }
    
            // 🔹 Store other costs
            foreach ($request->other_costs ?? [] as $cost) {
                OtherCost::create([
                    'invoice_no' => $invoice->invoice_no,
                    'description' => $cost['description'],
                    'price' => $cost['price'],
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
     
    
    public function index(Request $request)
    {
        $query = Invoice::query();
    
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
        }
    
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
    
        $invoices = $query->orderByDesc('date')->get();
        
    
        return view('invoices.index', compact('invoices'));
    }

    public function autocomplete(Request $request)
{
    $term = $request->get('term');
    $results = Invoice::where('invoice_no', 'like', '%' . $term . '%')
                      ->limit(10)
                      ->pluck('invoice_no');

    return response()->json($results);
}
public function searchContacts(Request $request)
{
    $query = $request->query('q');

    $customers = Customer::where('contact_number', 'like', $query . '%')->get();

    $results = [];

    foreach ($customers as $customer) {
        $vehicle = CustomerVehicle::where('contact_number', $customer->contact_number)->first();

        $results[] = [
            'contact_number' => $customer->contact_number,
            'customer_name' => $customer->customer_name,
            'vehicle_number' => $vehicle?->vehicle_number ?? '',
        ];
    }

    return response()->json($results);
}

public function searchVehicle(Request $request)
{
    $query = $request->query('q');
    $vehicles = CustomerVehicle::where('vehicle_number', 'like', $query . '%')->get();

    $results = [];
    foreach ($vehicles as $vehicle) {
        $customer = Customer::where('contact_number', $vehicle->contact_number)->first();
        $results[] = [
            'vehicle_number' => $vehicle->vehicle_number,
            'contact_number' => $vehicle->contact_number,
            'customer_name' => $customer ? $customer->customer_name : '',
        ];
    }

    return response()->json($results);
}


public function otherCostSuggestions(Request $request)
{
    $q = $request->q;

    $suggestions = DB::table('other_costs')
        ->where('description', 'like', "%$q%")
        ->select('description', 'price')
        ->distinct()
        ->limit(10)
        ->get();

    return response()->json($suggestions);
}


   

    public function download($invoice_no)
{
    $invoice = \App\Models\Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();

    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
    return $pdf->download('Invoice_' . $invoice->invoice_no . '.pdf');
}

public function show($invoice_no)
{
    $invoice = Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();
    return view('invoices.show', compact('invoice'));
}
public function preview($invoice_no)
{
    $invoice = Invoice::with(['soldParts', 'otherCosts'])->where('invoice_no', $invoice_no)->firstOrFail();
    return view('invoices.preview', compact('invoice'));
}



    
}
